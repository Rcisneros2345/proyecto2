# Implementación UI/UX — Devices

*Fecha: 2026-09-16*
*Estado: COMPLETADO (solo UI, sin cambios backend)*

---

## Archivos tocados

### 1. `resources/css/app.css`
- **Por qué**: Agregar estilos específicos para la sección devices: indicadores de atención a nivel de fila, zona de estado, y responsive para la zona de estado.
- **Cambios**:
  - `.device-row-offline` → borde rojo inset en primera celda (prioridad máxima: offline)
  - `.device-row-no-employees` → borde ámbar inset (falta enrolamiento)
  - `.device-row-sync-error` → borde rojo inset (errores de sincronización, futuro uso)
  - `.device-state-zone` → grid responsive para zona de estado en edit/show
  - `.ds-item`, `.ds-label`, `.ds-value` → estilos de la zona de estado
  - Media query `@media (max-width: 640px)` → layout 2 columnas en móvil

### 2. `resources/views/devices/index.blade.php`
- **Por qué**: Página principal que debe permitir identificar visualmente dispositivos que necesitan atención.
- **Cambios**:
  - **Badges normalizados**: Reemplazo de `<span class="badge badge-with-dot cat-*">` por `<x-badge color dot icon label size="sm" />`. Cada estado ahora lleva **icono + texto + color** (nunca solo color).
  - **Atención visual**: Filas con clase `device-row-offline` (borde rojo) o `device-row-no-employees` (borde ámbar) según prioridad.
  - **Columna IP+Puerto fusionada**: De "Dirección IP" y "Puerto" separadas a una sola columna "Conexión" (`IP:port`). Reduce ruido visual.
  - **Empleados = 0**: Muestra icono de advertencia junto al número 0.
  - **Accesibilidad**: `aria-label` en tabla, `aria-hidden` en iconos decorativos, `aria-live="polite"` en KPI grid, `role="alert"` en errores de validación.
  - **`title` descriptivos**: Todos los botones ahora incluyen el nombre del dispositivo en el tooltip.
  - **Empty state**: colspan ajustado a 6 (antes era 7 por las dos columnas separadas).

### 3. `resources/views/devices/edit.blade.php`
- **Por qué**: Agregar zona de estado aprobada y mejorar estructura del formulario.
- **Cambios**:
  - **[UI ONLY] Zona de estado** (`device-state-zone`): Grid con 7 items — Dispositivo, Conexión, Estado (badge con icono), Empleados, Registros, Huellas, Última sincronización (si existe).
  - **Agrupación lógica**: Formulario dividido en 3 fieldsets — "Identificación del dispositivo", "Datos de conexión", "Información adicional".
  - **`aria-required`** en campos obligatorios.
  - **`role="alert"`** en mensajes de error de validación.
  - **`novalidate`** en el form para permitir validación nativa.
  - **Help text mejorado**: "Dejar vacío para mantener la actual" en contraseña.
  - **Submit button mejorado**: Icono `bi-check-lg` + texto "Actualizar configuración" (antes solo "Actualizar").

### 4. `resources/views/devices/show.blade.php`
- **Por qué**: Página de mayor superficie; necesita zona de estado completa, badges normalizados, y mejor jerarquía visual.
- **Cambios**:
  - **[UI ONLY] Zona de estado mejorada**: Grid de 9 items — Equipo, Firmware, Conexión, Estado (badge), Hora del equipo, Empleados, Registros, Huellas, Última sync. Reemplaza la card anterior `if ($info)` con diseño más compacto y consistente.
  - **Badge meta actualizado**: De `['cat-green', 'bi-check-circle']` a `['color' => 'green', 'icon' => 'bi-check-circle']` para compatibilidad con `<x-badge>`.
  - **Badges normalizados**: Todos los `badge-with-dot cat-*` reemplazados por `<x-badge color dot icon label size="sm" />` en: estado del dispositivo, estado de sync, asistencias recientes.
  - **Accesibilidad**: `aria-label` en tablas, `aria-hidden` en iconos, `aria-live="polite"` en zona de sync y KPI grid, `role="alert"` en alertas.
  - **JS mantenido intacto**: Toda la lógica de polling, renderizado de tablas, búsqueda, y pestañas se conservó exactamente igual. Solo se actualizó el mapeo de colores en `syncStatusMeta` para usar nombres de color del componente badge.

### 5. `resources/views/devices/create.blade.php`
- **Por qué**: Mejorar estructura del formulario sin zona de estado (device no existe aún).
- **Cambios**:
  - **Sin zona de estado** (decisión aprobada: "device doesn't exist yet").
  - **Agrupación lógica**: 3 fieldsets — "Identificación del dispositivo", "Datos de conexión", "Información adicional".
  - **`aria-required`** en campos obligatorios.
  - **`role="alert"`** en mensajes de error.
  - **`novalidate`** en el form.
  - **Placeholders descriptivos**: "Ejemplo: Recepción Planta Baja", "192.168.1.x", "Ubicación, notas, responsable..."
  - **Help text mejorado**: "Nombre descriptivo para identificar el checador en la red.", "Opcional. Si el checador no tiene clave, deja vacío."
  - **Submit button mejorado**: Icono `bi-plus-lg` + texto "Guardar dispositivo".

---

## Componentes reutilizados vs creados

