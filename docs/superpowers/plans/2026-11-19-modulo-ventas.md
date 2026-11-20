# Módulo de ventas — Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Cerrar el módulo de ventas del Minimercado El Ahorro: gestión de clientes, registro de ventas de contado y fiadas, registro de abonos y reportes de ventas y cartera (RF-12 a RF-20 del README).

**Architecture:** Se sigue el patrón MVC de Laravel ya establecido por el módulo de inventario: controladores + Form Requests + vistas Blade con componentes propios + rutas protegidas por el middleware `rol`. Las ventas se confirman directamente (sin estado borrador, según CU-05) y toda la operación —venta, detalles, descuento de stock, movimientos de inventario y saldo del cliente— ocurre en una sola transacción de base de datos.

**Tech Stack:** PHP, Laravel, Eloquent, Blade + Alpine.js (mismo patrón que `compras/create.blade.php`), Tailwind CSS, PHPUnit con SQLite en memoria.

**Spec:** `README.md` — secciones 3.2 (RF-12 a RF-20), 3.4 (reglas de negocio 2, 3, 5, 6, 7, 8, 9, 10, 12, 13), 3.6 (CU-05, CU-07), 4.4 (tablas `clientes`, `ventas`, `detalle_ventas`, `abonos`) y 3.1 (matriz de permisos).

## Global Constraints

- Mensajes de validación y error en español (RNF-07), estilo de los existentes en `StoreCompraRequest`.
- Validación en servidor mediante Form Requests (RNF-06).
- Toda venta o abono se registra en `DB::transaction`: se confirma todo o nada (RNF-05).
- Permisos verificados en servidor con `rol:admin,empleado` / `rol:admin`; acceso indebido responde HTTP 403 (RNF-03). Nunca basta con ocultar botones.
- Interfaz usable desde computador y celular (RNF-08); vistas construidas con los componentes existentes (`x-card`, `x-table`, `x-button-primary`, `x-modal`, etc.) siguiendo el estilo de las vistas de inventario.
- Código en MVC y convenciones de Laravel (RNF-09); comentarios y docblocks en español, igual que el código existente.
- Cantidades de ventas y abonos siempre mayores que cero (regla 2).
- `detalle_ventas.precio_unitario` guarda el precio histórico (`productos.precio_venta` al momento de la venta).
- Registros históricos: clientes y productos con operaciones no se eliminan físicamente; se desactivan (`activo = false`) (regla 13).
- Cada cambio de stock genera un registro en `movimientos_inventario` con tipo, cantidad, usuario, fecha y operación de origen (regla 12).
- Pruebas: `php artisan test --filter=<NombreDelTest>` desde la raíz del proyecto (SQLite en memoria, `RefreshDatabase`).

## Review Focus

1. **Dos ventas simultáneas sobre el último stock disponible** — se espera que nunca quede stock negativo: la validación de stock debe hacerse con `Producto::lockForUpdate()` dentro de la transacción, no solo al validar el formulario. (Task 2, paso de implementación + `test_se_rechaza_la_venta_si_no_hay_stock_suficiente`.)
2. **Venta fiada con el cliente justo en el límite de crédito** — `saldo + total == limite_credito` debe permitirse; un peso más debe rechazarse informando saldo y cupo disponible. (Task 2, `test_se_rechaza_la_venta_fiada_si_supera_el_limite_de_credito` y `test_la_venta_fiada_en_el_limite_exacto_es_permitida`.)
3. **Abonos acumulados sobre la misma venta** — el segundo abono debe calcular el saldo pendiente ya descontado por el primero y no permitir excederse; el estado debe pasar de `pendiente` → `parcial` → `pagada`. (Task 3, `test_abonos_acumulados_actualizan_saldo_y_estado`.)
4. **Empleado intentando fijar o modificar `limite_credito`** — debe rechazarse o ignorarse en el servidor aunque el campo se envíe manipulado. (Task 1, `test_empleado_no_puede_definir_ni_modificar_el_limite_de_credito`.)
5. **Líneas duplicadas o productos inactivos en los items de la venta** — dos líneas con el mismo producto deben sumar cantidades para validar stock; un producto `activo = false` no debe poder venderse. (Task 2, `test_las_cantidades_duplicadas_del_mismo_producto_se_suman` y `test_no_se_puede_vender_un_producto_inactivo`.)

