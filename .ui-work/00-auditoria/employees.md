# Auditoria de la sección employees

*Read‑only – sin modificaciones a archivos*

## 1. Inventario de archivos UI

| Ruta | Tipo | Propósito |
|---|---|---|
| `resources/views/employees/index.blade.php` | Blade (view) | Página principal de listado de empleados – tabla, filtros, contador, acciones. |
| `resources/views/employees/create.blade.php` | Blade (view) | Formulario de creación de nuevo empleado. |
| `resources/views/employees/edit.blade.php` | Blade (view) | Página de edición – pestañas Identidad / Enrolamientos, KPIs, huellas, historial, sincronización. |
| `resources/views/employees/sobrantes.blade.php` | Blade (view) | Listado de sobrantes en dispositivos (empleados sin catálogo o dadas de baja). |
| `resources/views/employees/partials/_sync-preview-drawer.blade.php` | Blade (partial) | Drawer de preview de diferencias antes de sincronizar. |
| `resources/views/employees/partials/_sync-progress-panel.blade.php` | Blade (partial) | Panel de progreso de sincronización (polling cada 3 s). |
| `resources/views/permission-groups/assign-employees.blade.php` | Blade (view) | Asignación de empleados a un grupo de permisos. |
| `resources/js/employees-index.js` | JavaScript | Inicialización del data‑table, búsqueda en vivo y manejo de URL params. |
| `routes/web.php` (líneas 168‑192) | Ruta (PHP) | Definición de rutas `employees.*` (index, create, edit, store, update, destroy, sobrantes, sync‑devices, etc.). |

## 2. UI‑related components, páginas y rutas

| Componente / Página | Ruta asociada | Permisos (middleware) | Comentario |
|---|---|---|---|
| **Empleados – índice** | `employees.index` | `module_permission:empleados,view` | Listado con filtros, paginación, acciones masivas. |
| **Crear empleado** | `employees.create` | `admin` + `module_permission:empleados,create` | Formulario de un solo paso. |
| **Editar empleado** | `employees.{id}/edit` | `admin` + `module_permission:empleados,update` | Dos pestañas (Identidad, Enrolamientos), KPIs, huellas, historial. |
| **Sobrantes** | `employees.sobrantes` | `module_permission:empleados,view` | Empleados “huérfanos” en dispositivos. |
| **Asignar a grupo de permisos** | `permission-groups.{group}/employees` | `admin` | Formulario de checkboxes para seleccionar empleados. |
| **Sincronizar en dispositivo** | `employees.{id}/sync-devices` | `module_permission:empleados,update` | Abre offcanvas con preview de diff. |
| **Actualizar tarjeta** | `employees.{id}/update-card` | `module_permission:empleados,update` | Campo tarjeta por dispositivo. |

## 3. Inventario de los 12 puntos del estándar UI

