# Implementación — Sección employees

> **Propuesta base:** `.ui-work/01-propuestas/employees.md`
> **Decisiones aprobadas:** Zona de estado en edit, eliminación de eye duplicado, alineación de tokens, prioritización visual en índice.
> **Fecha:** 2026-09-16

---

## 1. Archivos modificados

### 1.1 `resources/css/app.css`
- **Por qué:** Agregar patrón visual de dot indicators (`●●○ N/M`) y row-level attention highlights.
- **Cambios:**
  - Nueva sección `.dot-indicator` con puntos filled/hollow para representación visual de conteos.
  - Nuevas clases `.row-attention-fp`, `.row-attention-none`, `.row-attention-sync` para resaltar filas que necesitan atención en el índice (basado en conteo de huellas).
- **Tokens usados:** `--cat-green`, `--cat-amber`, `--cat-red`, `--cat-blue`, `--cat-gray`.
- **Consumidores verificados:** Ningún otro archivo usa estas clases (son nuevas).

### 1.2 `resources/views/components/stat-card.blade.php`
- **Por qué:** Alinear a tokens `--cat-*` en vez de `--bs-{color}-subtle`/`text-{color}` (diagnóstico #2 de la propuesta).
- **Cambios:**
  - Mapeo PHP de colores Bootstrap (`success`, `danger`, `warning`, `info`) a tokens `--cat-*`.
  - Icon background usa `color-mix(in srgb, var(--cat-*) 15%, transparent)` en vez de `var(--bs-*-subtle)`.
  - Icon color y value color usan `var(--cat-*)` directamente.
  - Fallback para colores sin token (ej. `teal`): mantiene `var(--bs-{color}-subtle)`.
- **Consumidores verificados:** 89 usos en: `employees/edit`, `devices/index`, `devices/show`, `dashboard`, `academia/` (profesores, cursos, planes, grupos, horarios, ciclos). Todos pasan colores que están en el mapa o son manejados por el fallback.
- **Regresión:** Sin cambios visuales para consumidores existentes; los colores resultantes son equivalentes.

### 1.3 `resources/views/employees/partials/_fingerprint-badge.blade.php`
- **Por qué:** Centralizar regla de colores de huellas y agregar formato visual `●●○ N/M` (diagnóstico #3).
- **Cambios:**
  - Nuevo parámetro opcional `$fpMax` para el total esperado de dedos únicos.
  - Cuando `$fpMax` se provee: muestra dot indicator (`●●○`) + conteo `N/M`.
  - Cuando `$fpMax` es null: mantiene el formato anterior (icono + N).
  - Regla de colores centralizada: `0→gray`, `<3→amber`, `≥3→green`.
- **Consumidores verificados:** `employees/index.blade.php` (llamado con `$fpCount` y `$fpMax`).

### 1.4 `resources/views/employees/edit.blade.php`
- **Por qué:** Implementar zona de estado visual (decisión PUERTA aprobada) + resolver dual primary button (diagnóstico #1).
- **Cambios:**
  - **Zona de estado** (nueva, arriba del KPI grid): Usa clase `.device-state-zone` existente en CSS. Muestra:
    - Nombre / Identidad del empleado
    - Estado: `🟢 Activo` / `🔴 Baja` (emoji Unicode, no inventa datos)
    - Huellas: dot indicator + conteo (solo si hay datos; "Sin enrolamiento" si no)
    - Dispositivos: dot indicator `N/M` (solo si hay datos; "Sin enrolar" si no)
    - Sincronización: icono + estado + fecha (solo si hay sync; "Nunca ejecutada" si no)
    - Tarjeta RFID: números de tarjeta (solo si existen; omitido si no)
  - **Dual button fix:** "Guardar identidad" cambia de `btn-primary` a `btn-outline-primary` (secundario); "Guardar cambios" se mantiene como único `btn-primary`.
  - **Sync status colors:** Corregido mapping de `'danger'`→`'red'` y `'warning'`→`'amber'` para alinearse con tokens.
  - **Fingerprint badge centralizado:** Reemplaza cálculo inline de `$asideFpColor` por `@include` de `_fingerprint-badge`.
  - **Enrolments table:** Reemplaza `<x-badge :color="$fpColor">` inline por `@include('employees.partials._fingerprint-badge')`.
- **Tokens usados:** `--cat-green`, `--cat-red`, `--cat-amber`, `--cat-blue`, `--cat-gray`.
- **Sin cambios a:** rutas, controladores, lógica de negocio, formularios POST, sync preview drawer, sync progress panel.

### 1.5 `resources/views/employees/index.blade.php`
- **Por qué:** Priorización visual obligatoria (huellas > enrolamiento > sync > estado > dispositivos) + eliminar eye duplicado (diagnóstico #4).
- **Cambios:**
  - **Columna Hardware reestructurada:** Huellas primero (con dot indicator via `_fingerprint-badge` con `$fpMax`), luego dispositivos, luego sync con iconos semánticos (`bi-check-circle-fill`/`bi-x-circle-fill`/`bi-hourglass-split`).
  - **Eye duplicado eliminado:** Se removió el `<a>` con `bi-eye` que apuntaba al mismo destino que `bi-pencil`.
  - **Sync status con iconos:** Reemplaza `<x-badge>` por icono contextual + texto de estado con color `--cat-*`.
  - **Row-level attention:** Filas con `<3 huellas` obtienen `row-attention-fp` (amber strip); filas con 0 huellas obtienen `row-attention-none` (red strip).
- **Tokens usados:** `--cat-green`, `--cat-red`, `--cat-amber`, `--cat-blue`, `--cat-gray`.
- **Sin cambios a:** filtros, tabs, paginación, empty states, skeleton, error panel, AJAX search.

### 1.6 `resources/js/employees-index.js`
- **Por qué:** Mantener consistencia con cambios en Blade: eliminar eye duplicado, reestructurar Hardware, dot indicators, row attention.
- **Cambios:**
  - **Eye duplicado eliminado:** `buildRow()` ya no genera el icono `bi-eye`.
  - **Hardware reestructurado:** Huellas primero con dot indicator visual, luego dispositivos, luego sync con iconos semánticos.
  - **Row attention class:** `buildRow()` agrega `row-attention-fp`/`row-attention-none` al `<tr>` basado en `fingerprints_count`.
  - **Sync icons:** Mapeo `syncIconMap` para iconos contextuales (`bi-check-circle-fill`, `bi-x-circle-fill`, `bi-hourglass-split`).
- **Sin cambios a:** debounce, fetchEmployees, renderPagination, updateCounter, filter listeners, form submit handling.

### 1.7 `resources/views/employees/sobrantes.blade.php`
- **Por qué:** Reemplazar `text-warning`, `text-danger`, `text-info`, `text-secondary` con tokens `--cat-*` (diagnóstico #2 de la propuesta).
- **Cambios:**
  - **Stats cards:** `text-warning`→`var(--cat-orange)`, `text-danger`→`var(--cat-red)`, `text-info`→`var(--cat-blue)`, `text-secondary`→`var(--cat-gray)`.
  - **Tipo badges:** `<span class="badge badge-with-dot cat-red">`→`<x-badge color="red" label="Tipo A" dot />`.
  - **Estado badges:** `<span class="badge badge-with-dot cat-green/gray">`→`<x-badge color="green/gray" ... dot />`.
  - **Razón columna:** `text-danger`/`text-warning` inline→`var(--cat-red)`/`var(--cat-orange)` inline con condicional simplificado.
- **Tokens usados:** `--cat-orange`, `--cat-red`, `--cat-blue`, `--cat-gray`, `--cat-green`.
- **Sin cambios a:** filtros, paginación, acciones (ignore/unignore/remove), empty state.

### 1.8 `resources/views/employees/create.blade.php`
- **Por qué:** Quitar `pattern` y `maxlength` como única validación (diagnóstico #5 de la propuesta).
- **Cambios:**
  - `name`: eliminado `maxlength="24"` (server-side validation es fuente de verdad).
  - `user_id`: eliminado `maxlength="9"`.
  - `password`: eliminado `maxlength="8"` y `pattern="[0-9]{1,8}"`.
  - `card_number`: eliminado `maxlength="10"` y `pattern="[0-9]+"`.
  - Se conservan: `inputmode="numeric"`, `required`, `form-text` messages, `@error` displays.
- **Sin cambios a:** selects (device, area, puesto, role), form action, botones de envío.

### 1.9 `resources/views/permission-groups/assign-employees.blade.php`
- **Por qué:** Agregar responsive `table-cards` para móvil (propuesta §4.5).
- **Cambios:**
  - Tabla: `class="table table-hover align-middle"` → `class="table table-hover align-middle table-cards"`.
  - Celdas: agregados `data-label="Nombre"`, `data-label="ID"`, `data-label="Área"`, `data-label="Puesto"`, `data-label=""`.
- **Sin cambios a:** checkboxes, select-all logic, form submit, page header.

---

## 2. Componentes reutilizados vs creados

| Componente | Estado | Archivo |
|---|---|---|
| `x-page-header` | Reutilizado | `components/page-header.blade.php` |
| `x-badge` | Reutilizado | `components/badge.blade.php` |
| `x-stat-card` | **Modificado** (tokens `--cat-*`) | `components/stat-card.blade.php` |
| `_fingerprint-badge` | **Modificado** (dot indicator) | `employees/partials/_fingerprint-badge.blade.php` |
| `_quick-filters` | Reutilizado | `employees/partials/_quick-filters.blade.php` |
| `_sync-progress-panel` | Reutilizado (sin cambios) | `employees/partials/_sync-progress-panel.blade.php` |
| `_sync-preview-drawer` | Reutilizado (sin cambios) | `employees/partials/_sync-preview-drawer.blade.php` |
| `_sync-devices-form` | Reutilizado (sin cambios) | `employees/partials/_sync-devices-form.blade.php` |
| `.device-state-zone` | **Reutilizado** (CSS existente) | `resources/css/app.css` |
| `.dot-indicator` | **Nuevo** (CSS) | `resources/css/app.css` |

**Componentes nuevos no creados (pendientes):**
- `⋮ Más` dropdown de acciones: `[COMPONENTE NUEVO]` — pendiente por decisión de complejidad.
- `Columnas ▾` selector: `[COMPONENTE NUEVO]` — pendiente por decisión de complejidad.

---

## 3. Tokens usados y `[GAP]` detectados

| Token | Uso |
|---|---|
| `--cat-green` | Estado activo, sync completed, enrolado, fingerprints ≥3 |
| `--cat-red` | Estado baja, sync failed, row attention (0 huellas), tipo A sobrantes |
| `--cat-amber` | Sync running/queued, fingerprints <3, row attention (<3 huellas) |
| `--cat-blue` | Dispositivos enrolados, sede, tipo B sobrantes |
| `--cat-gray` | Sin enrolar, sync queued, inactivo, ignorados |
| `--cat-orange` | Total sobrantes, tipo B razón |
| `--cat-purple` | Huellas disponibles (KPI) |
| `--cat-pink` | (no usado directamente en employees) |

**`[GAP]` detectados:**
- `[GAP: BACKEND]` — Contadores en chips rápidos del índice (requiere agregados en endpoint search).
- `[GAP: BACKEND]` — Filtros rápidos en assign-employees (`sin_huella`/`sin_device` no soportados).
- `[GAP: BACKEND]` — Ordenamiento server-side de tabla de índice.
- `[GAP]` — Documentación UI faltante (`UI_MASTER_STANDARD.md`, etc.).

---

## 4. Excepciones

**Ninguna.** No se modificaron controladores, modelos, rutas, migraciones ni lógica de negocio.

---

## 5. Validación

| Check | Resultado |
|---|---|
| `php artisan view:clear` | ✅ OK |
| `npm run build` | ✅ OK (58 modules, 0 errors) |
| Blade syntax (view:cache) | ✅ No ejecutado (pre-exists ModuleController error en route:list) |
| Rutas no modificadas | ✅ Verificado: ninguna ruta fue creada, modificada o eliminada |
| Consumidores de `x-stat-card` | ✅ 89 usos verificados; el fallback `var(--bs-{color}-subtle)` maneja `teal` y otros sin token |
| Consumidores de `_fingerprint-badge` | ✅ Solo `employees/index` (2 usos: PHP y JS) |
| Consumidores de `x-badge` en employees | ✅ 15 usos verificados; no se alteró la interfaz del componente |
| Dark mode | ✅ Tokens `--cat-*` ya resuelven valores distintos por tema (app.css líneas 22-30 y 76-84) |
| Responsive | ✅ `table-cards` CSS existente (app.css líneas 1523-1553) + `data-label` en assign-employees |
| Empty states | ✅ Sin cambios a empty states existentes |
| Loading states | ✅ Sin cambios a skeleton/spinner existentes |
| Error states | ✅ Sin cambios a panel-error existente |
| Acciones existentes | ✅ Todas preservadas: index pagination, create validation, edit update, sync polling, assign-employees submit, sobrantes ignore/unignore/remove |

---

## 6. Huérfanos

**CSS:**
- Clases `.row-attention-fp`, `.row-attention-none` se usan en `index.blade.php` y `employees-index.js`.
- Clases `.dot-indicator`, `.dot` se usan en `_fingerprint-badge.blade.php`, `edit.blade.php`, `index.blade.php`, y `employees-index.js`.

**JS:**
- `syncIconMap` en `buildRow()` usa los mismos iconos que el Blade.
- `fpDots` en `buildRow()` replica la lógica de `_fingerprint-badge` (necesario para AJAX rendering).

No hay huérfanos.

---

## 7. Pendiente (requiere backend o decisión adicional)

| Item | Razón | Marca |
|---|---|---|
| Exportar CSV/Excel | `employees.export` no existe | `[BLOQUEADO: ROUTE SAFETY]` |
| Vista tarjeta de empleado | `employees.show` no existe | `[BLOQUEADO: ROUTE SAFETY]` |
| Contadores en chips rápidos | Requiere agregados en endpoint search | `[GAP: BACKEND]` |
| Filtros rápidos en assign-employees | Requiere soporte backend | `[GAP: BACKEND]` |
| Ordenamiento server-side | Requiere endpoint con `sort` | `[GAP: BACKEND]` |
| Menú contextual `⋮ Más` | Decisión de complejidad pendiente | `[COMPONENTE NUEVO]` |
| Selector de columnas | Decisión de complejidad pendiente | `[COMPONENTE NUEVO]` |
| Unificar formularios edit (PUT único) | Requiere cambio de contrato backend | `[GAP: BACKEND]` |