---

## Estructura de archivos

| Archivo | Responsabilidad |
|---|---|
| `app/Http/Controllers/ClienteController.php` | CRUD de clientes (sin eliminación física). |
| `app/Http/Requests/StoreClienteRequest.php` / `UpdateClienteRequest.php` | Validación de datos de cliente. |
| `resources/views/clientes/{index,create,edit,form}.blade.php` | Vistas de clientes (mismo patrón que `productos/`). |
| `app/Models/Venta.php` (modificar) | Constantes de tipo/estado y `actualizarEstadoPago()`. |
| `app/Http/Controllers/VentaController.php` | Registro y consulta de ventas. |
| `app/Http/Requests/StoreVentaRequest.php` | Validación de la venta (items, tipo, cliente, descuento). |
| `resources/views/ventas/{index,create,show}.blade.php` | Vistas de ventas. |
| `app/Http/Controllers/AbonoController.php` | Registro de abonos a ventas fiadas. |
| `app/Http/Requests/StoreAbonoRequest.php` | Validación del abono. |
| `resources/views/abonos/create.blade.php` | Formulario de abono (flujo CU-07: buscar cliente, elegir venta fiada). |
| `app/Http/Controllers/ReporteController.php` | Reporte de ventas por fecha y reporte de cartera. |
| `resources/views/reportes/{ventas,cartera}.blade.php` | Vistas de reportes (solo admin). |
| `routes/web.php` (modificar) | Rutas nuevas en los grupos `rol:admin,empleado` y `rol:admin`. |
| `resources/views/layouts/sidebar.blade.php` (modificar) | Enlaces de navegación por rol. |

---

### Task 1: Módulo de clientes (RF-12, RF-13)

**Files:**
- Create: `app/Http/Controllers/ClienteController.php`
- Create: `app/Http/Requests/StoreClienteRequest.php`, `app/Http/Requests/UpdateClienteRequest.php`
- Create: `resources/views/clientes/index.blade.php`, `create.blade.php`, `edit.blade.php`, `form.blade.php`
- Modify: `routes/web.php` (grupo `rol:admin,empleado`), `resources/views/layouts/sidebar.blade.php`
- Test: `tests/Feature/ClientesTest.php`

