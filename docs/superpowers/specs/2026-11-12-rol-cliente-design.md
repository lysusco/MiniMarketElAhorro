# Rol Cliente – Diseño

**Fecha:** 2026-11-12
**Proyecto:** Minimercado El Ahorro

## Intención

Agregar un tercer rol de usuario, `cliente`, que inicie sesión en el mismo aplicativo pero con acceso únicamente de consulta: su panel, catálogo de productos, sus ventas y sus abonos. No registra ventas, abonos, entradas, ajustes ni gestiona usuarios, precios, stock ni límites de crédito.

## Alcance

Incluye:
- Rol `cliente` en `users`.
- Vinculación cuenta ↔ cliente mediante `clientes.user_id`.
- Portal de solo lectura para el cliente.
- Navegación condicionada por rol.
- Seeders con cuentas de cliente de demostración.

No incluye:
- Registro de ventas/abonos/pedidos por el cliente.
- Modificación de datos del negocio por el cliente.
- Pasarelas de pago o comercio electrónico.

## Decisiones

- `user_id` nullable en `clientes` (un cliente puede existir sin cuenta).
- Relaciones: `User::cliente()` (hasOne) y `Cliente::user()` (belongsTo).
- Rutas bajo `auth` + `rol:cliente`.
- `/profile` disponible para todos los roles autenticados.
- `/` redirige según rol.
- Contraseña de demostración para clientes: `cliente123`.
</content>