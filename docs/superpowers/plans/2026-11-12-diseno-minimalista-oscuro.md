# Diseño minimalista oscuro — Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Armonizar toda la interfaz de Minimercado El Ahorro con un tema oscuro minimalista basado en la paleta dada, mediante tokens Tailwind y componentes Blade reutilizables, aplicado a vistas existentes y preparado para las futuras.

**Architecture:** Extender Tailwind con tokens de color (bg/primary/accent/text + semánticos), definir CSS variables espejo, refactorizar layouts y crear componentes Blade nuevos con alias de compatibilidad para los de Breeze, y migrar vistas una por una. Sin cambio de stack.

**Tech Stack:** Laravel, Blade, Tailwind CSS, Vite, Alpine.js (Breeze), PHPUnit/Dusk.

**Spec:** `docs/superpowers/specs/2026-11-12-diseno-minimalista-oscuro-design.md`

## Global Constraints

- Paleta fija: primary-100 #1F3A5F, primary-200 #4d648d, primary-300 #acc2ef, accent-100 #3D5A80, accent-200 #cee8ff, text-100 #FFFFFF, text-200 #e0e0e0, bg-100 #0F1C2E, bg-200 #1f2b3e, bg-300 #374357.
- Colores semánticos (Tailwind estándar) solo para estados: green (success), amber (warning), red (danger), blue (info).
- Tema oscuro fijo; sin toggle.
- Idioma UI: español.
- No Bootstrap; stack intacto.
- Componentes Breeze se mantienen como alias compatibles hasta terminar migración.
- Vistas de paginación y error 403 tematizadas.
- Solo librería reutilizable para secciones "próximamente" (sin placeholders).

## Review Focus