| # | Punto | Hallazgo |
|---|---|---|
| **1. Flujo de usuario** | Recorrido punta‑a‑punto + caminos de error. Índice → Crear/Editar → Sobrantes. Errores: estados “vacío”, “cargando” (skeleton), “error” (panel‑error), validaciones de formulario. Sync: preview drawer, progreso polling, toasts de éxito/fallo. |
| **2. Navegación** | Entradas: `employees.index`, `employees.create`, `employees.{id}/edit`, `employees.sobrantes`. Migas: `Operación › Empleados`, `Operación › Empleados › Agregar empleado`, `Operación › Empleados › Editar`. Retornos: botones “Volver”. Navegación entre subvistas: pestañas Identidad/Enrolamientos, pestañas de sincronización, filtro rápido. |
| **3. Página principal** | Propósito: consultar, administrar estado de empleados en la red biométrica. Contenido al entrar: cabecera título “Empleados”, contador total, filtros rápidos, tabla paginada, acciones “Sobrantes” (admin). |
| **4. Listado** | Columnas: Empleado, Puesto, Adscripción, Hardware, Estado, Acciones. Orden por defecto: por ID. Paginación: 25/50/100 filas, selector de “per_page”. Densidad: tabla “table‑cards” con avatares, badges, chips. Acciones por fila: ver detalle, editar, re‑sincronizar, dar de baja (con confirmación), ver dispositivos. |
| **5. Detalle** | Estructura: página *edit* con KPIs (4 cards), pestañas Identidad/Enrolamientos, tarjetas laterales (Huellas, Historial, Zona de riesgo). Agrupación: identidad (nombre, badge, ID, estado catálogo, área, puesto, usuario de acceso) + credenciales (PIN, rol) + enrolamientos (dispositivos, UID, tarjetas, huellas, sync). |
| **6. Creación** | Campos: device (select), name (max 24), user_id (9 dígitos), password (8 dígitos opcional), card_number (10 dígitos opcional), area_id, puesto_id, role (0/13/14). Obligatorios: device, name, user_id. Validaciones: Laravel `Required`, `Digits`, `Between`, `Unique:employees`. Pasos: un solo formulario, submit a `employees.store`. Guardado: redirección a `employees.index` con toast de éxito. |
| **7. Edición** | Diferencias vs creación: campo `user_id_display` solo‑lectura (no editable). Campos bloqueados: `user_id_display`, `name` editable con aviso “se refleja en todos los checadores tras sincronizar”. Concurrencia: no hay validación de “última actualización”. |
| **8. Historial** | Qué se registra: cada operación de sincronización (`syncs` table) – status, stage, processed/total, created_at, error_message, dispositivo. Cómo se muestra: tarjeta “Historial de sincronización” con lista de items, badge de estado, progreso processed/total, enlace a “Ver cola completa”. |
| **9. Acciones** | Todas las acciones: Índice: Ver detalle, Editar, Sincronizar en dispositivos, Dar de baja (destructiva, confirmación). Crear/Guardar: Agregar al checador. Edición: Guardar identidad, Guardar cambios (credenciales), Sincronizar seleccionados, Copiar/quitar huella. Sobrantes: Ignorar, Restaurar, Eliminar. Asignar grupo: Guardar asignación. Permisos: cada acción tiene su middleware. Confirmaciones: modales/confirmations para borrados, ignore/eliminar sobrantes. Masivas: selector “Seleccionar todos”. |
| **10. Filtros** | Filtros existentes: búsqueda `q` (nombre/ID/puesto), `cargo`, `departamento`, `id_campus` (sede), `sin_huella`, `sin_device`. Persistencia: parámetros en URL, mantenidos al navegar. combinación: se pueden combinar todos los filtros simultáneamente. Búsqueda: input “Buscar” con debounce 300 ms, recarga directa de la URL. Limpieza: botón “Limpiar” que elimina todos los filtros y retorna a `employees.index`. |
| **11. Tablas** | Patrón usado: `<table class="table table-hover align-middle mb-0 table-cards">` (Tailwind + clases propias). Responsive: `table-responsive` wrapper, columnas con `min‑width` definidas. Columnas fijas: Empleado (220 px), Puesto (180 px), etc. Orden: encabezados con icono de flecha; funcionalidad de ordenamiento por AJAX. Exportación: **no** hay botón de exportar CSV/Excel en la vista actual. |
| **12. Estados** | Vacío: mensajes “No hay empleados” / “Sin resultados para tu filtro” con CTA “Agregar empleado”. Cargando: skeleton rows (5 filas) mostradas durante búsqueda AJAX. Error: panel `.panel-error` con botón “Reintentar”. Sin permisos: middleware bloquea acceso; muestra página de “403” de Laravel. Parcial: estado mixto cuando algunos filtros están activos y la tabla muestra filas parciales. Saturado: tabla con muchas filas; paginación y selector “per_page” evitan overflow. |

## 4. Inconsistencias con el Design System

| Inconsistencia | Documento del Design System que viola | Comentario |
|---|---|---|
| Uso de componentes Blade personalizados `x-page-header`, `x-stat-card`, `kpi-grid` no definidos en `UI_MASTER_STANDARD.md` ni en `UI_COMPONENTS.md`. | `docs/ui/UI_MASTER_STANDARD.md` / `docs/ui/UI_COMPONENTS.md` | Estos componentes forman parte del patrón de la app pero no están catalogados como “cards” estándar; podrían necesitar revisión de consistencia visual. |
| Badges con clases `cat-blue`, `cat-green`, `cat-amber`, `cat-gray`, `cat-purple`, `cat-red`, `cat-orange` – colores definidos en el sistema pero usadas en múltiples contextos sin una guía única. | `docs/ui/UI_DESIGN_SYSTEM.md` (paleta de colores) | No es un error grave, pero hay repetitiva definición de colores “cat‑*”. |
| Tabla de empleados usa `table-cards` clase propia no documentada en `UI_TABLES.md`. | `docs/ui/UI_TABLES.md` | Se recomienda verificar si `table-cards` debería ser un patrón estándar o si se puede reemplazar por la clase `table-striped`. |
| Componentes de formulario (`form-control`, `form-select`) son estándar de Tailwind, pero algunos `input` tienen `maxlength` y `pattern` inline; el Design System sugiere usar validaciones server‑side y mensajes de ayuda globales. | `docs/ui/UI_FORMS.md` | Pequeña desviación, no crítico. |

## 5. Duplicaciones y patrones repetidos

