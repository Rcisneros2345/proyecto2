# Auditoria UI/UX Completa - 2026-09-19

> Analisis exhaustivo de diseno, usabilidad, sistema de diseno, herramientas, tipografia, colores, organizacion y consistencia por vista/modulo.

---

## 1. Resumen Ejecutivo

| Dimension | Estado | Detalle |
|---|---|---|
| **Framework CSS** | OK | Bootstrap 5.3.3 + CSS custom properties (design tokens) |
| **Tipografia** | OK | DM Sans (UI) + JetBrains Mono (datos/codigo) |
| **Paleta de colores** | OK | 9 colores categoricos + semanticos con soporte dark/light |
| **Componentes Blade** | OK | 8 componentes reutilizables |
| **Modo oscuro/claro** | OK | Toggle con persistencia en localStorage, sin FOUC |
| **Responsive** | Parcial | Desktop y movil funcionan; tablet tiene gaps |
| **Accesibilidad** | Mejorable | Focus-visible presente, faltan labels en algunos forms |
| **Iconos** | OK | Bootstrap Icons 1.11.3 en todo el proyecto |
| **JavaScript** | OK | Vanilla JS, modulos lazy-load, progressive enhancement |

**Calificacion global: 7.5/10** - Sistema de diseno solido, con deuda tecnica menor.

---

## 2. Sistema de Diseno Actual

### 2.1 Tipografia

| Uso | Familia | Peso | Tamano | Fuente |
|---|---|---|---|---|
| UI general | DM Sans | 400-800 | 14px base | Google Fonts (app.css:1) |
| Numeros/datos/codigo | JetBrains Mono | 500-700 | Variable | Google Fonts (app.css:1) |
| Titulos de pagina | DM Sans | 800 | 23px | app.css:456 |
| Subtitulos | DM Sans | 500 | 13px | app.css:477 |
| Labels de seccion | DM Sans | 700 | 11px uppercase | app.css:231-237 |
| Texto de tabla | DM Sans | 400 | 13px | app.css:1243 |
| Badges | DM Sans | 700 | 11px | app.css:1282-1293 |
| KPI values | JetBrains Mono | 800 | 28px | app.css:1080-1087 |
| Toasts | DM Sans | 500-700 | 12-13px | app.css:693-694 |

**Hallazgos:**
- OK: Consistencia excelente: dos familias claras con roles definidos
- OK: JetBrains Mono con font-variant-numeric: tabular-nums
- ALERTA: app.css:1022 usa font-weight: 750 (no estandar, browsers redondean)
- ALERTA: login.blade.php:33 usa style="color:var(--text)" inline (innecesario)

### 2.2 Paleta de Colores

#### Design Tokens (Dark Mode)

| Token | Valor | Uso |
|---|---|---|
| --bg | #0b1120 | Fondo de pagina |
| --surface | #131b2e | Fondo de cards |
| --surface-elevated | #1a2438 | Modales, dropdowns |
| --border | rgba(255,255,255,.06) | Bordes sutiles |
| --primary | #ea580c | Naranja principal |
| --text | #f1f5f9 | Texto principal |
| --text-secondary | #94a3b8 | Texto secundario |
| --success | #10b981 | Verde |
| --warning | #fbbf24 | Amarillo |
| --error | #ef4444 | Rojo |
| --info | #3b82f6 | Azul |

#### Colores Categoricos

| Token | Dark | Light |
|---|---|---|
| --cat-blue | #3b82f6 | #2563eb |
| --cat-orange | #f97316 | #ea580c |
| --cat-purple | #8b5cf6 | #7c3aed |
| --cat-pink | #ec4899 | #db2777 |
| --cat-red | #ef4444 | #dc2626 |
| --cat-green | #10b981 | #059669 |
| --cat-amber | #fbbf24 | #d97706 |
| --cat-gray | #94a3b8 | #64748b |

**Hallazgos:**
- OK: Paleta completa dark/light
- OK: Fallbacks para navegadores sin color-mix()
- ALERTA: app.css:1332-1337 - badges cat-* pierden fondo en light mode
- ALERTA: employees-index.js:184 - colores hardcoded por concatenacion JS