- Contraste de texto secundario (text-200 #e0e0e0 sobre bg-200 #1f2b3e) en tablas y labels.
- Foco visible en inputs/botones sobre fondo oscuro.
- Estados hover/active de dropdowns del navbar.
- Badges de estado con semánticos legibles sobre bg-200.
- Paginación de Laravel legible en tema oscuro.

---

### Task 1: Tokens de color en Tailwind y CSS variables

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`

**Interfaces:**
- Produces: clases `bg-bg-*`, `text-text-*`, `bg-primary-*`, `bg-accent-*`, `border-bg-*`, etc.; variables CSS `--bg-100`... en `:root`.

- [ ] **Step 1: Escribir test que falle**

Test: `tests/Feature/DesignTokensTest.php` con método `test_tokens_present_in_compiled_css` que hace GET a `/` (o una ruta pública) y asserta que el HTML contiene `bg-[#0F1C2E]`? Mejor: test feature que renderiza dashboard autenticado y asserta `class="...bg-bg-100..."` no existe aún. Simplificar: test que verifica que `tailwind.config.js` contiene la clave `bg`. Como test de config, leer archivo.

```php
public function test_tailwind_config_defines_design_tokens(): void
{
    $config = file_get_contents(base_path('tailwind.config.js'));
    $this->assertStringContainsString("'bg'", $config);
    $this->assertStringContainsString("'#0F1C2E'", $config);
}
```

- [ ] **Step 2: Ejecutar test para verificar que falla**

Run: `php artisan test --filter=DesignTokensTest`
Expected: FAIL (archivo no existe o sin tokens).

- [ ] **Step 3: Implementar tokens en `tailwind.config.js`**

Extender `theme.extend.colors` con:
```js
colors: {
  bg: { 100: '#0F1C2E', 200: '#1f2b3e', 300: '#374357' },
  primary: { 100: '#1F3A5F', 200: '#4d648d', 300: '#acc2ef' },
  accent: { 100: '#3D5A80', 200: '#cee8ff' },
  text: { 100: '#FFFFFF', 200: '#e0e0e0' },
},
```
Y en `resources/css/app.css` agregar `:root` variables.

- [ ] **Step 4: Ejecutar test**

Run: `php artisan test --filter=DesignTokensTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add tailwind.config.js resources/css/app.css tests/Feature/DesignTokensTest.php
git commit -m "feat(design): add dark theme color tokens"
```

---

### Task 2: Componentes Blade nuevos + alias de compatibilidad

**Files:**
- Create: `resources/views/components/button-primary.blade.php`
- Create: `resources/views/components/button-secondary.blade.php`
- Create: `resources/views/components/button-ghost.blade.php`
- Create: `resources/views/components/button-danger.blade.php`
- Create: `resources/views/components/badge.blade.php`
- Create: `resources/views/components/card.blade.php`
- Create: `resources/views/components/alert.blade.php`
- Modify: `resources/views/components/primary-button.blade.php` (alias)
- Modify: `resources/views/components/secondary-button.blade.php` (alias)
- Modify: `resources/views/components/danger-button.blade.php` (alias)

**Interfaces:**
- Produces: `x-button-primary`, `x-button-secondary`, `x-button-ghost`, `x-button-danger`, `x-badge`, `x-card`, `x-alert`. Alias mantienen props existentes.

- [ ] **Step 1: Escribir test que falle**

`tests/Feature/ComponentsTest.php`: renderizar `<x-button-primary>Hola</x-button-primary>` via view y asserta contiene `bg-primary-100`.

- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar componentes** con clases de paleta. Alias delegan a los nuevos (`@include`/component).
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 3: Layouts oscuros (app, guest)

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/layouts/guest.blade.php`

**Interfaces:**
- Consumes: tokens bg/text.
- Produce: fondo bg-100, cards bg-200.

- [ ] **Step 1: Test feature** que autentica y GET dashboard asserta `bg-bg-100` en HTML.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar** clases oscuras en body/container/header.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 4: Navegación

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php`

**Interfaces:**
- Consumes: componentes dropdown existentes.

- [ ] **Step 1: Test** GET dashboard autenticado asserta `bg-bg-200` en nav.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar** navbar oscuro, dropdowns con hover bg-300, triggers consistentes.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 5: Dashboard

**Files:**
- Modify: `resources/views/dashboard.blade.php`

- [ ] **Step 1: Test** GET dashboard asserta presencia de `bg-bg-200` (cards).
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar** cards/badges/tablas con tokens y semánticos.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 6: Categorías (index, create, edit, form)

**Files:**
- Modify: `resources/views/categorias/*.blade.php`

- [ ] **Step 1: Test** GET categorias.index asserta `bg-bg-200`.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar**.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 7: Panel cliente + ventas + abonos

**Files:**
- Modify: `resources/views/cliente/panel.blade.php`
- Modify: `resources/views/cliente/ventas/*.blade.php`
- Modify: `resources/views/cliente/abonos/*.blade.php`
- Modify: `resources/views/cliente/catalogo/*.blade.php`

- [ ] **Step 1: Test** GET cliente.panel asserta `bg-bg-200`.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar**.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 8: Perfil

**Files:**
- Modify: `resources/views/profile/edit.blade.php`
- Modify: `resources/views/profile/partials/*.blade.php`

- [ ] **Step 1: Test** GET profile.edit asserta `bg-bg-200`.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar**.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 9: Auth views

**Files:**
- Modify: `resources/views/auth/*.blade.php`

- [ ] **Step 1: Test** GET login asserta `bg-bg-100`.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar**.
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 10: Paginación y error 403

**Files:**
- Create/Modify: `resources/views/vendor/pagination/*.blade.php` (publish)
- Modify: `resources/views/errors/403.blade.php`

- [ ] **Step 1: Test** GET ruta que pagina asserta clase oscura; GET 403 asserta `bg-bg-100`.
- [ ] **Step 2: Run → FAIL**
- [ ] **Step 3: Implementar** (publish pagination, tematizar).
- [ ] **Step 4: Run → PASS**
- [ ] **Step 5: Commit**

---

### Task 11: Tests Dusk (actualización)

**Files:**
- Modify: `tests/Browser/*.php` si selectores dependen de clases cambiadas.

- [ ] **Step 1: Ejecutar suite Dusk** para detectar fallos.
- [ ] **Step 2: Ajustar selectores/clases**.
- [ ] **Step 3: Ejecutar de nuevo → PASS**
- [ ] **Step 4: Commit**

---

### Task 12: Limpieza de alias (opcional, tras migración)

**Files:**
- Delete alias components una vez migradas todas las vistas.

- [ ] **Step 1: Verificar no quedan usos de aliases viejos** (grep).
- [ ] **Step 2: Eliminar archivos alias**.
- [ ] **Step 3: Run tests → PASS**
- [ ] **Step 4: Commit**
```

Voy a escribir este plan. Pero debo asegurar que los tests sean realistas. El plan es largo. Voy a escribirlo.<|end|><|start|><|start|>assistant to=write<|channel|>analysis <|constrain|>xml<|message|><rfl:parameter name="path">
C:\xampp\htdocs\MinimercadoElAhorro\docs\superpowers\plans\2026-11-12-diseno-minimalista-oscuro.md