**Interfaces:**
- Consume: modelo `Cliente` existente (`creditoDisponible(): float`, campos `nombre`, `identificacion`, `telefono`, `correo`, `direccion`, `limite_credito`, `saldo`, `activo`).
- Produces: rutas `clientes.index`, `clientes.create`, `clientes.store`, `clientes.edit`, `clientes.update`, `clientes.toggleActivo` (PATCH `/clientes/{cliente}/toggle-activo`). Regla de permiso aplicada en servidor: solo el admin puede enviar/modificar `limite_credito`; el empleado que lo envíe lo ve ignorado (en `store` queda en 0, en `update` conserva el valor anterior). Sin eliminación física: `toggleActivo` alterna `activo`. La vista `clientes/index` muestra por cliente: nombre, identificación, teléfono, saldo, crédito disponible y estado.

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/ClientesTest.php` (clase `ClientesTest extends TestCase`, `use RefreshDatabase`, helpers `admin()` y `empleado()` copiando el estilo de `tests/Feature/ComprasTest.php`: `User::factory()->create(['role' => User::ROLE_ADMIN, 'activo' => true])`). Métodos:

- `test_admin_puede_crear_cliente_con_limite_de_credito()` — POST `/clientes` con `['nombre' => 'Doña Rosa', 'identificacion' => 'CC-100', 'telefono' => '300', 'limite_credito' => 50000]`; aserciones:
  ```php
  ->assertRedirect(route('clientes.index'));
  $this->assertDatabaseHas('clientes', ['identificacion' => 'CC-100', 'limite_credito' => 50000, 'activo' => true]);
  ```
- `test_empleado_puede_crear_clientes()` — `actingAs($this->empleado())->post('/clientes', [... 'limite_credito' => 999999 ...])`; aserciones:
  ```php
  $this->assertDatabaseHas('clientes', ['identificacion' => 'CC-200', 'limite_credito' => 0]);
  ```
- `test_empleado_no_puede_definir_ni_modificar_el_limite_de_credito()` — el empleado hace PUT `/clientes/{id}` con `limite_credito` nuevo; aserción: `assertDatabaseHas('clientes', ['id' => ..., 'limite_credito' => 50000])` (conserva el anterior).
- `test_la_identificacion_del_cliente_es_unica()` — segundo POST con la misma identificación: `->assertSessionHasErrors('identificacion')`.
- `test_el_limite_de_credito_no_puede_ser_negativo()` — admin POST con `limite_credito => -1`: `->assertSessionHasErrors('limite_credito')`.
- `test_se_puede_desactivar_y_reactivar_un_cliente()` — PATCH `/clientes/{id}/toggle-activo` dos veces; aserciones: `assertDatabaseHas('clientes', ['id' => ..., 'activo' => false])` y luego `['activo' => true]`.
- `test_el_cliente_requiere_nombre()` — POST sin nombre: `->assertSessionHasErrors('nombre')`.

- [ ] **Step 2: Ejecutar el test para verificar que falla**

Run: `php artisan test --filter=ClientesTest`
Expected: FAIL — la clase `ClienteController` no existe (rutas no definidas, error 404 o excepción de ruta).

- [ ] **Step 3: Implementar el módulo**

- `ClienteController` (siguiendo el estilo de `ProductoController`):
  ```php
  public function index(Request $request): View              // busca por nombre o identificación (like), paginate(10)
  public function create(): View
  public function store(StoreClienteRequest $request): RedirectResponse
  public function edit(Cliente $cliente): View
  public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
  public function toggleActivo(Cliente $cliente): RedirectResponse
  ```
  En `store`/`update`: si `$request->user()->isAdmin()` se acepta `limite_credito`; si no, se ignora (0 en `store`, valor anterior en `update`).
- `StoreClienteRequest::rules()` (mensajes en español como `StoreCompraRequest`):
  ```php
  'nombre' => ['required', 'string', 'max:100'],
  'identificacion' => ['required', 'string', 'max:30', 'unique:clientes,identificacion'],
  'telefono' => ['nullable', 'string', 'max:30'],
  'correo' => ['nullable', 'email', 'max:100'],
  'direccion' => ['nullable', 'string', 'max:150'],
  'limite_credito' => ['nullable', 'numeric', 'min:0'],
  ```
  `UpdateClienteRequest` igual pero con `Rule::unique('clientes', 'identificacion')->ignore($this->cliente)`.
- Vistas `clientes/`: patrón de `productos/` (index con `x-table` y buscador, `form.blade.php` parcial compartido por create/edit, campo `limite_credito` visible solo con `@if(auth()->user()->isAdmin())`).
- Rutas en `routes/web.php`, dentro del grupo `rol:admin,empleado`:
  ```php
  Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
  Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
  Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
  Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
  Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
  Route::patch('/clientes/{cliente}/toggle-activo', [ClienteController::class, 'toggleActivo'])->name('clientes.toggleActivo');
  ```
- Sidebar: enlace "Clientes" (`route('clientes.index')`) en la sección admin/empleado, junto a "Entradas" y "Ajustes".

- [ ] **Step 4: Ejecutar el test para verificar que pasa**

Run: `php artisan test --filter=ClientesTest`
Expected: PASS (todos los métodos).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/ClienteController.php app/Http/Requests/StoreClienteRequest.php app/Http/Requests/UpdateClienteRequest.php resources/views/clientes routes/web.php resources/views/layouts/sidebar.blade.php tests/Feature/ClientesTest.php
git commit -m "feat(clientes): CRUD de clientes con límite de crédito por rol"
```

---

### Task 2: Registrar ventas de contado y fiadas (RF-14 a RF-18, RF-16)

**Files:**
- Modify: `app/Models/Venta.php` (constantes y `actualizarEstadoPago()`)
- Create: `app/Http/Controllers/VentaController.php`, `app/Http/Requests/StoreVentaRequest.php`
- Create: `resources/views/ventas/index.blade.php`, `create.blade.php`, `show.blade.php`
- Modify: `routes/web.php`, `resources/views/layouts/sidebar.blade.php`
- Test: `tests/Feature/VentasTest.php`

