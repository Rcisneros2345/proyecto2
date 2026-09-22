# Auditoria de la sección devices

*Read‑only – sin modificaciones a archivos*

## 1. Inventario de archivos UI

| Ruta | Tipo | Propósito |
|---|---|---|
| `resources/views/devices/index.blade.php` | Blade (view) | Página principal de listado de dispositivos – tabla con KPIs superiores, filtros, acciones por fila (sync, editar, eliminar). |
| `resources/views/devices/create.blade.php` | Blade (view) | Formulario de creación de nuevo dispositivo. |
| `resources/views/devices/edit.blade.php` | Blade (view) | Página de edición de configuración del dispositivo (IP, puerto, contraseña, descripción). |
| `resources/views/devices/show.blade.php` | Blade (view) | Página de detalle del dispositivo – KPI grid, pestañas de empleados/asistencias/huellas, actividad reciente, acciones remotas. |
| `resources/js/devices-index.js` | JavaScript | Inicialización del data‑table, búsqueda en vivo, manejo de URL params, polling de sincronización, renderizado de tablas de empleados/asistencias/huellas. |
| `routes/web.php` (líneas 140‑166) | Ruta (PHP) | Definición de rutas `devices.*` (index, create, show, edit, update, destroy, sync‑*, refresh-data, progress, check-status, deduplicate). |

## 2. UI‑related componentes, páginas y rutas

| Componente / Página | Ruta asociada | Permisos (middleware) | Comentario |
|---|---|---|---|
| **Dispositivos – índice** | `devices.index` | `module_permission:dispositivos,view` | Listado con KPIs superiores, filtros, tabla table‑cards, acciones por fila (sync, editar, eliminar). |
| **Crear dispositivo** | `devices.create` | `admin` | Formulario de un solo paso: nombre, IP, puerto, contraseña, descripción. |
| **Editar dispositivo** | `devices.{id}/edit` | `admin` | Formulario de configuración: nombre, IP, puerto, contraseña, descripción. |
| **Detalle dispositivo** | `devices.show` | `module_permission:dispositivos,view` | KPI grid (empleados, asistencias, huellas, estado), pestañas (empleados, asistencias, huellas), actividad reciente, acciones remotas (sync users/fingerprints/attendances/all). |
| **Sincronizar todo** | `devices.sync-all` | `module_permission:dispositivos,sync` | Sincroniza usuarios, asistencias y huellas de golpe. |
| **Sincronizar usuarios** | `devices.sync-users` | `module_permission:dispositivos,sync` | Sincroniza solo usuarios/empleados. |
| **Sincronizar asistencias** | `devices.sync-attendances` | `module_permission:dispositivos,sync` | Sincroniza solo asistencias. |
| **Sincronizar huellas** | `devices.sync-fingerprints` | `module_permission:dispositivos,sync` | Sincroniza solo huellas. |
| **Estado de sincronización** | `devices.sync-status` | `module_permission:dispositivos,view` | Endpoint AJAX que devuelve estado actual (queued/running/completed/failed) y progreso. |
| **Actualizar datos** | `devices.refresh-data` | `module_permission:dispositivos,view` | Endpoint AJAX que devuelve counts actualizados para KPIs y tablas. |
| **Quitar dispositivo** | `devices.destroy` | `admin` | Elimina dispositivo y sus empleados, huellas y registros asociados. |
| **Deduplicar registros** | `devices.deduplicate` | `module_permission:dispositivos,sync` | Elimina registros duplicados por empleado, conservando el más antiguo. |

## 3. Inventario de los 12 puntos del estándar UI