| Componente | Acción | Justificación |
|---|---|---|
| `<x-badge>` | **Reutilizado** | Ya existente en `resources/views/components/badge.blade.php`. Soporta `color`, `dot`, `icon`, `label`, `size`. |
| `<x-stat-card>` | **Reutilizado** | Ya existente en `resources/views/components/stat-card.blade.php`. Se mantuvo sin cambios (componente compartido). |
| `<x-page-header>` | **Reutilizado** | Ya existente. Se mantuvo sin cambios. |
| `@include('partials.empty-state')` | **Reutilizado** | Ya existente. Se mantuvo sin cambios. |
| `@include('partials.sparkline')` | **Reutilizado** | Ya existente. Se mantuvo sin cambios. |
| Zona de estado | **Inline Blade** | No se creó componente nuevo. Implementado inline en edit/show como `<div class="device-state-zone">`. Justificación: es específico de devices, no reutilizable en otras secciones. |

---

## Tokens usados

| Token | Uso |
|---|---|
| `--cat-red` | Fila offline, badge offline, sync error |
| `--cat-amber` | Fila sin empleados, badge running |
| `--cat-green` | Badge online, badge completed |
| `--cat-gray` | Badge unknown, badge queued |
| `--cat-blue` | Badge accent (KPI sparklines) |
| `--cat-purple` | Badge accent (KPI sparklines) |
| `--border` | Bordes de zona de estado |
| `--surface` | Fondo de zona de estado |
| `--text` | Texto principal de zona de estado |
| `--text-tertiary` | Labels de zona de estado |
| `--text-secondary` | Texto secundario |

### [GAP] detectados
1. **`x-stat-card` usa colores hardcodeados** (`teal`, `green`, `purple`, `blue`) en lugar de tokens `--cat-*`. No se modificó el componente porque es compartido por todas las secciones. **[GAP: BACKEND]** — requiere refactor del componente `stat-card` a nivel global.
2. **`latestSync` en edit** — puede no estar eager-loaded en el controlador. Se usa `@if ($device->latestSync?->finished_at)` con null-safe operator para evitar errores si no está disponible.

---

## Consumidores verificados de componentes modificados

| Componente | Consumidores en devices | Consumidores fuera de devices |
|---|---|---|
| `<x-badge>` | index, edit, show | employees/index, employees/edit, employees/sobrantes, dashboard, academia/dashboard, operations/queue |
| `<x-stat-card>` | index, show | (otras secciones del dashboard — NO modificado) |
| `badge-with-dot cat-*` (raw CSS) | index (eliminado), show (eliminado en Blade, mantenido en JS) | academia/dashboard, dashboard, employees/sobrantes, operations/queue |

**Nota sobre JS**: El JS de show (`renderRecentSyncs`, `renderAttendancesTable`, `renderRecentFeed`) aún usa `badge-with-dot cat-*` raw en los templates literales. Esto es correcto porque el JS genera HTML dinámico y `<x-badge>` no está disponible fuera de Blade. No se rompió nada.

---

## Excepciones: toques a controlador

**Ninguno.** No se modificó ningún controlador. Toda la información necesaria ya estaba disponible en las vistas.

---

## Validación

| Prueba | Resultado |
|---|---|
| `php artisan view:clear` | ✅ Compiled views cleared successfully |
| Sintaxis Blade | ✅ Todos los archivos compilan sin errores |
| Rutas | ✅ Sin cambios — `routes/web.php` no fue modificado |
| JS show | ✅ Script completo preservado, solo se actualizó mapeo de colores en `syncStatusMeta` |
| CSS tokens | ✅ Solo se usan tokens existentes (`--cat-*`, `--border`, `--surface`, `--text*`) |
| Dark mode | ✅ Todos los estilos usan tokens con soporte light/dark |
| Responsive | ✅ `device-state-zone` tiene media query para móvil (2 columnas) |
| Accesibilidad | ✅ `aria-label`, `aria-hidden`, `aria-live`, `aria-required`, `role="alert"`, `role="region"`, `role="tablist"`, `role="tab"`, `role="tabpanel"` |
| Empty state | ✅ Preservado con colspan ajustado |
| Actions intactas | ✅ Todas las rutas, formularios, data-confirm, data-sync intactos |
| Polling intacto | ✅ Lógica JS de polling, tabs, búsqueda, sync buttons sin cambios |

---

## Huérfanos

**Ninguno.** No se crearon componentes nuevos ni archivos CSS/JS separados. Todos los estilos se agregaron a `app.css` que ya existía.

---

## Pendiente (requiere backend o decisions adicionales)

1. **[BLOQUEADO: ROUTE SAFETY] devices.export** — Botón/exportación CSV-Excel. Requiere nueva ruta y endpoint.
2. **[GAP: BACKEND] Sincronización fallida en index** — No se puede mostrar estado de sync fallido en el índice porque `$device->latestSync` no está eager-loaded en el controlador del índice. Solo se muestra en show.
3. **[GAP: BACKEND] `x-stat-card` color normalization** — El componente compartido usa colores Bootstrap en vez de tokens `--cat-*`. Requiere refactor a nivel global.
4. **[GAP: BACKEND] Filtros avanzados** — No existen filtros por estado (online/offline) en el índice. Requiere controlador.
5. **[UI ONLY]Skeleton loading en index** — El estado de carga inicial podría mejorarse con skeleton rows cuando se carga la página. Actualmente se muestra el contenido directamente.