**Interfaces:**
- Consume: `Cliente` (Task 1, solo lectura: `activo`, `saldo`, `limite_credito`, `creditoDisponible()`), `Producto` (`activo`, `stock_actual`, `precio_venta`), `MovimientoInventario::TIPO_SALIDA`, `User::isAdmin()`.
- Produces:
  - Constantes en `Venta`: `TIPO_CONTADO = 'contado'`, `TIPO_FIADO = 'fiado'`, `ESTADO_PENDIENTE = 'pendiente'`, `ESTADO_PARCIAL = 'parcial'`, `ESTADO_PAGADA = 'pagada'`.
  - `Venta::actualizarEstadoPago(): void` — recalcula `estado` según `saldoPendiente()`: cero abonos → `pendiente`, abonos parciales → `parcial`, saldo pendiente 0 → `pagada`. (Lo consume la Task 3.)
  - Rutas `ventas.index`, `ventas.create`, `ventas.store`, `ventas.show` (admin y empleado).
  - Efectos confirmados juntos en la transacción: filas en `ventas` y `detalle_ventas`; `productos.stock_actual -= cantidad` (con `lockForUpdate`); fila en `movimientos_inventario` por producto con `tipo = 'salida'`, `venta_id`, `compra_id = null`, `observacion = "Salida por venta #{id}"`; y solo si es fiada: `clientes.saldo += total` y `estado = 'pendiente'`. Contado → `estado = 'pagada'`, `cliente_id = null` permitido.
  - Payload de `POST /ventas`: `fecha` (date), `tipo_venta` (`contado|fiado`), `cliente_id` (obligatorio si fiado), `descuento` (nullable, numérico, min 0), `items` (array min 1 de `['producto_id', 'cantidad']`).

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/VentasTest.php` (mismo estilo que `ComprasTest`; helper `productoConStock(int $stock): Producto` que crea categoría + producto con `precio_venta => 2000`). Métodos:

- `test_se_puede_registrar_una_venta_de_contado()` — crea producto con stock 10; POST `/ventas` con `tipo_venta => 'contado'`, un item de 3 unidades, `fecha => now()->toDateString()`; aserciones:
  ```php
  ->assertRedirect(route('ventas.index'));
  $this->assertDatabaseHas('ventas', ['tipo_venta' => 'contado', 'estado' => 'pagada', 'total' => 6000, 'subtotal' => 6000, 'cliente_id' => null]);
  $this->assertDatabaseHas('detalle_ventas', ['cantidad' => 3, 'precio_unitario' => 2000, 'subtotal' => 6000]);
  $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock_actual' => 7]);
  $this->assertDatabaseHas('movimientos_inventario', ['producto_id' => $producto->id, 'tipo' => 'salida', 'cantidad' => 3]);
  ```
- `test_se_rechaza_la_venta_si_no_hay_stock_suficiente()` — producto con stock 2, item de 5; aserciones: `->assertSessionHasErrors('items')` y `assertDatabaseCount('ventas', 0)` (no guarda nada parcialmente).
- `test_la_venta_fiada_aumenta_el_saldo_del_cliente()` — cliente con `limite_credito => 10000`, `saldo => 0`; POST `tipo_venta => 'fiado'`, `cliente_id`, total 4000; aserciones: `assertDatabaseHas('clientes', ['saldo' => 4000])` y `assertDatabaseHas('ventas', ['estado' => 'pendiente'])`.
- `test_se_rechaza_la_venta_fiada_si_supera_el_limite_de_credito()` — cliente con `saldo => 8000`, `limite_credito => 10000`; venta de 3000; aserciones: `->assertSessionHasErrors('cliente_id')`, `assertDatabaseCount('ventas', 0)`, `assertDatabaseHas('clientes', ['saldo' => 8000])` (sin cambios).
- `test_la_venta_fiada_en_el_limite_exacto_es_permitida()` — `saldo => 8000`, `limite_credito => 10000`, venta de 2000: se registra (saldo queda 10000).
- `test_la_venta_fiada_requiere_cliente_activo()` — cliente `activo => false`: `->assertSessionHasErrors('cliente_id')`; y fiado sin `cliente_id`: `->assertSessionHasErrors('cliente_id')`.
- `test_el_precio_unitario_conserva_el_precio_historico()` — registra venta, luego `Producto::find($id)->update(['precio_venta' => 9999])`; aserción: `assertDatabaseHas('detalle_ventas', ['precio_unitario' => 2000])`.
- `test_las_cantidades_duplicadas_del_mismo_producto_se_suman()` — dos items con el mismo producto (2 y 3) con stock 5: la venta se registra y el stock queda 0 (la validación de stock considera 5 en total).
- `test_no_se_puede_vender_un_producto_inactivo()` — producto `activo => false`: `->assertSessionHasErrors('items')`, `assertDatabaseCount('ventas', 0)`.
- `test_el_descuento_no_puede_superar_el_subtotal()` — `descuento` mayor que el subtotal: `->assertSessionHasErrors('descuento')`.
- `test_el_empleado_puede_registrar_ventas()` — `actingAs($this->empleado())->get('/ventas/create')->assertOk()`.

- [ ] **Step 2: Ejecutar el test para verificar que falla**

Run: `php artisan test --filter=VentasTest`
Expected: FAIL — `VentaController` no existe.

- [ ] **Step 3: Implementar**

- En `app/Models/Venta.php` agregar las constantes de `Interfaces` y:
  ```php
  public function actualizarEstadoPago(): void   // usa saldoPendiente() y abonos()->sum('monto')
  ```
- `VentaController`:
  ```php
  public function index(Request $request): View        // filtros fecha_desde, fecha_hasta, tipo_venta; paginate(10); orderByDesc fecha+id
  public function create(): View                        // productos activos y clientes activos (para el selector de fiado)
  public function store(StoreVentaRequest $request): RedirectResponse
  public function show(Venta $venta): View              // carga detalles.producto, abonos, cliente, user
  ```
  `store` ejecuta todo dentro de `DB::transaction`:
  1. Agregar cantidades de items repetidos por `producto_id` (mapa `producto_id => cantidad total`).
  2. Por cada producto: `Producto::lockForUpdate()->findOrFail($id)`; si `! activo` o `stock_actual < cantidad`, lanzar `ValidationException::withMessages(['items' => "Stock insuficiente para {nombre}: disponible {stock}, solicitado {cantidad}"])` (esto revierte la transacción; RF-15).
  3. Calcular `subtotal = Σ cantidad * producto.precio_venta`, `total = subtotal - descuento`. Si `descuento > subtotal`, lanzar `ValidationException::withMessages(['descuento' => 'El descuento no puede superar el subtotal.'])`.
  4. Si es fiada: validar cliente (`activo`, regla 5) y crédito (regla 6): `cliente.saldo + total > cliente.limite_credito` → `ValidationException::withMessages(['cliente_id' => "El cliente tiene saldo {$saldo} y cupo disponible {$disponible}; la venta de {$total} supera su límite de crédito."])`.
  5. Crear `Venta` y `DetalleVenta` (precio_unitario = `producto.precio_venta`, precio histórico), descontar stock, crear `MovimientoInventario` (TIPO_SALIDA, `venta_id`, `compra_id => null`, `fecha`, `user_id`, observacion `"Salida por venta #{id}"`), y si es fiada: `cliente.saldo += total`.
  6. `estado`: contado → `ESTADO_PAGADA`; fiado → `ESTADO_PENDIENTE`.
- `StoreVentaRequest::rules()` (mensajes en español):
  ```php
  'fecha' => ['required', 'date'],
  'tipo_venta' => ['required', 'in:contado,fiado'],
  'cliente_id' => ['required_if:tipo_venta,fiado', 'nullable', 'integer', 'exists:clientes,id'],
  'descuento' => ['nullable', 'numeric', 'min:0'],
  'items' => ['required', 'array', 'min:1'],
  'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
  'items.*.cantidad' => ['required', 'integer', 'min:1'],
  ```
- Vistas `ventas/` siguiendo el patrón de `compras/`: `create.blade.php` usa el mismo esquema Alpine (`ventaForm(@json($productos))`) con buscador de productos en modal, cantidad por línea, cálculo de subtotales/total en vivo, selector tipo de pago (contado/fiado) que muestra el selector de cliente solo para fiado, y campo descuento. `index.blade.php` con filtros de fecha y tipo. `show.blade.php` con detalles, estado de pago y abonos (si los hay). El botón "Registrar abono" **no** se agrega en esta task: lo agrega la Task 3 cuando la ruta `abonos.create` exista.
- Rutas en el grupo `rol:admin,empleado`:
  ```php
  Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
  Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create');
  Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
  Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
  ```
- Sidebar: enlaces "Ventas" y, más abajo, "Nueva venta" no es necesario: solo "Ventas".

- [ ] **Step 4: Ejecutar el test para verificar que pasa**

Run: `php artisan test --filter=VentasTest`
Expected: PASS. Verificar también que los tests existentes no se rompieron: `php artisan test --filter=ComprasTest` (PASS).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Venta.php app/Http/Controllers/VentaController.php app/Http/Requests/StoreVentaRequest.php resources/views/ventas routes/web.php resources/views/layouts/sidebar.blade.php tests/Feature/VentasTest.php
git commit -m "feat(ventas): registro de ventas de contado y fiadas con descuento de stock en transacción"
```