| # | Punto | Hallazgo |
|---|---|---|
| **1. Flujo de usuario** | Recorrido punta‑a‑punto: índice → crear/editar/show. Errores: estados “vacío”, “cargando” (skeleton), “error” (panel‑error), validaciones de formulario. Sync: polling cada 3 s, toasts de éxito/fallo, botón “Actualizar datos” AJAX. |
| **2. Navegación** | Entradas: `devices.index`, `devices.create`, `devices.show`, `devices.{id}/edit`. Migas: `Operación › Dispositivos`, `Operación › Dispositivos › Agregar dispositivo`, `Operación › Dispositivos › Ver dispositivo`. Retornos: botón “Volver” en cada página. Navegación entre pestañas: empleados / asistencias / huellas en show. |
| **3. Página principal** | Propósito: supervisar la red biométrica y sus registros. Contenido al entrar: cabecera título “Dispositivos”, KPI grid (checadores registrados, en línea, empleados sincronizados, checadas almacenadas), tabla de dispositivos con estado, filtros, acciones masivas “Limpiar duplicados”. |
| **4. Listado** | Columnas: Dispositivo, IP, Puerto, Estado, Empleados, Registros, Acciones. Orden por defecto: por ID. Paginación: rows predeterminados (no especificado explícito, usa paginación default de Laravel). Densidad: tabla “table‑cards” con avatares, badges, chips. Acciones por fila: ver detalle (show), editar, re‑sincronizar asistencias, dar de baja (destructiva, confirmación). |
| **5. Detalle** | Estructura: página show con KPIs (4 cards), pestañas Empleados/Asistencias/Huellas, actividad reciente, zona de acciones remotas. Agrupación: identidad (nombre, IP, puerto, estado, serial number) + credenciales (firmware, versión, hora equipo) + sincronización (última operación, estado, progreso). |
| **6. Creación** | Campos: name (obligatorio), ip (obligatorio), port (obligatorio, default 4370), password (opcional), description (opcional). Validaciones: Laravel `Required`, `IP`, `Between` (puerto 1‑65535). Pasos: un solo formulario, submit a `devices.store`. Guardado: redirección a `devices.index` con toast de éxito. |
| **7. Edición** | Diferencias vs creación: campo `name` ya existente, IP/puerto/contraseña pre‑cargados. Campos obligatorios: name, ip, port. Validaciones idénticas a create. Guardado: redirección a `devices.show` con toast de éxito. |
| **8. Historial** | Qué se registra: cada operación de sincronización (`device_syncs` table) – status, stage, processed/total, created_at, error_message, dispositivo. Cómo se muestra: pestañas “Empleados/Asistencias/Huellas” con lista de items, badge de estado, progreso processed/total, enlace a “Ver cola completa”. |
| **9. Acciones** | Todas las acciones: Índice: Ver detalle, Editar, Sincronizar asistencias, Dar de baja (destructiva, confirmación). Crear/Guardar: Agregar al checador. Edición: Actualizar configuración. Show: 4 pestañas (Empleados, Asistencias, Huellas, Acciones remotas), botones sync users/fingerprints/attendances/all, polling progreso, toasts de éxito/fallo. Sobrantes: no aplica (no hay concepto de sobrantes en devices). Asignar grupo: no aplica. Permisos: cada acción tiene su middleware. Confirmaciones: modales/confirmations para borrados, sync‑now. |
| **10. Filtros** | Filtros existentes: búsqueda `q` (nombre/ID) en índice. No hay filtros por estado, rango de fechas, etc. Persistencia: parámetros en URL, mantenidos al navegar. Búsqueda: input “Buscar” con debounce. Limpieza: recarga directa de la URL elimina filtros. |
| **11. Tablas** | Patrón usado: `<table class="table table-hover align-middle mb-0 table-cards">` (Tailwind + clases propias). Responsive: `table-responsive` wrapper. Columnas fijas: Dispositivo (nombre + IP), Puerto, Estado, Empleados, Registros. Orden: encabezados con flecha (aunque handler AJAX no siempre está implementado para todas). Exportación: **no** hay botón de exportar CSV/Excel en la vista actual. |
| **12. Estados** | Vacío: mensaje “No hay checadores registrados aún” con CTA “Registrar primer dispositivo” (admin). Cargando: skeleton rows (4 KPIs) mostrados durante polling AJAX. Error: panel `.panel-error` con botón “Reintentar” / toast. Sin permisos: middleware bloquea acceso; muestra página de “403” de Laravel. Parcial: estado mixto cuando algunos filtros están activos y la tabla muestra filas parciales. Saturado: tabla con muchas filas; paginación evita overflow. |

## 4. Inconsistencias con el Design System

