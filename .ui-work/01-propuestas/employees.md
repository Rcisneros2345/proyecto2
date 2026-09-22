# Propuesta de rediseño — Sección employees

> **Base:** auditoría `.ui-work/00-auditoria/employees.md` (101 líneas) + evidencia verificada en código.
> **GAP de documentación:** en `docs/ui/` solo existe `UI_DESIGN_PRINCIPLES.md`. Los documentos citados por la auditoría (`UI_MASTER_STANDARD.md`, `UI_COMPONENTS.md`, `UI_TABLES.md`, `UI_FORMS.md`, `UI_DESIGN_SYSTEM.md`, `UI_PAGE_PATTERNS.md`) **no existen**. Esta propuesta se apoya en los principios vigentes y en el código real; donde un documento faltante sería la referencia, se marca `[GAP]`.

---

## 1. Diagnóstico

| # | Problema | Evidencia |
|---|---|---|
| 1 | **Dos acciones primarias en edit.** "Guardar identidad" y "Guardar cambios" son dos botones `btn-primary` compitiendo en la misma página. Viola el principio "una sola acción primaria por página". | `edit.blade.php` líneas 128-131 y 173-176; `UI_DESIGN_PRINCIPLES.md` §1. |
| 2 | **Tokens de color inconsistentes.** `x-stat-card` usa clases Bootstrap (`--bs-{color}-subtle`, `text-{color}`) en lugar de tokens `--cat-*`; `sobrantes` usa `text-warning/danger/info/secondary` sueltos. `app.css` línea 1411 marca `stat-card` como alias legado (el contenedor real es `.kpi-card`). | `stat-card.blade.php` líneas 3-13; `sobrantes.blade.php` líneas 76-101; `app.css` líneas 1068-1104 y 1411. |
| 3 | **Regla de huellas duplicada en 3 lugares.** El partial `_fingerprint-badge` centraliza la regla 0→gray / <3→amber / ≥3→green, pero `edit` la repite inline y `employees-index.js` la duplica en JS. Cualquier cambio de umbral exige tocar 3 archivos. | `_fingerprint-badge.blade.php`; `edit.blade.php` líneas 212-213 y 320-321; `employees-index.js` líneas 151-154. |
| 4 | **Acciones de fila duplicadas en índice.** Los iconos `eye` y `pencil` apuntan ambos a `employees.edit`. Dos iconos, mismo destino = ruido; además el `eye` sugiere "ver detalle" que no existe como página. | `index.blade.php` líneas 358-366; `employees-index.js` líneas 197-200. |
| 5 | **Sin exportación ni vista de detalle.** No existen `employees.export` ni `employees.show`. El usuario no puede exportar el listado ni consultar un empleado sin entrar al modo edición (riesgo de cambio accidental). | `routes/web.php` líneas 168-192 (grupo `employees.`); auditoría §6-7. |

**Nota de documentación:** la auditoría recomienda "extraer filtros rápidos a partial" y "crear helper cat-badge". Verificado en código: `_quick-filters.blade.php` **ya existe** y se usa en índice; `_fingerprint-badge.blade.php` **ya existe**; `x-badge` **ya mapea color→cat-\***. El trabajo pendiente no es crearlos, sino **reutilizarlos** donde hoy se duplican (assign-employees, edit, JS).

---

## 2. Trabajo del usuario

- **Usuario:** administrador de RRHH / operador de control de acceso (admin) y personal con permiso `empleados,view` (consulta).
- **Frecuencia:** índice a diario (listado operativo de uso intensivo); edit ocasional; create puntual; sobrantes semanal; assign-employees puntual (admin).
- **Tarea real:** mantener la plantilla enrolada y sincronizada con los checadores biométricos: detectar quién falta por enrolar, quién tiene huellas insuficientes, quién está sobrante en un dispositivo y quién debe darse de baja.
- **Decisión:** ¿a quién enrolar/sincronizar? ¿qué sobrante ignorar o eliminar? ¿qué empleado dar de baja?
- **Acción principal:** en índice, **buscar/consultar empleados y entrar a editar**; en edit, **guardar cambios** (una sola acción primaria).
- **Información necesaria para decidir:** estado de huellas (0/<3/≥3), dispositivos enrolados, último sync (estado + fecha), estado catálogo (activo/baja), sede, puesto, tarjeta RFID.
- **Consulta rara vez:** historial completo de sync (vive en la columna lateral de edit y en "Ver cola completa").