| Componente / Patrón | Archivo(s) | Observación |
|---|---|---|
| Formulario de **asignación de empleados** y la tabla de empleados en `index` comparten estructura de filtros y chips rápidos. | `resources/views/permission-groups/assign-employees.blade.php`, `resources/views/employees/index.blade.php` | La lógica de “Rápidos: Sin huellas / Sin enrolar” podría extraerse a un componente parcial reutilizable. |
| Badges de estado de huella (`cat-amber` para <3, `cat-green` para ≥3) aparecen tanto en `index` como en `edit`. | `resources/views/employees/index.blade.php`, `resources/views/employees/edit.blade.php` | Mismo patrón; podría centralizarse en un componente de “badge‑de‑huella”. |
| KPIs de 4 cards en `edit.blade.php` son idénticos al patrón `stat-card` usado en otras secciones. | `resources/views/employees/edit.blade.php` | Si el Design System ya define `x-stat-card`, su uso es consistente; no hay duplicación de markup. |
| Rutas de sincronización (`sync-devices`, `enrollment-diff`, `update-card`) aparecen tanto en `edit` como en el *drawer* de preview. | `routes/web.php`, `resources/views/employees/edit.blade.php` | Lógica duplicada; considerar si el *drawer* podría reutilizar la misma acción del form. |

## 6. `[GAP]` – Casos que el Design System no cubre (alternativa más cercana)

| GAP | Descripción | Alternativa existente |
|---|---|---|
| **Tabla con exportación CSV/Excel** | El estándar de tablas no incluye botón de exportar; la sección employees no tiene esta funcionalidad. | Usar componente `table-export` de la app (si existe) o añadir un nuevo botón que invoque endpoint `employees.export` (no existe hoy). |
| **Vista “Tarjeta de empleado” independiente** | No hay una página de “perfil” de empleado que muestre solo datos esenciales sin pestañas complejas. | Se podría crear una ruta `employees.show` que reutilice la tabla de índice con modo “solo‑lectura”. |
| **Estados de “sincronización en tiempo real”** | El panel de progreso usa polling cada 3 s; el Design System sugiere websockets para actualizaciones instantáneas. | Hoy se usa polling; no hay componente de “live‑status” en el sistema. |
| **Accesibilidad ARIA completa** | Algunos botones de acción carecen de `aria‑label` descriptivo (ej. iconos de “eye”, “pencil” en índice). | Revisar y añadir `aria-label` consistente con el patrón `btn‑icon`. |

## 7. `[BLOQUEADO: ROUTE SAFETY]` – Mejoras que exigirían tocar una ruta

| Mejora propuesta | Ruta afectada | Riesgo |
|---|---|---|
| Añadir ruta `employees.export` (CSV/Excel) y endpoint asociado. | `routes/web.php` (nueva ruta `GET employees/export`) | **Alto** – requiere nuevo controlador, validación de permisos y generación de archivo; fuera del alcance de esta auditoría (solo lectura). |
| Crear ruta `employees.show` (perfil público). | `routes/web.php` (nueva ruta `GET employees/{id}`) | **Medio** – nuevo controlador y vista; podría reutilizar partes de `edit` pero necesita diseño de UI. |
| Mover la lógica de “sincronizar en dispositivos” a un componente único reutilizable y eliminar la duplicación entre `edit` y el *drawer*. | No es una ruta, pero afecta a `routes/web.php` (mismo nombre de acción). | **Bajo** – refactor interno, sin cambiar la superficie de URLs. |

## 8. Riesgo por vista

| Vista | Nivel de riesgo | Justificación |
|---|---|---|
| **Empleados – índice** (`index.blade.php`) | **Medio** | Gran cantidad de filtros, acciones por fila, estados vacíos/loading/error, y dependencia de datos del servidor (sync, fingerprints). |
| **Empleados – crear** (`create.blade.php`) | **Bajo** | Formulario simple, validaciones estándar, poco estado complejo. |
| **Empleados – editar** (`edit.blade.php`) | **Alto** | Mayor superficie: KPIs, 2 pestañas, historial, zona de riesgo, acciones de sincronización, fingerprint management. |
| **Sobrantes** (`sobrantes.blade.php`) | **Medio** | Tabla de excepciones con acciones de ignore/unignore/remove; permisos de admin solo. |
| **Asignar empleados a grupo** (`assign-employees.blade.php`) | **Bajo** | Formulario de selección simple, sin lógica de negocio compleja. |

## 9. Recomendaciones finales (solo lectura)

1. **Validar consistencia de colores “cat‑*”** – asegurar que la paleta definida en `UI_DESIGN_SYSTEM.md` sea la única fuente; considerar la creación de un helper Blade `cat-badge($count)` para evitar duplicidad.
2. **Extraer el componente de “filtros rápidos”** (chips “Sin huellas”, “Sin enrolar”, “Sobrantes”) a un partial reutilizable entre `index` y `assign-employees`.
3. **Añadir `aria-label`** a los iconos de acción en la tabla de índice para cumplir con los estándares de accesibilidad del Design System.
4. **Evaluar la posibilidad de un endpoint `employees.export`** fuera del alcance actual; si se necesita, planificar como mejora futura.
5. **Revisar el uso de `x-page-header` y `x-stat-card`** – confirmar que estos componentes estén documentados o sustituirlos por variantes del sistema para evitar brechas de diseño.

*Fin de la auditoría.*