| Inconsistencia | Documento del Design System que viola | Comentario |
|---|---|---|
| Uso de componentes Blade personalizados `x-page-header`, `x-stat-card`, `kpi-grid` no definidos en `UI_MASTER_STANDARD.md` ni en `UI_COMPONENTS.md`. | `docs/ui/UI_MASTER_STANDARD.md` / `docs/ui/UI_COMPONENTS.md` | Estos componentes forman parte del patrón de la app pero no están catalogados como “cards” estándar; podrían necesitar revisión de consistencia visual. |
| Badges con clases `badge-with-dot cat-*` (online/offline/unknown) usadas en múltiples contextos sin una guía única. | `docs/ui/UI_DESIGN_SYSTEM.md` (paleta de colores) | No es un error grave, pero hay repetitiva definición de colores “cat‑*” para estado de dispositivo. |
| Tabla de dispositivos usa `table-cards` clase propia no documentada en `UI_TABLES.md`. | `docs/ui/UI_TABLES.md` | Se recomienda verificar si `table-cards` debería ser un patrón estándar o si se puede reemplazar por la clase `table-striped`. |
| KPI cards en `show.blade.php` y `index.blade.php` usan colores `teal`, `green`, `purple`, `blue` hardcodeados en lugar de tokens `--cat-*`. | `docs/ui/UI_DESIGN_SYSTEM.md` (uso de tokens) | Desviación menor, pero los tokens `--cat-*` ya están definidos y deberían usarse para consistencia. |
| Inputs con `maxlength` y `pattern` inline en create/edit; el Design System sugiere usar validaciones server‑side y mensajes de ayuda globales. | `docs/ui/UI_FORMS.md` | Pequeña desviación, no crítico. |
| Colores Bootstrap sueltos (`text-tertiary-token`, `text-muted`) en lugar de tokens `--cat-*` en algunos lugares. | `docs/ui/UI_DESIGN_SYSTEM.md` | Consistencia visual: preferir tokens `--cat-*` sobre clases sueltas. |

## 5. Duplicaciones y patrones repetidos

| Componente / Patrón | Archivo(s) | Observación |
|---|---|---|
| Formulario de **crear** y **editar** dispositivo comparten campos obligatorios (name, ip, port) y validaciones idénticas. | `resources/views/devices/create.blade.php`, `resources/views/devices/edit.blade.php` | La lógica de validación podría extraerse a un componente partial o a un helper Blade reutilizable. |
| Badges de estado `badge-with-dot cat-*` aparecen en `index`, `show` y `JS` (`devices-index.js`). | `resources/views/devices/index.blade.php`, `resources/views/devices/show.blade.php`, `resources/js/devices-index.js` | Mismo patrón; podría centralizarse en un partial de “badge‑de‑estado”. |
| KPI grid de 4 cards en `show.blade.php` es idéntico al patrón `stat-card` usado en otras secciones. | `resources/views/devices/show.blade.php` | Si el Design System ya define `x-stat-card`, su uso es consistente; no hay duplicación de markup. |
| Lógica de polling de sincronización (`setInterval 3s`) aparece tanto en `devices-index.js` como en el propio Blade (`@include('partials.sparkline')`). | `resources/js/devices-index.php`, `resources/views/devices/index.blade.php` | Duplicación de concepto; el polling es responsabilidad JS, los sparklines son presentación. |
| Rutas de sincronización (`sync-users`, `sync-fingerprints`, `sync-attendances`, `sync-all`) aparecen en `show.blade.php` y en el JS `devices-index.js`. | `routes/web.php`, `resources/js/devices-index.js` | Lógica duplicada; el drawer o panel podría reutilizar la misma acción del form. |
| Partial `sparkline` usado en `index`, `show` y `devices-index.js`. | `resources/views/partials.sparkline`, `resources/js/devices-index.js` | El partial existe y se reusa; no es una duplicación estricta pero sí patrón repetido. |

## 6. `[GAP]` – Casos que el Design System no cubre (alternativa más cercana)