---

### Task 3: Registrar abonos a ventas fiadas (RF-19, reglas 8, 9, 10)

**Files:**
- Create: `app/Http/Controllers/AbonoController.php`, `app/Http/Requests/StoreAbonoRequest.php`
- Create: `resources/views/abonos/create.blade.php`
- Modify: `routes/web.php`, `resources/views/layouts/sidebar.blade.php`, `resources/views/ventas/show.blade.php` (botón "Registrar abono")
- Test: `tests/Feature/AbonosTest.php`

**Interfaces:**
- Consume: `Venta::saldoPendiente()`, `Venta::actualizarEstadoPago()` (Task 2), `Cliente.saldo`, constantes `Venta::ESTADO_*` y `Venta::TIPO_FIADO`, ruta `ventas.show`.
- Produces: rutas `abonos.create` (GET `/abonos/create`, acepta query `?cliente={id}` y `?venta={id}` para precargar) y `abonos.store` (POST `/abonos`, campos `venta_id`, `fecha`, `monto`, `metodo_pago` (`efectivo|transferencia|otro`), `observacion` nullable). Efectos confirmados en transacción: fila en `abonos`, `clientes.saldo -= monto`, `venta->actualizarEstadoPago()`.

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/AbonosTest.php` con un helper `ventaFiada(float $total, float $saldoCliente = 0): Venta` que cree cliente (`limite_credito` amplio), producto con stock y una venta fiada usando el `VentaController` vía POST `/ventas` (o creando las filas directamente con los modelos y llamando a `actualizarEstadoPago()`). Métodos:

- `test_se_puede_registrar_un_abono_en_una_venta_fiada()` — venta fiada de 10000, abono de 4000 (`metodo_pago => 'efectivo'`); aserciones:
  ```php
  ->assertRedirect(); // hacia la venta o el formulario
  $this->assertDatabaseHas('abonos', ['monto' => 4000, 'metodo_pago' => 'efectivo']);
  $this->assertDatabaseHas('ventas', ['id' => $venta->id, 'estado' => 'parcial']);
  $this->assertDatabaseHas('clientes', ['id' => $venta->cliente_id, 'saldo' => 6000]);
  ```
- `test_el_abono_total_marca_la_venta_como_pagada()` — abono por 10000 sobre venta de 10000: `assertDatabaseHas('ventas', ['estado' => 'pagada'])` y `assertDatabaseHas('clientes', ['saldo' => 0])`.
- `test_no_se_puede_abonar_mas_del_saldo_pendiente()` — abono de 11000 sobre venta de 10000: `->assertSessionHasErrors('monto')`, `assertDatabaseCount('abonos', 0)`, cliente sin cambios.
- `test_no_se_puede_abonar_una_venta_de_contado()` — venta de contado: `->assertSessionHasErrors('venta_id')`, `assertDatabaseCount('abonos', 0)`.
- `test_no_se_puede_abonar_una_venta_ya_pagada()` — venta fiada ya saldada con abono previo: `->assertSessionHasErrors('venta_id')`.
- `test_el_monto_del_abono_debe_ser_mayor_que_cero()` — monto 0 y monto negativo: `->assertSessionHasErrors('monto')`.
- `test_abonos_acumulados_actualizan_saldo_y_estado()` — venta de 10000: abono 6000 (estado `parcial`, saldo 4000), segundo abono 4000 (estado `pagada`, saldo 0); aserciones después de cada POST.
- `test_el_empleado_puede_registrar_abonos()` — `actingAs($this->empleado())->get('/abonos/create')->assertOk()`.

- [ ] **Step 2: Ejecutar el test para verificar que falla**

Run: `php artisan test --filter=AbonosTest`
Expected: FAIL — `AbonoController` no existe.

- [ ] **Step 3: Implementar**

- `AbonoController`:
  ```php
  public function create(Request $request): View
      // Si llega ?venta={id}, precargar esa venta (validar que sea fiada y tenga saldo pendiente).
      // Si llega ?cliente={id}, listar las ventas fiadas con saldo pendiente de ese cliente (flujo CU-07).
      // Sin parámetros: listar clientes con saldo > 0 para buscar (campo de búsqueda por nombre/identificación).
  public function store(StoreAbonoRequest $request): RedirectResponse
      // DB::transaction: crear Abono (user_id = auth, fecha, monto, metodo_pago, observacion),
      // cliente.saldo -= monto, venta->actualizarEstadoPago().
      // Reglas 8 y 9 verificadas con lockForUpdate sobre la venta dentro de la transacción:
      //   - venta debe ser fiada y saldoPendiente() > 0, si no: ValidationException con ['venta_id' => ...]
      //   - monto <= saldoPendiente(), si no: ValidationException con ['monto' => "El monto supera el saldo pendiente de la venta ({$saldo})."]
  ```
- `StoreAbonoRequest::rules()` (mensajes en español):
  ```php
  'venta_id' => ['required', 'integer', 'exists:ventas,id'],
  'fecha' => ['required', 'date'],
  'monto' => ['required', 'numeric', 'gt:0'],
  'metodo_pago' => ['required', 'in:efectivo,transferencia,otro'],
  'observacion' => ['nullable', 'string', 'max:200'],
  ```
- Vista `abonos/create.blade.php`: patrón de formularios del proyecto; muestra resumen de la venta (total, abonado, saldo pendiente), el historial de abonos y el formulario; el buscador de cliente/venta reutiliza el patrón de modal de `compras/create.blade.php`.
- En `ventas/show.blade.php`: botón "Registrar abono" (`route('abonos.create', ['venta' => $venta->id])`) visible solo si `$venta->esFiada() && $venta->saldoPendiente() > 0`.
- Rutas en el grupo `rol:admin,empleado`:
  ```php
  Route::get('/abonos/create', [AbonoController::class, 'create'])->name('abonos.create');
  Route::post('/abonos', [AbonoController::class, 'store'])->name('abonos.store');
  ```
- Sidebar: enlace "Abonos" (`route('abonos.create')`) en la sección admin/empleado.

- [ ] **Step 4: Ejecutar el test para verificar que pasa**

Run: `php artisan test --filter=AbonosTest`
Expected: PASS. Verificar regresión: `php artisan test --filter=VentasTest` (PASS).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AbonoController.php app/Http/Requests/StoreAbonoRequest.php resources/views/abonos resources/views/ventas/show.blade.php routes/web.php resources/views/layouts/sidebar.blade.php tests/Feature/AbonosTest.php
git commit -m "feat(abonos): registro de abonos a ventas fiadas con actualización de saldo y estado"
```