### 2.3 Espaciado y Layout

| Elemento | Espaciado | Fuente |
|---|---|---|
| Sidebar expanded | 258px | app.css:45 |
| Sidebar collapsed | 72px | app.css:46 |
| Main padding | 24px 32px 44px | app.css:388 |
| Card border-radius | 16px | app.css:47 |
| Button border-radius | 8px | app.css:919 |
| Badge border-radius | 999px | app.css:1282 |
| KPI grid gap | 14px | app.css:1069 |
| Nav link min-height | 42px | app.css:241 |

**Responsive breakpoints:**

| Breakpoint | Comportamiento |
|---|---|
| >1200px | Sidebar sticky en employee edit |
| <=1024px | KPI 2 col, pipeline 3 col |
| <=640px | Sidebar overlay, KPI 1 col, table-cards |

**Hallazgos:**
- OK: Layout shell con sidebar fijo + main scrollable
- OK: Table-cards conversion en movil
- ALERTA: app.css:1064-1066 - .col-xl-4 sticky generic afecta todas las vistas

### 2.4 Framework CSS/JS

| Herramienta | Version | Uso |
|---|---|---|
| Bootstrap | 5.3.3 | Grid, utilities, alerts, forms |
| Bootstrap Icons | 1.11.3 | Todos los iconos |
| Custom CSS | 1612 lineas | Design tokens, layout, componentes |
| Vanilla JS | 686 lineas (app.js) | Theme, sidebar, toasts, confirm |
| Vite | OK | Bundling CSS y JS |

**Hallazgos:**
- OK: Stack ligero sin dependencias pesadas
- OK: SVG charts manuales (dashboard, sparklines)
- OK: Progressive enhancement en employees-index.js
- ALERTA: admin.blade.php:24-26 - Bootstrap CDN + Vite = carga doble potencial

### 2.5 Componentes Blade

| Componente | Lineas | Uso |
|---|---|---|
| x-page-header | 23 | Titulo + subtitulo + acciones |
| x-data-table | 175 | Tabla con paginacion, acciones, responsive |
| x-stat-card | 44 | KPI card con icono, valor, tendencia |
| x-badge | 47 | Badges de estado con variantes |
| x-filter-bar | 28 | Barra de filtros |
| x-drawer | 24 | Offcanvas panel (Bootstrap) |
| x-navigation-menu | 27 | Menu dinamico desde NavigationItem |

**Partials:**

| Partial | Lineas | Uso |
|---|---|---|
| donut | 58 | Grafica donut SVG |
| empty-state | 15 | Estado vacio con CTA |
| sparkline | 32 | Sparkline SVG |

**Hallazgos:**
- OK: Componentes bien desacoplados
- OK: data-table soporta columnas ocultas responsive (hideOn)
- ALERTA: x-filter-bar es muy generico (solo form con slot)
- ALERTA: x-drawer usa Bootstrap offcanvas, no el drawer custom de CSS (dos sistemas)

---

## 3. Auditoria por Modulo

### 3.1 AUTH (login)

**Archivos:** auth/login.blade.php (73 lineas)

| Aspecto | Estado |
|---|---|
| Layout | Standalone (no usa admin layout) |
| Branding | OK - brand mark con fingerprint |
| Formulario | OK - labels, icons, validacion |
| Tema | OK - dark/light heredado |
| Responsive | OK - width: min(400px, 100%) |
| Accesibilidad | Parcial - falta aria-describedby para errores |

**Problemas:**
1. login.blade.php:33 - style="color:var(--text)" inline redundante
2. login.blade.php:60 - style="height:42px" inline en boton
3. login.blade.php:65 - style="border-top:1px solid var(--border)" inline
4. Sin indicador de carga en boton submit
5. Sin link "forgot password"

### 3.2 DASHBOARD

**Archivos:** dashboard.blade.php (312 lineas), partials/donut, partials/sparkline