---

## 3. Patrón aplicado

`UI_PAGE_PATTERNS.md` no existe → `[GAP]`. Aplico los patrones que la evidencia del código y `UI_DESIGN_PRINCIPLES.md` soportan:

| Página | Patrón | Justificación |
|---|---|---|
| Índice | **Listado operativo con filtros + tabla** (densidad alta) | Se usa a diario para buscar, comparar y detectar anomalías. |
| Create | **Formulario de creación de una columna** (densidad cómoda) | Proceso guiado, pocos campos, una sola acción. |
| Edit | **Detalle-edición con resumen KPI + pestañas** (densidad media) | Superficie grande: identidad, credenciales, enrolamientos, historial. |
| Sobrantes | **Listado de excepciones con stats + tabla** (densidad media) | Auditoría de huérfanos; las stats dan contexto antes de la tabla. |
| Assign-employees | **Selección masiva simple** (densidad cómoda) | Checkboxes + guardar; sin lógica compleja. |

---

## 4. Estructura propuesta

### 4.1 Índice (`employees.index`)

```
┌────────────────────────────────────────────────────────────────────────────┐
│ 12 columnas · contenedor acotado (max-width) · densidad alta               │
├────────────────────────────────────────────────────────────────────────────┤
│ [x-page-header] Empleados  ········  Sobrantes (badge)                     │
│ ┌──────────────────────────────────────────────────────────────────────┐   │
│ │ FILTROS (x-filter-bar): Buscar [____] Puesto [▾] Depto [▾] Sede [▾] │   │
│ │ [Filtrar] [Limpiar]                                                  │   │
│ │ Activos: [q ✕] [cargo ✕] ...   Rápidos: [Sin huellas] [Sin enrolar]  │   │
│ └──────────────────────────────────────────────────────────────────────┘   │
│ Tabs: [Todos] [Activos] [Bajas] [Sobrantes]                                │
│ Contador "N empleados"  ········  [+ Agregar empleado]  ← acción primaria  │
│ ┌──────────────────────────────────────────────────────────────────────┐   │
│ │ TABLA (table-cards en móvil):                                        │   │
│ │ Empleado | Puesto | Adscripción | Hardware | Estado | Acciones       │   │
│ │  · avatar+nombre+ID  · cargo+depto · sede+contrato · chips+huellas   │   │
│ │  · badge sync compacto  · badge estado  · [✎] [⇅] [🗑]               │   │
│ └──────────────────────────────────────────────────────────────────────┘   │
│ Mostrando X–Y de Z · Filas [25|50|100] · [paginación]                      │
└────────────────────────────────────────────────────────────────────────────┘
```

Cambios clave:
- **Acciones de fila: máximo 2 visibles** → `✎ Editar` (primaria) y `⋮ Más` (menú contextual con Re-sincronizar, Dar de baja, Ver dispositivo). Se elimina el `eye` duplicado.
- **Selector de columnas** `Columnas ▾` en el toolbar de la tabla (ocultar/mostrar Adscripción, Hardware, etc.) — `[UI ONLY]` si se persiste en localStorage, `[GAP: BACKEND]` si se persiste por usuario.
- **Exportar** en toolbar → `[BLOQUEADO: ROUTE SAFETY]` (no existe `employees.export`); el botón se diseña pero queda deshabilitado/marcado como pendiente.

### 4.2 Create (`employees.create`)