---

### Task 4: Reportes de ventas y cartera (RF-20)

**Files:**
- Create: `app/Http/Controllers/ReporteController.php`
- Create: `resources/views/reportes/ventas.blade.php`, `resources/views/reportes/cartera.blade.php`
- Modify: `routes/web.php` (grupo `rol:admin`), `resources/views/layouts/sidebar.blade.php`
- Test: `tests/Feature/ReportesTest.php`

**Interfaces:**
- Consume: `Venta` (con `cliente`, `user`, `detalles`), `Cliente` (`saldo`, `limite_credito`, `creditoDisponible()`), constantes `Venta::ESTADO_PENDIENTE`, `ESTADO_PARCIAL`, `TIPO_FIADO`.
- Produces: rutas `reportes.ventas` (GET `/reportes/ventas?fecha_desde=&fecha_hasta=`) y `reportes.cartera` (GET `/reportes/cartera`), solo `rol:admin`. `ReporteController` con métodos `ventas(Request $request): View` y `cartera(): View`.

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/ReportesTest.php` (helpers: admin, empleado, y creación directa de una venta fiada y una de contado con modelos). Métodos:

- `test_el_admin_ve_el_reporte_de_ventas_por_fecha()` — crea una venta hoy y una con fecha anterior; GET `/reportes/ventas?fecha_desde={hoy}&fecha_hasta={hoy}`; aserciones: `->assertOk()`, `->assertSee('Venta #')` (solo la de hoy) y el total del día visible; sin filtros muestra ambas.
- `test_el_reporte_de_cartera_lista_solo_clientes_con_saldo_pendiente()` — cliente con `saldo => 5000` y cliente con `saldo => 0`; GET `/reportes/cartera`; aserciones: `->assertSee('5000')` y `->assertDontSee` el nombre del cliente sin saldo.
- `test_cartera_muestra_el_credito_disponible()` — cliente con `limite_credito => 10000`, `saldo => 4000`; aserción: `->assertSee('6000')` (crédito disponible).
- `test_el_empleado_no_puede_ver_los_reportes()` — `actingAs($this->empleado())->get('/reportes/ventas')->assertStatus(403)` e igual para `/reportes/cartera`.
- `test_el_reporte_de_ventas_muestra_los_totales()` — venta de contado de 3000 y fiada de 2000 hoy; aserción: `->assertSee('5000')` (total del rango).

- [ ] **Step 2: Ejecutar el test para verificar que falla**

Run: `php artisan test --filter=ReportesTest`
Expected: FAIL — `ReporteController` no existe (404).

- [ ] **Step 3: Implementar**

- `ReporteController`:
  ```php
  public function ventas(Request $request): View
      // Valida fecha_desde/fecha_hasta opcionales (date). Filtra por rango sobre 'fecha',
      // con cliente y user cargados, orderByDesc('fecha')->orderByDesc('id'), paginate(20).
      // Calcula totales del rango: suma de 'total', cantidad de ventas, suma contado, suma fiado.
  public function cartera(): View
      // Cliente::where('saldo', '>', 0)->orderByDesc('saldo')->get()
      // con columnas: nombre, identificación, teléfono, límite, saldo, crédito disponible,
      // y cantidad de ventas fiadas con saldo pendiente.
  ```
- Vistas `reportes/` con `x-card`, `x-table`, formulario de filtro de fechas en `ventas.blade.php` (mismo estilo de formularios GET del proyecto).
- Rutas dentro del grupo `rol:admin` (junto a `categorias`/`proveedores`):
  ```php
  Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
  Route::get('/reportes/cartera', [ReporteController::class, 'cartera'])->name('reportes.cartera');
  ```
- Sidebar: enlace "Reportes" con submenú o dos enlaces ("Ventas", "Cartera") visibles solo para admin (misma condición `@if` que oculta "Categorías" al empleado).

- [ ] **Step 4: Ejecutar el test para verificar que pasa**

Run: `php artisan test --filter=ReportesTest`
Expected: PASS. Regresión completa: `php artisan test` (PASS en todos los suites del proyecto).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/ReporteController.php resources/views/reportes routes/web.php resources/views/layouts/sidebar.blade.php tests/Feature/ReportesTest.php
git commit -m "feat(reportes): reporte de ventas por fecha y cartera de clientes con saldo pendiente"
```

---

## Decisiones del plan (para revisión)

Estas decisiones no están explícitas en el README; se tomaron aquí y se marcan como propias del builder:

1. **Venta sin estado borrador** — a diferencia de las compras, la venta se confirma de inmediato (CU-05 describe confirmación directa). El campo `ventas.estado` se usa para el estado de pago (`pendiente|parcial|pagada`), según el comentario de la migración.
2. **Identificación de cliente única** — regla no escrita en el README; se impone `unique` para evitar clientes duplicados al buscar por cédula.
3. **`limite_credito` ignorado (no 403) cuando lo envía un empleado** — la matriz prohíbe al empleado definirlo; se ignora el valor en servidor y el campo no se muestra en el formulario. Un 403 explícito sería también aceptable.
4. **Abono con fecha editable** (por defecto hoy), igual que las compras, por si se registra un cobro atrasado.
5. **Reportes solo del administrador** según la matriz de permisos (la de la sección 3.1); el empleado no accede aunque RF-20 no lo precise.
6. **Campo `descuento` incluido en el formulario de venta** porque existe en el esquema de la tabla `ventas`; validado `>= 0` y `<= subtotal`.