| Aspecto | Estado |
|---|---|
| Layout | OK - extiende admin layout |
| Banner contextual | OK - card con borde primary |
| KPIs | OK - grid de stat-card con sparklines |
| Grafica tendencia | OK - SVG manual con gradient |
| Pipeline | OK - 6 pasos con progreso |
| Donut | OK - SVG con legenda interactiva |
| Tabla reciente | OK - hover, badges, links |
| Polling | PROBLEMA - doble polling |
| Responsive | OK |

**Problemas:**
1. dashboard.blade.php:8 - style="border-left:4px solid var(--primary)" inline
2. dashboard.blade.php:285-306 - fetchDashboardKPIs() DUPLICADO con Heartbeat de app.js
3. dashboard.blade.php:277 - Toast cada 30s (ruidoso, molesto)
4. Dos sistemas de polling compitiendo (Heartbeat cada 20s + fetchDashboardKPIs cada 30s)

### 3.3 EMPLEADOS

**Archivos:** employees/index.blade.php (495 lineas), create, edit, sobrantes, employees-index.js (417 lineas)

| Aspecto | Estado |
|---|---|
| Header | OK - x-page-header |
| Filtros | OK - busqueda, cargo, depto, sede |
| Tabla | OK - custom table, thead sticky |
| Busqueda live | OK - debounce 300ms, AJAX |
| Paginacion AJAX | OK - rendered via JS |
| Empty state | OK - diferenciado |
| Acciones | OK - edit, sync, delete con confirm |
| Responsive | PROBLEMA - sin table-cards |

**Problemas:**
1. employees/index.blade.php:18 - shadow-sm inconsistente
2. employees-index.js:93,106,183 - max-width:18ch/20ch/22ch hardcoded inline
3. employees-index.js:184 - colores hardcoded por concatenacion JS
4. Falta aria-live en contador de empleados
5. Falta data-label en celdas para table-cards en movil

### 3.4 DISPOSITIVOS

**Archivos:** devices/index.blade.php (170 lineas), show, create, edit

| Aspecto | Estado |
|---|---|
| Header | OK |
| KPIs | OK - 4 stat-cards |
| Tabla | OK - table-cards, attention indicators |
| Attention indicators | OK - red/amber strips |
| State zone | OK |
| Acciones | OK |

**Problemas:**
1. devices/index.blade.php:58 - shadow-sm inconsistente
2. devices/index.blade.php:17 - btn-outline-danger para accion no destructiva
3. Sin loading state en sync desde tabla

### 3.5 ASISTENCIAS

**Archivos:** attendances/ (no auditado en detalle)

| Aspecto | Estado |
|---|---|
| Exportacion CSV | OK |
| Filtros por fecha | OK |
| Tabla registros | OK |

Requiere revision posterior.

### 3.6 ACADEMIA

**Archivos:** academia/ con 10 subdirectorios

| Aspecto | Estado |
|---|---|
| Estructura | OK - organizado por subdominio |
| Ciclo selector | OK |
| Dashboard academico | OK |
| Horarios | OK |
| Kardex | OK |

**Problemas:**
- academia/empty-ciclos.blade.php podria ser huérfano

### 3.7 FIREBIRD, RBAC, OTRAS VISTAS

No auditadas en detalle. Requieren revision posterior.

---

## 4. Mapa de Consistencia

### 4.1 Uso de componentes por modulo

| Modulo | page-header | data-table | stat-card | badge | empty-state |
|---|---|---|---|---|---|
| Dashboard | NO (manual) | NO (manual) | SI | SI | SI |
| Empleados | SI | NO (custom) | NO | SI | SI |
| Dispositivos | SI | NO (manual) | SI | SI | SI |
| Asistencias | SI | SI | NO | SI | SI |
| Academia | SI | SI | SI | SI | SI |
| Areas | SI | SI | NO | SI | SI |
| RBAC | SI | SI | NO | SI | SI |

**Hallazgo:** Dashboard y empleados tienen layouts custom que rompen la consistencia.

### 4.2 Inline styles vs clases CSS

