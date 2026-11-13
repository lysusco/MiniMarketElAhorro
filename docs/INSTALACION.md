# Guía de instalación – Minimercado El Ahorro

> Este archivo era el `README.md` original de la cáscara del proyecto. El README principal del
> repositorio contiene el documento de análisis y diseño; aquí queda la guía técnica de instalación.

Prototipo web desarrollado en **Laravel 12** bajo el patrón **Modelo-Vista-Controlador (MVC)** para controlar el
inventario, registrar ventas de contado y fiadas y gestionar el crédito de los clientes del Minimercado El Ahorro.

## Estado actual de los módulos

| Módulo | Estado | Descripción |
|---|---|---|
| Autenticación | ✅ Disponible | Inicio y cierre de sesión con Laravel Breeze (Blade + Tailwind). Sin registro público: las cuentas las gestiona el administrador (RF-03). |
| Roles y permisos | ✅ Disponible | Roles `admin` y `empleado` verificados en el servidor con el middleware `rol` (RF-02, RNF-03). |
| Base de datos | ✅ Disponible | 11 tablas del diagrama entidad-relación con migraciones, modelos Eloquent y relaciones. |
| Datos de prueba | ✅ Disponible | Seeders con usuarios, catálogo, proveedores, clientes y un flujo demo (compra → venta contado → venta fiada → abono). |
| Panel principal | ✅ Disponible | Indicadores: productos activos, stock bajo, ventas de hoy y cartera pendiente (RF-11). |
| Categorías | ✅ Disponible | CRUD completo con validación en español (RF-05). Patrón de referencia para los demás módulos. |
| Productos, proveedores, entradas y ajustes | ⏳ Próximamente | Persona A (módulo de inventario). |
| Ventas, clientes, abonos y reportes | ⏳ Próximamente | Persona B (ventas, clientes y seguridad). |

## Requisitos

- PHP 8.2 o superior (probado con XAMPP 8.2.12).
- MySQL / MariaDB (incluido en XAMPP).
- Composer 2.x.
- Node.js y npm (para compilar los estilos con Vite).

## Instalación

1. Clonar el repositorio en la carpeta `htdocs` de XAMPP:

   ```
   cd C:\xampp\htdocs
   git clone https://github.com/lysusco/MiniMarketElAhorro.git
   cd MiniMarketElAhorro
   ```

2. Instalar las dependencias de PHP:

   ```
   php C:\composer\composer.phar install
   ```

3. Crear el archivo de configuración y la clave de la aplicación:

   ```
   copy .env.example .env
   php artisan key:generate
   ```

4. Crear la base de datos `minimercado_el_ahorro` en MySQL (phpMyAdmin o consola) y verificar la configuración
   de conexión en `.env`:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=minimercado_el_ahorro
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Ejecutar las migraciones con los datos de prueba:

   ```
   php artisan migrate:fresh --seed
   ```

6. Compilar los recursos de interfaz:

   ```
   npm install
   npm run build
   ```

7. Abrir la aplicación:

   - Con Apache (XAMPP): `http://localhost/MiniMarketElAhorro/public`
   - O con el servidor de desarrollo: `php artisan serve` → `http://127.0.0.1:8000`

## Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@minimercado.com` | `admin123` |
| Empleado | `empleado@minimercado.com` | `empleado123` |

## Pruebas

El proyecto incluye dos niveles de pruebas:

**PHPUnit (integración, rápidas):** autenticación, roles y categorías.

```
php artisan test
```

**Laravel Dusk (navegador, end-to-end):** login, permisos y CRUD de categorías
ejecutándose en un navegador real (Chrome, en modo headless por defecto).

```
php artisan dusk
```

Requisitos para Dusk: Google Chrome instalado y MySQL en ejecución
(la aplicación se sirve automáticamente en `http://127.0.0.1:8000`).
Para ver el navegador durante la ejecución (útil para la sustentación),
ejecuta con la variable de entorno `DUSK_HEADLESS_DISABLED=true`.

Las pruebas se ejecutan sobre una base de datos SQLite en memoria (PHPUnit)
o sobre la base de datos de desarrollo con limpieza automática (Dusk),
y no afectan los datos de desarrollo.

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/          # Controladores (MVC)
│   ├── Middleware/
│   │   └── VerificarRol.php  # Autorización por rol (alias 'rol')
│   └── Requests/             # Form Requests con validación en español
└── Models/                   # Modelos Eloquent con relaciones
database/
├── migrations/               # 11 tablas del diagrama ER
├── factories/                # Fábricas para pruebas
└── seeders/                  # Datos de prueba y flujo demo
resources/views/              # Vistas Blade (Breeze + Tailwind)
routes/web.php                # Rutas principales del sistema
tests/Feature/                # Pruebas automatizadas
```

## Reglas de negocio implementadas hasta el momento

- El stock no se modifica directamente: cambia mediante entradas, ventas o ajustes y cada cambio genera un
  movimiento en `movimientos_inventario`.
- Las cuentas de usuario solo las gestiona el administrador; no existe registro público.
- Los permisos se verifican en el servidor (middleware `rol`); un acceso no autorizado responde HTTP 403.
- Los registros con operaciones históricas no se eliminan físicamente: se desactivan (regla 13).
- Las operaciones de venta y crédito se registran en transacciones (RNF-05); el flujo demo del seeder
  `DatosDemoSeeder` es la referencia de implementación.