```
┌────────────────────────────────────────────────────────────────────────────┐
│ 12 columnas · card centrada (col-lg-6) · densidad cómoda                   │
│ [x-page-header] Agregar empleado ········ [Volver]                         │
│ ┌──────────────────────────────────────────────┐                           │
│ │ Dispositivo [▾]                              │                           │
│ │ Nombre completo [____________] (max 24)      │                           │
│ │ ID badge [______]  PIN [______]             │                           │
│ │ Tarjeta [______]  Área [▾]                  │                           │
│ │ Puesto [▾]  Rol [▾]                         │                           │
│ │ [Agregar al checador]  [Cancelar]           │  ← una sola acción primaria│
│ └──────────────────────────────────────────────┘                           │
└────────────────────────────────────────────────────────────────────────────┘
```

Cambios clave:
- **Quitar `pattern` y `maxlength` inline** como única validación; dejar la validación server-side como fuente de verdad y `form-text` de ayuda (ya existe en la mayoría). `[UI ONLY]`.
- Mantener una columna (ya cumple `UI_DESIGN_PRINCIPLES` §10).

### 4.3 Edit (`employees.edit`)

```
┌────────────────────────────────────────────────────────────────────────────┐
│ 12 columnas · densidad media                                               │
│ [x-page-header] Editar empleado: Nombre ········ [Volver]                  │
│ KPI GRID (4 x-stat-card con tokens cat-*):                                 │
│ [Huellas] [Dispositivos] [Tarjetas] [Último sync]                          │
│ Tabs: [Identidad] [Enrolamientos]                                          │
│ ┌─ col-8 ──────────────────────┐ ┌─ col-4 (sticky) ───────────────────┐   │
│ │ Identidad (form)             │ │ Huellas guardadas (badge + lista)  │   │
│ │ Credenciales (form)          │ │ Historial de sync (lista)          │   │
│ │  · un solo botón primario    │ │ Zona de riesgo (destructiva)       │   │
│ │    "Guardar cambios"         │ └────────────────────────────────────┘   │
│ │ Enrolamientos (tabla)        │                                          │
│ │ Enviar a dispositivos        │                                          │
│ │ [sync-progress-panel]        │                                          │
│ └──────────────────────────────┘                                          │
│ [sync-preview-drawer] (offcanvas 480px)                                   │
└────────────────────────────────────────────────────────────────────────────┘
```

Cambios clave:
- **Unificar los dos botones primarios** en un solo "Guardar cambios" que persista identidad + credenciales en una sola petición. ⛔ **PUERTA** (requiere decisión de producto: hoy son dos `PUT` separados al mismo endpoint; unificar cambia el contrato de guardado). Alternativa sin tocar backend: mantener dos formularios pero **uno solo con `btn-primary`** y el otro como `btn-outline-primary` secundario. `[UI ONLY]` en la alternativa.
- **`x-stat-card` alineado a tokens** `--cat-*` (ver §6). `[UI ONLY]`.
- **Regla de huellas centralizada**: usar `_fingerprint-badge` en edit (hoy duplica la regla inline). `[UI ONLY]`.

### 4.4 Sobrantes (`employees.sobrantes`)

```
┌────────────────────────────────────────────────────────────────────────────┐
│ 12 columnas · densidad media                                               │
│ [x-page-header] Sobrantes ········ [Mostrar ignorados] [Volver]            │
│ FILTROS: Dispositivo [▾] Tipo [▾] Buscar [____] [Filtrar] [Limpiar]        │
│ STATS (4 cards con tokens):                                                │
│ [Total] [Tipo A] [Tipo B] [Ignorados]                                      │
│ TABLA: Tipo | Dispositivo | UID | Empleado | ID Firebird | Razón | Estado │
│        | Acciones [↺] [👁] [🗑]                                             │
│ [paginación]                                                               │
└────────────────────────────────────────────────────────────────────────────┘
```

Cambios clave:
- **Stats con tokens** en vez de `text-warning/danger/info/secondary` sueltos. `[UI ONLY]`.
- **Badges con `x-badge`** en vez de `badge-with-dot cat-*` crudos. `[UI ONLY]`.

### 4.5 Assign-employees (`permission-groups.{group}/employees`)

