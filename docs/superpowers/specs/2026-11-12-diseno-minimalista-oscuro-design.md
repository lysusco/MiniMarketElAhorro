# Diseño: Interfaz minimalista oscuro para Minimercado El Ahorro

## Intención y por qué

La interfaz actual (Laravel Breeze + Tailwind) es funcional pero inconsistente:
botones con estilos ad hoc, dropdowns con triggers distintos, badges con paletas
variadas y secciones "próximamente" que rompen el ritmo visual. Se busca
armonizar toda la app con un estilo minimalista de tema oscuro fijo, usando la
paleta proporcionada por el usuario, sin cambiar el stack (Tailwind + Blade).

## Objetivos

- Aplicar tema oscuro fijo a todas las vistas existentes (dashboard, categorías,
  panel/ventas/abonos del cliente, perfil, auth).
- Definir un sistema de tokens (colores, tipografía, espaciado, radios, sombras)
  basado en la paleta dada.
- Crear componentes Blade reutilizables (botón primario/secundario/ghost, badge,
  card, tabla, alerta, input, dropdown) que reemplacen estilos ad hoc.
- Dejar componentes preparados para las secciones aún "próximamente"
  (productos, proveedores, ventas, clientes, reportes, seguridad).
- Mantener accesibilidad básica (contraste, foco visible).

## No-objetivos

- No introducir Bootstrap ni otro framework CSS.
- No implementar toggle claro/oscuro por ahora (solo oscuro fijo).
- No rediseñar lógica de negocio ni rutas.
- No cambiar el stack (seguimos con Tailwind + Vite + Blade).
- No crear vistas placeholder para secciones "próximamente"; solo librería reutilizable.

## Restricciones

- Stack: Laravel + Breeze + Tailwind CSS + Vite + Blade components.
- Idioma de la UI: español.
- Paleta fija proporcionada por el usuario (ver tokens).
- Componentes existentes de Breeze (x-app-layout, x-nav-link, etc.) se
  refactorizan, no se eliminan abruptamente.

## Tokens de diseño

Colores (CSS variables):

- --primary-100: #1F3A5F (botones primarios, acento principal)
- --primary-200: #4d648d
- --primary-300: #acc2ef (texto sobre oscuro, enlaces activos)
- --accent-100: #3D5A80
- --accent-200: #cee8ff (alertas de atención/stock bajo)
- --text-100: #FFFFFF
- --text-200: #e0e0e0 (texto secundario)
- --bg-100: #0F1C2E (fondo de página)
- --bg-200: #1f2b3e (cards, navbar, tablas)
- --bg-300: #374357 (bordes, divisores, hover)

Tipografía: Figtree (400/500/600), heredada de Breeze.

Colores semánticos (Tailwind estándar, solo para estados):
- success: verde (ej. green-500/600)
- warning: ámbar (ej. amber-500/600)
- danger: rojo (ej. red-500/600)
- info: azul (ej. blue-500/600)

Se usan únicamente en badges, alertas, botones de eliminar y indicadores de estado.
El resto de la UI usa la paleta fija.

Espaciado: escala Tailwind por defecto (4/8/12/16/24/32/48).

Radios: sm 6px, md 8px, lg 10px, full 999px (badges).

Sombras: mínimas; preferir bordes de 1px sobre sombras pesadas.

## Componentes a crear/refactorizar

Blade components en resources/views/components/:

- x-button-primary, x-button-secondary, x-button-ghost, x-button-danger
- x-badge (variantes: success, warning, info, neutral)
- x-card (con slot header/body)
- x-table (wrapper con estilos base)
- x-alert (success, error, info)
- x-input, x-select, x-textarea (estilo oscuro)
- x-dropdown (refactor del de Breeze)
- x-nav-link (refactor)
- x-modal (refactor)

Layouts:

- x-app-layout: fondo bg-100, navbar bg-200, cards bg-200 con borde bg-300.
- x-guest-layout: mismo tema para auth.

## Aplicación a vistas existentes

- Dashboard: cards de indicadores, tablas de últimas ventas y stock bajo.
- Categorías: tabla + búsqueda + botones.
- Panel cliente: cards de crédito, tablas de ventas/abonos.
- Perfil y auth: formularios con inputs oscuros.
- Navegación: dropdowns con triggers consistentes, hover bg-300.

## Migración

1. Definir tokens en tailwind.config.js (extend colors) y/o CSS variables en app.css.
2. Refactorizar layouts y componentes base.
3. Tematizar vistas de paginación (Laravel pagination) y página de error 403.
4. Migrar vistas una por una, priorizando dashboard y categorías.
5. Actualizar tests de navegador (Dusk) si cambian selectores/clases.

## Compatibilidad incremental (decisión del builder)

Se mantendrán los componentes Breeze existentes (`x-primary-button`, `x-danger-button`,
`x-text-input`, etc.) como alias compatibles durante la migración, delegando a los
nuevos componentes. Una vez migradas todas las vistas, se eliminarán los alias.
Esto minimiza el riesgo de romper vistas no migradas.

## Lo que queda al builder

- Decisiones finas de contraste y espaciado.
- Nombres exactos de componentes y props.
- Orden de migración de vistas.
- Tests de regresión visual (opcional).

## Aprobación

Pendiente de aprobación del usuario antes de planificar implementación.