| GAP | Descripción | Alternativa existente |
|---|---|---|
| **Tabla con exportación CSV/Excel** | El estándar de tablas no incluye botón de exportar; la sección devices no tiene esta funcionalidad. | Usar componente `table-export` de la app (si existe) o añadir un nuevo botón que invoque endpoint `devices.export` (no existe hoy). |
| **Vista “Tarjeta de dispositivo” independiente** | No hay una página de “perfil” de dispositivo que muestre solo datos esenciales sin pestañas complejas. | Se podría crear una ruta `devices.mini` que reutilice la tabla de índice con modo “solo‑lectura”. |
| **Estados de “sincronización en tiempo real”** | El panel de progreso usa polling cada 3 s; el Design System sugiere websockets para actualizaciones instantáneas. | Hoy se usa polling; no hay componente de “live‑status” en el sistema. |
| **Accesibilidad ARIA completa** | Algunos botones de acción carecen de `aria-label` descriptivo (ej. iconos de “eye”, “pencil” en índice). | Revisar y añadir `aria-label` consistente con el patrón `btn‑icon`. |
| **Filtros avanzados** | Solo existe búsqueda por nombre/ID. No hay filtros por estado (online/offline), rango de empleados, etc. | Revisar si es necesario para la operativa diaria. |

## 7. `[BLOQUEADO: ROUTE SAFETY]` – Mejoras que exigirían tocar una ruta

| Mejora propuesta | Ruta afectada | Riesgo |
|---|---|---|
| Añadir ruta `devices.export` (CSV/Excel) y endpoint asociado. | `routes/web.php` (nueva ruta `GET devices/export`) | **Alto** – requiere nuevo controlador, validación de permisos y generación de archivo; fuera del alcance actual (solo lectura). |
| Crear ruta `devices.mini` (perfil público simplificado). | `routes/web.php` (nueva ruta `GET devices/{id}/mini`) | **Medio** – nueva vista que reutilice partes de `show` pero necesita diseño de UI. |
| Mover la lógica de “sincronizar en dispositivos” a un componente único reutilizable y eliminar la duplicación entre `show` y el *drawer*. | No es una ruta, pero afecta a `routes/web.php` (mismo nombre de acción). | **Bajo** – refactor interno, sin cambiar la superficie de URLs. |

## 8. Riesgo por vista

| Vista | Nivel de riesgo | Justificación |
|---|---|---|
| **Dispositivos – índice** (`index.blade.php`) | **Medio** | KPIs superiores, tabla con filtros, acciones por fila, estados vacíos/loading/error, dependencia de datos del servidor (sync, status). |
| **Dispositivos – crear** (`create.blade.php`) | **Bajo** | Formulario simple, validaciones estándar, poco estado complejo. |
| **Dispositivos – editar** (`edit.blade.php`) | **Bajo** | Formulario de configuración, campos obligatorios claros, poco estado visual. |
| **Sobrantes** – no aplica (no hay concepto de sobrantes en devices). | — | — |
| **Detalle – show** (`show.blade.php`) | **Alto** | Mayor superficie: KPIs, 3 pestañas, historial, zona de riesgo, acciones de sincronización, fingerprint management. |

## 9. Recomendaciones finales (solo lectura)

1. **Validar consistencia de colores “cat‑*”** – asegurar que la paleta definida en `UI_DESIGN_SYSTEM.md` sea la única fuente; considerar la creación de un helper Blade `cat-badge($count)` para evitar duplicidad.
2. **Extraer el componente de “filtros rápidos”** (búsqueda, estado) a un partial reutilizable entre index y show.
3. **Añadir `aria-label`** a los iconos de acción en la tabla de índice y show para cumplir con los estándares de accesibilidad del Design System.
4. **Evaluar la posibilidad de un endpoint `devices.export`** fuera del alcance actual; si se necesita, planificar como mejora futura.
5. **Revisar el uso de `x-stat-card` y colores hardcodeados** – confirmar que estos componentes estén documentados o sustituirlos por variantes del sistema para evitar brechas de diseño.
6. **Centralizar la regla de badges de estado** (`badge-with-dot cat-*`) en un partial para evitar duplicación en index, show y JS.
7. **Evaluar filtros avanzados** en el índice (por estado online/offline, rango de empleados) si la operativa lo requiere.
8. **Mejorar la jerarquía visual** en el índice: los KPIs superiores deberían tener mayor peso que la tabla, para que el usuario identifique situaciones críticas al instante.

*Fin de la auditoría.*