```
┌────────────────────────────────────────────────────────────────────────────┐
│ 12 columnas · densidad cómoda                                              │
│ [x-page-header] Asignar empleados ········ [Volver]                        │
│ Rápidos: [Sin huellas] [Sin enrolar]  ← reutilizar _quick-filters          │
│ ┌──────────────────────────────────────────────────────────────────────┐   │
│ │ [☑] Nombre | ID | Área | Puesto                                     │   │
│ │ [☑] ...                                                             │   │
│ └──────────────────────────────────────────────────────────────────────┘   │
│ [Guardar asignación]  ← acción primaria                                    │
└────────────────────────────────────────────────────────────────────────────┘
```

Cambios clave:
- **Reutilizar `_quick-filters`** (ya existe) para filtrar sin huellas/sin enrolar antes de asignar. ⚠️ Requiere que el backend de esta página acepte `sin_huella`/`sin_device` → `[GAP: BACKEND]`.
- **`table-cards` en móvil** (hoy la tabla no tiene el patrón responsive). `[UI ONLY]`.

---

## 5. Mapa de componentes

| Elemento | Componente | Estado | Archivo |
|---|---|---|---|
| Cabecera de página | `x-page-header` | existente | `resources/views/components/page-header.blade.php` |
| Filtros de índice | `x-filter-bar` (o form actual) | existente / extensión | `resources/views/components/filter-bar.blade.php` |
| Chips rápidos | `_quick-filters` | existente (reutilizar en assign-employees) | `resources/views/employees/partials/_quick-filters.blade.php` |
| Badge de huellas | `_fingerprint-badge` | existente (extender uso a edit y JS) | `resources/views/employees/partials/_fingerprint-badge.blade.php` |
| Badges de estado | `x-badge` | existente (sustituir `cat-*` crudos) | `resources/views/components/badge.blade.php` |
| KPI cards | `x-stat-card` | extensión (tokens `--cat-*`) | `resources/views/components/stat-card.blade.php` |
| Tabla índice | tabla manual `table-cards` | extensión (selector columnas, menú ⋮) | `resources/views/employees/index.blade.php` |
| Tabla estándar | `x-data-table` | existente (referencia para sobrantes/assign) | `resources/views/components/data-table.blade.php` |
| Drawer preview sync | `_sync-preview-drawer` | existente | `resources/views/employees/partials/_sync-preview-drawer.blade.php` |
| Panel progreso sync | `_sync-progress-panel` | existente (polling 3s, `aria-live`) | `resources/views/employees/partials/_sync-progress-panel.blade.php` |
| Menú contextual de fila (⋮ Más) | — | `[COMPONENTE NUEVO]` (dropdown de acciones de fila reutilizable) | — |
| Selector de columnas | — | `[COMPONENTE NUEVO]` (dropdown Columnas ▾) | — |
| Exportar CSV/Excel | — | `[BLOQUEADO: ROUTE SAFETY]` (no existe `employees.export`) | `routes/web.php` |
| Vista tarjeta de empleado | — | `[BLOQUEADO: ROUTE SAFETY]` (no existe `employees.show`) | `routes/web.php` |

**Justificación de los 2 componentes nuevos (máximo permitido):**
1. **Menú contextual de fila** — hoy cada fila muestra hasta 4 iconos sueltos (eye, pencil, sync, destroy) y el principio §11 limita a 2 visibles. Un dropdown `⋮ Más` reutilizable resuelve índice, sobrantes y futuras tablas. No existe ningún dropdown de acciones en el sistema.
2. **Selector de columnas** — la tabla de índice tiene 6 columnas con datos densos (Hardware, Adscripción); el usuario experto necesita ocultar columnas sin pedir cambio de código. No existe ningún control equivalente.

---

## 6. Jerarquía y color

### Índice
- **Primario:** tabla + contador + CTA "Agregar empleado" (admin). Se entiende en 2 s: "aquí están mis empleados".
- **Secundario:** filtros, tabs por estado, chips rápidos.
- **Terciario:** selector de columnas, exportar, menú ⋮ por fila.
- **Tokens:** `--cat-blue` (sede), `--cat-gray` (sin enrolar), `--cat-amber` (sin huellas/aviso), `--cat-green` (activo/enrolado), `--cat-red` (baja/destructivo). Naranja (`--cat-orange`/`--primary`) solo en CTA y tab activo.
- **Light/Dark:** los tokens `--cat-*` ya resuelven a valores distintos por tema (app.css líneas 22-30 y 76-84); verificar contraste AA de `--cat-amber` sobre superficie en ambos temas.

