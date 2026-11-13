# Rol Cliente – Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Agregar el rol `cliente` con portal de solo consulta (panel, catálogo, mis ventas, mis abonos) y vincular cuentas de usuario con clientes.

**Architecture:** Se extiende `users` con el rol `cliente`, se vincula a `clientes` mediante `user_id` nullable, y se crea un grupo de rutas protegido por `rol:cliente` con controladores y vistas de solo lectura. La navegación y la raíz se condicionan por rol.

**Tech Stack:** Laravel 11, Eloquent, Blade, Tailwind (Breeze), PHPUnit.

**Spec:** `docs/superpowers/specs/2026-11-12-rol-cliente-design.md`

## Global Constraints

- Roles válidos: `admin`, `empleado`, `cliente`.
- `clientes.user_id` nullable, FK a `users.id`, `onDelete('set null')`.
- El cliente solo consulta sus propios datos (ventas/abonos vinculados a su cliente).
- Sin registro de ventas, abonos, entradas, ajustes ni gestión.
- Mensajes y vistas en español.
- Pruebas con `RefreshDatabase`.

## Review Focus

- Un cliente inactivo no puede operar (middleware).
- Un cliente no puede ver ventas/abonos de otro cliente (IDOR).
- Un admin/empleado no puede entrar al portal cliente.
- La raíz redirige correctamente según rol.
- El catálogo no expone costo ni stock al cliente.

---

### Task 1: Modelo, migración y seeders

**Files:**
- Modify: `app/Models/User.php`
- Modify: `app/Models/Cliente.php`
- Create: `database/migrations/2026_11_12_100000_add_user_id_to_clientes_table.php`
- Modify: `database/seeders/ClienteSeeder.php`
- Modify: `database/factories/UserFactory.php`

**Interfaces:**
- `User::ROLE_CLIENTE = 'cliente'`
- `User::isCliente(): bool`
- `User::cliente(): HasOne`
- `Cliente::user(): BelongsTo`

- [ ] **Step 1: Write failing test** en `tests/Feature/ClientePortalTest.php` (se crea en Task 4, pero el enlace se valida aquí con prueba mínima).

- [ ] **Step 2: Implementar constantes y relaciones** en `User.php` y `Cliente.php`.

- [ ] **Step 3: Crear migración** `add_user_id_to_clientes_table`.

- [ ] **Step 4: Actualizar seeder** para crear cuenta por cliente.

- [ ] **Step 5: Actualizar factory** con estado `cliente()`.

- [ ] **Step 6: Ejecutar migraciones y seeders** (`php artisan migrate:fresh --seed`) y verificar login cliente.

- [ ] **Step 7: Commit** `feat: add cliente role and user-client link`.

---

### Task 2: Rutas y controladores del portal cliente

**Files:**
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/ClienteDashboardController.php`
- Create: `app/Http/Controllers/ClienteCatalogoController.php`
- Create: `app/Http/Controllers/ClienteVentasController.php`
- Create: `app/Http/Controllers/ClienteAbonosController.php`

**Interfaces:**
- `GET /cliente/panel` → `cliente.panel`
- `GET /cliente/catalogo` → `cliente.catalogo`
- `GET /cliente/ventas` → `cliente.ventas.index`
- `GET /cliente/ventas/{venta}` → `cliente.ventas.show`
- `GET /cliente/abonos` → `cliente.abonos.index`

- [ ] **Step 1: Escribir pruebas de acceso** (Task 4).

- [ ] **Step 2: Implementar controladores** con consultas filtradas por `cliente_id` del usuario autenticado.

- [ ] **Step 3: Registrar rutas** bajo `auth` + `rol:cliente`.

- [ ] **Step 4: Ajustar `/`** para redirigir por rol.

- [ ] **Step 5: Mover `/profile`** a grupo `auth` general.

- [ ] **Step 6: Commit** `feat: add cliente portal routes and controllers`.

---

### Task 3: Vistas y navegación

**Files:**
- Create: `resources/views/cliente/panel.blade.php`
- Create: `resources/views/cliente/catalogo/index.blade.php`
- Create: `resources/views/cliente/ventas/index.blade.php`
- Create: `resources/views/cliente/ventas/show.blade.php`
- Create: `resources/views/cliente/abonos/index.blade.php`
- Modify: `resources/views/layouts/navigation.blade.php`

- [ ] **Step 1: Crear vistas** reutilizando layout `app`.

- [ ] **Step 2: Ajustar navegación** por rol y etiqueta de rol.

- [ ] **Step 3: Verificar manualmente** login cliente y navegación.

- [ ] **Step 4: Commit** `feat: add cliente portal views`.

---

### Task 4: Pruebas

**Files:**
- Create: `tests/Feature/ClientePortalTest.php`

- [ ] **Step 1: Escribir pruebas**:
  - cliente accede a panel/catálogo/ventas/abonos (200)
  - cliente no accede a `/dashboard`, `/categorias` (403)
  - admin/empleado no acceden a `/cliente/panel` (403)
  - cliente solo ve sus ventas (no las de otro)
  - catálogo no muestra costo/stock

- [ ] **Step 2: Ejecutar** `vendor/bin/phpunit`.

- [ ] **Step 3: Commit** `test: cover cliente portal access and ownership`.

---

### Task 5: Documentación

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Actualizar matriz de roles** y alcance para incluir cliente.

- [ ] **Step 2: Commit** `docs: document cliente role`.
</content>