| Ubicacion | Valor hardcoded | Deberia ser |
|---|---|---|
| dashboard.blade.php:8 | border-left:4px solid | Clase CSS |
| dashboard.blade.php:17 | height:44px | Clase CSS |
| login.blade.php:33 | color:var(--text) | Innecesario |
| login.blade.php:60 | height:42px | Clase CSS |
| login.blade.php:65 | border-top | CSS de card-footer |
| employees-index.js:93,106,183 | max-width:18ch/20ch/22ch | Clase CSS |

Total: ~10 inline styles innecesarios.

---

## 5. Problemas de Usabilidad

### 5.1 Criticos

| # | Problema | Ubicacion | Impacto |
|---|---|---|---|
| 1 | Doble polling de KPIs | app.js:69-165, dashboard:224-311 | Requests duplicados, race conditions |
| 2 | Toast cada 30s en dashboard | dashboard.blade.php:277 | Fatiga de notificaciones |
| 3 | Tabla empleados sin responsive cards | employees/index.blade.php | Movil ilegible |

### 5.2 Medios

| # | Problema | Ubicacion |
|---|---|---|
| 4 | ~10 inline styles innecesarios | Varios archivos |
| 5 | shadow-sm inconsistente | employees, devices |
| 6 | x-drawer vs CSS drawer (dos sistemas) | drawer.blade.php, app.css:898 |
| 7 | Badge light mode pierde fondo | app.css:1332-1337 |
| 8 | Sidebar sticky generico | app.css:1064-1066 |
| 9 | font-weight 750 no estandar | app.css:1022 |

### 5.3 Menores

| # | Problema | Ubicacion |
|---|---|---|
| 10 | Login sin forgot password | auth/login.blade.php |
| 11 | Login sin loading state | auth/login.blade.php:60 |
| 12 | Hardcoded max-width en JS | employees-index.js |
| 13 | Welcome page probablemente legacy | welcome.blade.php |
| 14 | CDN Bootstrap + Vite carga doble | admin.blade.php:24-26 |
| 15 | Skeleton loaders definidos pero no usados | app.css:1374-1390 |
| 16 | @if(false) muerto en layout (~165 lineas) | admin.blade.php:46-210 |

---

## 6. Recomendaciones

### Prioridad Alta

| # | Recomendacion | Esfuerzo | Impacto |
|---|---|---|---|
| 1 | Unificar polling de KPIs - eliminar fetchDashboardKPIs() y dejar Heartbeat | Bajo | Elimina duplicados |
| 2 | Eliminar toast de actualizacion automatica cada 30s | Bajo | Mejora UX |
| 3 | Agregar table-cards a tabla de empleados | Bajo | Movil legible |
| 4 | Eliminar @if(false) del layout (165 lineas muertas) | Bajo | Limpieza |
| 5 | Eliminar skeleton CSS muerto (app.css:1374-1390) | Bajo | Limpieza |

### Prioridad Media

| # | Recomendacion | Esfuerzo | Impacto |
|---|---|---|---|
| 6 | Mover inline styles a clases CSS reutilizables | Medio | Mantenibilidad |
| 7 | Corregir badges light mode (app.css:1332-1337) | Bajo | Consistencia |
| 8 | Scoped .col-xl-4 sticky solo a empleados | Bajo | Evita bugs |
| 9 | Corregir font-weight: 750 a 700 u 800 | Bajo | Rendering |
| 10 | Unificar x-drawer con CSS drawer custom | Medio | Elimina duplicacion |
| 11 | Migrar Bootstrap CDN a Vite bundling | Medio | Performance |
| 12 | Agregar aria-live a contador AJAX de empleados | Bajo | Accesibilidad |

### Prioridad Baja

| # | Recomendacion | Esfuerzo | Impacto |
|---|---|---|---|
| 13 | Agregar loading state en login submit | Bajo | UX |
| 14 | Agregar link forgot password en login | Bajo | UX |
| 15 | Reemplazar shadow-sm por token custom | Bajo | Consistencia |
| 16 | Auditar vistas no revisadas (asistencias, academia, firebird, rbac) | Alto | Cobertura |