### Edit
- **Primario:** un solo botón "Guardar cambios" (o el que decida la ⛔ PUERTA).
- **Secundario:** KPI grid, tabs, formularios, lista de huellas, historial.
- **Terciario:** zona de riesgo (destructiva, separada y con confirmación), drawer de preview.
- **Tokens:** KPI con `--cat-purple/blue/green` y sync con `--cat-green/red/amber/gray` (mapeo ya existente en `edit.blade.php` líneas 10-11). **Corregir `x-stat-card`** para usar `--cat-*` en vez de `--bs-*-subtle`/`text-*`.

### Create / Sobrantes / Assign
- Create: una acción primaria ("Agregar al checador"), todo lo demás secundario.
- Sobrantes: stats como contexto (P1), tabla como operativo (P1), acciones destructivas terciarias con confirmación.
- Assign: una acción primaria ("Guardar asignación").

---

## 7. Oportunidades de comprensión visual

| Información | Representación actual | Representación propuesta | Propósito |
|---|---|---|---|
| Estado de huellas (0/<3/≥3) | Badge `cat-*` en índice, edit y JS (regla duplicada) | `_fingerprint-badge` en los 3 lugares | Detectar de un vistazo quién necesita enrolamiento; una sola fuente de verdad |
| Estado de sync por empleado | Badge compacto en columna Hardware | Mantener, pero con `x-badge` (ya mapea color→cat-*) | Saber si el último sync falló sin abrir el registro |
| Resumen de sobrantes | 4 cards con colores Bootstrap sueltos | 4 cards con tokens `--cat-*` + etiqueta | Contexto antes de la tabla: ¿cuántos huérfanos hay y de qué tipo? |
| Conteos de atención en índice (sin huellas / sin enrolar / sobrantes) | Chips `_quick-filters` (ya existen) | Mantener; añadir contador en el chip si el backend lo provee | Pasar del resumen a los registros afectados con un clic |
| Progreso de sync | Panel con polling 3s + barra de progreso | Mantener (ya tiene `aria-live`); solo alinear badges a `x-badge` | Seguimiento sin recargar la página |

**No se proponen gráficas nuevas** (línea, donut, tendencia): el backend no expone histórico de métricas para alimentarlas y el principio §8 prohíbe gráficas decorativas. La tabla y los badges son la representación adecuada para esta sección.

---

## 8. Tabla (índice)

- **Consulta:** búsqueda `q` (nombre/ID/puesto) con debounce 300 ms + filtros `cargo`, `departamento`, `id_campus`, `sin_huella`, `sin_device` (combinables, persistidos en URL). Ya existe y funciona (SSR + AJAX).
- **Filtros:** global (`q`) y contextuales (selects). Los chips rápidos `_quick-filters` ya existen. Filtros por columna: no existen → `[GAP]` (no propongo inventarlos: el backend no los soporta).
- **Ordenamiento:** los encabezados muestran icono `bi-arrow-down-up` pero **no hay ordenamiento funcional** (auditoría §11: "funcionalidad de ordenamiento por AJAX" — verificado: no hay handler en `employees-index.js`). Propuesta: ordenar por Empleado (nombre) y Puesto como mínimo, server-side → `[GAP: BACKEND]` (el endpoint `search` no recibe `sort`).
- **Selección de filas:** no existe y **no se propone** — no hay acción masiva real en el backend (solo sync por empleado). `[GAP: BACKEND]` si algún día se quiere "sincronizar seleccionados".
- **Acciones:** hoy 4 iconos sueltos (eye, pencil, sync, destroy). Propuesta: **2 visibles** (`✎ Editar` + `⋮ Más`) con Re-sincronizar, Dar de baja y Ver dispositivo dentro del menú. `[UI ONLY]` (el menú es presentación; las rutas ya existen).
- **Columnas:** Empleado, Puesto, Adscripción, Hardware, Estado, Acciones. Propuesta: `Columnas ▾` para ocultar Adscripción/Hardware (datos densos) → `[COMPONENTE NUEVO]` + persistencia en localStorage `[UI ONLY]`.
- **Exportación:** no existe. Botón diseñado pero **bloqueado** → `[BLOQUEADO: ROUTE SAFETY]` (falta `employees.export`). Alcance propuesto cuando exista: página actual / filtrados / todos + columnas visibles/seleccionadas.
- **Impresión:** no hay vista de impresión. `@media print` puede bastar (ocultar filtros/acciones, expandir tabla) sin backend → `[UI ONLY]`.
- **Densidad:** alta (listado operativo). Filas compactas; `table-cards` en móvil ya convierte a tarjetas.
- **Paginación:** 25/50/100 con selector `per_page` y resumen "Mostrando X–Y de Z". Ya existe.
- **Responsive:** desktop tabla completa; tablet scroll horizontal (`table-responsive`); móvil `table-cards` (CSS ya implementado en app.css líneas 1523-1553).
- **Persistencia:** filtros en URL (ya existe). Columnas visibles y densidad → localStorage `[UI ONLY]`; por usuario → `[GAP: BACKEND]`.
- **Backend requerido:** ordenamiento server-side (`sort`), exportación (`employees.export`), filtros por columna (si se piden). Todo lo demás es presentación.

---

## 9. Estados

| Estado | Índice | Edit | Sobrantes | Assign |
|---|---|---|---|---|
| Vacío | "No hay empleados" + CTA (existe) | N/A (formulario) | "No hay sobrantes" (existe) | "No hay empleados registrados" (existe, mejorar con CTA) |
| Cargando | Skeleton 5 filas (existe) + spinner AJAX | Spinner en botón submit (existe) | — | — |
| Error | `.panel-error` + Reintentar (existe) | Toast de error (existe vía `dashToast`) | — | — |
| Sin permisos | 403 Laravel (middleware) | 403 Laravel | 403 Laravel | 403 Laravel |
| Sin resultados de filtro | "Sin resultados para tu filtro" + Limpiar (existe) | N/A | "No hay sobrantes" | — |
| Procesando | Spinner en botones sync/destroy | Spinner en submit + panel progreso sync (existe) | Spinner en submit | Spinner en submit |

---

## 10. Responsive

- **Desktop (≥1200px):** índice tabla completa; edit con columna lateral sticky (`col-xl-4`, CSS ya existe en app.css líneas 1063-1066).
- **Tablet (768-1199px):** scroll horizontal en tablas (`table-responsive`); KPI grid 2×2 (CSS ya existe línea 1155-1159); edit sin sticky lateral.
- **Móvil (<768px):** `table-cards` convierte filas a tarjetas (CSS ya existe líneas 1523-1553); KPI grid 1 columna; filtros apilados; acciones de fila dentro de la tarjeta (última celda con borde superior, ya implementado).

---

## 11. Accesibilidad

- **Labels:** todos los filtros y campos ya tienen `<label for>` y `aria-label` (verificado en index/create/edit). Mantener.
- **Focus:** verificar foco visible en chips rápidos, tabs y acciones de fila (los `btn-ghost` icon-only necesitan `:focus-visible`).
- **Teclado:** tabs Bootstrap ya son navegables por teclado; el nuevo menú `⋮ Más` debe implementarse con `aria-expanded`, `aria-haspopup` y cierre con Escape.
- **Contraste:** `--cat-amber` sobre superficie en Light puede no alcanzar 4.5:1 → verificar y ajustar token si falla. `text-tertiary-token` en chips activos: revisar AA.
- **ARIA:** `aria-live="polite"` ya existe en contador y panel de progreso; `aria-busy` en tbody durante AJAX (existe). Añadir `aria-sort` cuando exista ordenamiento real. El `eye` duplicado se elimina, reduciendo confusión de `aria-label`.
- **Color nunca único portador:** todos los badges llevan texto (estado, conteo); los chips llevan icono + texto. Mantener.

---

## 12. Qué se elimina

| Elemento | Problema que resuelve quitarlo |
|---|---|
| Icono `eye` duplicado en acciones de fila (apunta a `employees.edit` igual que `pencil`) | Ruido y falsa promesa de "ver detalle" cuando no existe `employees.show`; deja 2 acciones visibles máx. |
| Regla de huellas inline en `edit.blade.php` (2 sitios) y en `employees-index.js` | Duplicación: un cambio de umbral exige tocar 3 archivos; se centraliza en `_fingerprint-badge` |
| Clases `cat-*` crudas en HTML/JS (chips activos, sobrantes, drawer sync, buildRow) | Duplicación del mapeo que `x-badge` ya resuelve; riesgo de deriva de tokens |
| `--bs-{color}-subtle`/`text-{color}` en `x-stat-card` | Inconsistencia con el sistema de tokens `--cat-*`; `app.css` ya marca `stat-card` como alias legado |
| `pattern`/`maxlength` inline como única validación en create | Mensajes de validación del navegador inconsistentes con el sistema; la validación server-side + `form-text` es la fuente de verdad |
| `text-warning/danger/info/secondary` sueltos en stats de sobrantes | Colores de framework que no pasan por tokens; viola `UI_DESIGN_PRINCIPLES` §5 |

---

## 13. Riesgos y bloqueos

- `[GAP]` — Documentación: faltan `UI_MASTER_STANDARD.md`, `UI_COMPONENTS.md`, `UI_TABLES.md`, `UI_FORMS.md`, `UI_DESIGN_SYSTEM.md`, `UI_PAGE_PATTERNS.md`. Sin ellos, la consistencia se valida contra `UI_DESIGN_PRINCIPLES.md` y el código.
- `[COMPONENTE NUEVO]` — Menú contextual de fila `⋮ Más` (justificado en §5).
- `[COMPONENTE NUEVO]` — Selector de columnas `Columnas ▾` (justificado en §5).
- `[GAP: BACKEND]` — Ordenamiento server-side de la tabla de índice (endpoint `search` no acepta `sort`).
- `[GAP: BACKEND]` — Filtros rápidos en assign-employees (`sin_huella`/`sin_device` no soportados por esa página).
- `[GAP: BACKEND]` — Contadores en chips rápidos del índice (requiere agregados en el endpoint).
- `[BLOQUEADO: ROUTE SAFETY]` — Exportación CSV/Excel (`employees.export` no existe).
- `[BLOQUEADO: ROUTE SAFETY]` — Vista tarjeta de empleado (`employees.show` no existe).
- ⛔ **PUERTA (decisión de producto):** unificar "Guardar identidad" + "Guardar cambios" en un solo botón/petición en edit. Alternativa sin backend: un solo `btn-primary` y el otro `btn-outline-primary`.

---

## 14. Esfuerzo

**Esfuerzo global: medio.**

Orden recomendado de implementación (cada paso es independiente y de bajo riesgo):

1. **Bajo — Higiene de tokens y componentes** (sin backend): sustituir `cat-*` crudos por `x-badge`; alinear `x-stat-card` a `--cat-*`; centralizar regla de huellas en `_fingerprint-badge` (edit + JS); quitar `pattern`/`maxlength` como única validación en create.
2. **Bajo — Acciones de fila** (`[COMPONENTE NUEVO]` menú ⋮): eliminar `eye` duplicado, mover sync/destroy al menú. Solo presentación.
3. **Medio — Selector de columnas** (`[COMPONENTE NUEVO]` + localStorage): ocultar Adscripción/Hardware en índice.
4. **Medio — Edit**: resolver ⛔ PUERTA del botón único; si se aprueba, requiere coordinación con backend (una sola petición `PUT`).
5. **Alto — Exportación** (`[BLOQUEADO: ROUTE SAFETY]`): requiere ruta + controlador + generación de archivo; diseñar el modal de alcance/columnas antes de implementar.
6. **Alto — Vista tarjeta** (`[BLOQUEADO: ROUTE SAFETY]`): requiere ruta `employees.show`; alternativa intermedia sin ruta: drawer con datos ya disponibles en índice.