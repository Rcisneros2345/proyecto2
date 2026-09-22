# Plan de UI — cómo mejorar el diseño sin rehacerlo

## Lo primero: ya tienes el plan escrito y nadie lo está usando

En la raíz del proyecto hay un archivo llamado `diseño`, **sin extensión**, de 17 KB. Es una
especificación de UX seria y bien pensada: principio de no rediseñar por gusto, diagnóstico
desde el usuario, decisiones implementables (layout, espaciado, tipografía, color semántico,
componentes a extender, estados, responsive, accesibilidad WCAG 2.2 AA, microcopy,
arquitectura del sidebar), **cinco oleadas de implementación**, análisis de impacto y
criterios de aceptación.

Es mejor que la mayoría de los planes de UI que se escriben desde cero. Y está invisible:

- Sin extensión, ningún editor lo abre como markdown.
- En la raíz, `ui-designer` y `ui-implementer` no lo encuentran: sus prompts apuntan a
  `docs/ui/` y `.ui-work/`.
- La auditoría UI del 2026-09-19 no lo menciona ni una vez — reinventó parte de su contenido.

**Acción cero, 30 segundos:**

```bash
git mv "diseño" docs/ui/UX_SISTEMA_COMPLETO.md
```

Y en `.ai/guidelines/05-ui.md`, agrega la línea: *"Fuente de verdad de UI:
`docs/ui/UI_DESIGN_PRINCIPLES.md` + `docs/ui/UX_SISTEMA_COMPLETO.md`. Léelos antes de
proponer nada."*

Eso solo ya cambia la calidad de todo lo que el agente de UI proponga a partir de ahora.

---

## Cómo encajan tus dos documentos de UI

| Documento | Qué es | Para qué sirve |
|---|---|---|
| `UX_SISTEMA_COMPLETO.md` (el `diseño`) | El **plan**: qué patrón debe tener cada tipo de pantalla, en 5 oleadas | Define el destino |
| `auditoria-ui-ux-2026-09-19.md` | El **estado**: 16 problemas concretos con archivo:línea, priorizados | Define la ruta |

No compiten, se complementan. La auditoría dice qué está roto hoy; la spec dice cómo debe
quedar. El error sería tratarlas como dos planes rivales y elegir uno.

Nota importante sobre cobertura: la auditoría UI revisó **auth, dashboard, empleados,
dispositivos, asistencias y academia parcial**. Quedaron sin revisar firebird, RBAC,
incidencias, áreas/puestos, navigation-items, operaciones — más de la mitad de las 95 vistas.
Su propia recomendación #16 lo dice. No cierres la fase de UI creyendo que está auditada
entera.

---

## Orden de ejecución

### Oleada 0 — Quick wins (una sesión, ~3 horas)

Las 5 recomendaciones de prioridad alta de tu auditoría son todas de esfuerzo bajo. Hazlas
juntas, en un solo módulo de trabajo, con `impacto` al final:

| # | Qué | Dónde | Por qué primero |
|---|---|---|---|
| 1 | Unificar polling de KPIs: eliminar `fetchDashboardKPIs()`, dejar solo Heartbeat | `app.js:69-165` + `dashboard.blade.php:224-311` | Hoy hay dos temporizadores pidiendo lo mismo: requests duplicados y condiciones de carrera |
| 2 | Quitar el toast de "actualizado" cada 30 s | `dashboard.blade.php:277` | Una notificación que aparece sola cada 30 segundos entrena al usuario a ignorar **todas** las notificaciones, incluidas las que importan |
| 3 | `table-cards` en la tabla de empleados | `employees/index.blade.php` | Es la pantalla más usada y en móvil es ilegible. El patrón ya existe en dispositivos: se copia, no se inventa |
| 4 | Borrar el `@if(false)` del layout | `layouts/admin.blade.php:46-210` | 165 líneas muertas en un archivo de 460. Cada sesión de IA las lee y gasta contexto en menú que no existe |
| 5 | Borrar el CSS de skeleton no usado | `app.css:1374-1390` | Se define y nunca se aplica |

```
@ui-implementer aplica las 5 recomendaciones de prioridad alta de
.ui-work/00-auditoria/auditoria-ui-ux-2026-09-19.md. Antes lee
docs/ui/UX_SISTEMA_COMPLETO.md. No toques rutas ni lógica. Al terminar corre
php scripts/impacto.php y revisa cada consumidor del layout y de app.js.
```

Ojo con el #4: tocar el layout afecta a **las 95 vistas**. Es exactamente el caso donde
`impacto` gana su sueldo. Y con el #1: `app.js` lo cargan todas las pantallas; verifica que
ninguna otra dependa de `fetchDashboardKPIs()`.

Verificación de la oleada: abre dashboard y empleados en claro y en oscuro, en escritorio y
en móvil, y comprueba que las pruebas Feature que asertan HTML siguen verdes
(`DashboardRenderTest`, `EmployeeIndexTest`).

### Oleada 1 — Sistema de componentes (la de mayor apalancamiento)

Es la "Oleada 0" de tu spec, y es la que hace que todo lo demás sea barato. Endurecer los
componentes antes de tocar 80 vistas, no después:

- `x-page-header`: subtítulo + slot `actions` (primary / outline / danger), sin segundo `h1`.
- `x-filter-bar`: submit "Filtrar" + `clearUrl` + slot de chips + labels con `for`/`id`.
- `x-data-table`: empty vía `partials/empty-state` con título, causa y CTA; `table-cards` con
  `data-label`; acciones Ver/Editar/Eliminar con el `data-confirm` del layout; sin
  `table-bordered` por defecto.
- `x-badge`: reescribir a clases del DS (`badge--status`, `badge-with-dot`, `cat-*`) y de paso
  arreglar el problema #7 de la auditoría (badge pierde fondo en modo claro,
  `app.css:1332-1337`). Tu spec además señala que arrastra utilidad Tailwind muerta.

Dos avisos que tu propia spec ya marca y conviene respetar:

- Si `x-data-table` cambia sus defaults, hay que actualizar **las 4 vistas que ya lo usan** en
  el mismo cambio. Eso es `impacto`, no confianza.
- `x-data-table` con `render` HTML (ciclos) es **deuda de XSS**. Nada nuevo con `{!! !!}`;
  migrar a slots Blade cuando se toque esa vista. Súbelo de categoría: eso no es deuda de
  estilo, es seguridad. Que lo mire `seguridad` antes de la oleada 2.

Además, aquí se resuelve el problema #6 de la auditoría: **hay dos sistemas de drawer**
(`x-drawer` y el CSS de `app.css:898`). Uno de los dos sobra, y elegir cuál es una decisión
con consecuencias: mándala por `decisiones`, no la resuelva el implementador a mitad de una
vista.

### Oleada 2 — Listas CRUD simétricas

áreas, puestos, incidencias, permission-groups, permissions, cursos, planes, ciclos.

Son las pantallas que hoy están más dispersas y las más baratas de homogeneizar, porque todas
hacen lo mismo: listar → filtrar → ver → editar. Van en lotes de 2-3 vistas por sesión (regla
de tu `ui-orchestrator`, que sigue vigente).

En ciclos hay un modal huérfano que tu spec ya detectó: o se usa desde el header, o se borra
y se deja `create`. Decide una y aplícala a todas.

### Oleada 3 — Listas operativas

dispositivos, empleados, asistencias, puntualidad, huellas, cola, firebird — solo el
"chrome": header, filtros, estados vacíos. **Sin tocar la lógica de sincronización**, que
además está en reparación (ver `PLAN-DE-CONTINUACION.md`, fase 1).

Aquí conviene coordinar: si la fase 1 va a reescribir `DeviceSyncController` y
`FingerprintController`, la UI de esas pantallas se toca **después**, no en paralelo. Dos
agentes sobre los mismos archivos es la receta del conflicto.

### Oleada 4 — Show, formularios y academia densa

Detalle de dispositivo, empleado, grupo, alumno, profesor, ciclo, curso, plan. Formularios con
`@error` + `old()` + agrupación por secciones.

Asimetría deliberada que tu spec defiende y conviene mantener: horarios, kárdex y los
dashboards **no** se aplanan a CRUD. La grilla semanal es el patrón correcto para un horario;
forzarla a tabla genérica sería empeorar el diseño en nombre de la consistencia.

### Oleada 5 — Namespaces de modelos

Tu spec la incluye, y tiene razón en ponerla al final y en marcarla como trabajo de
arquitectura, no de diseño. Yo la sacaría del plan de UI por completo: mover 40 modelos de
namespace toca `use` en controllers, services, tests, factories y seeders. Es un cambio
transversal de PHP cuyo beneficio es de orden, no de usuario.

Si se hace, va como su propia decisión (`decisiones`), con `arquitecto` y `revisor`, y con la
suite completa antes y después. No mezclada con trabajo visual.

---

## Prioridad media y baja: qué sí y qué no

De las recomendaciones #6 a #16 de tu auditoría:

**Sí, entran naturalmente en las oleadas:**
- Mover inline styles a clases (#6) → oleadas 2-4, según se toque cada vista.
- Badges en modo claro (#7) → oleada 1, al reescribir `x-badge`.
- `.col-xl-4` sticky solo en empleados (#8) → oleada 1.
- `font-weight: 750` → 700 u 800 (#9) → oleada 1, un solo renglón de CSS.
- Unificar drawers (#10) → oleada 1, pero por `decisiones` primero.
- `aria-live` en el contador AJAX (#12) → oleada 3.
- Loading state y "olvidé mi contraseña" en login (#13, #14) → el segundo **no es UI**:
  necesita backend (ruta, mailable, tokens). Si lo quieres, es una funcionalidad, no un
  retoque.

**Con cuidado:**
- Migrar Bootstrap de CDN a Vite (#11). Es correcto técnicamente: hoy cargas Bootstrap dos
  veces (`admin.blade.php:24-26` + Vite). Pero toca **todas** las pantallas a la vez y en
  producción hay que rehacer el build. Va solo, con su propia verificación visual, nunca
  mezclado con otra oleada.

**Pendiente de verdad:**
- Auditar las vistas no revisadas (#16). Es la única de esfuerzo alto de la lista, y es la que
  evita que "terminemos la UI" con la mitad del sistema sin mirar.

---

## Cómo medir que el diseño mejoró

Los criterios de aceptación ya están escritos en tu spec, y son buenos. Los repito porque son
la parte que se olvida:

- Un solo `h1` visible por página; breadcrumb que refleja el módulo real.
- Toda lista: estado vacío con causa y acción, filtros con label y "Limpiar".
- "Sin resultados de filtro" **distinto** del vacío inicial. Son situaciones distintas y hoy se
  ven igual.
- Acciones primarias naranja; destructivas rojo con la confirmación del layout, no `confirm()`
  suelto.
- CRUDs análogos con la misma composición de componentes.
- Claro y oscuro con tokens, sin colores Bootstrap crudos.
- Tablas usables a 768 px con `table-cards`, sin scroll horizontal de página.
- Errores de validación junto al campo.

Y una métrica sencilla que puedes correr tú:

```bash
rg -c 'style="' resources/views | sort -t: -k2 -rn | head    # inline styles por vista
rg -l '<h1' resources/views | wc -l                          # vistas con h1 propio
rg -c '{!!' resources/views                                  # riesgo de XSS pendiente
```

Si esos tres números bajan oleada tras oleada, el sistema se está homogeneizando de verdad.

---

## Una advertencia sobre "mejorar el diseño"

El sistema visual que tienes está calificado 7.5/10 por tu propia auditoría, y coincido: DM
Sans + JetBrains Mono, tokens con dark/light sin FOUC, 8 componentes, iconografía consistente.
Eso ya es mejor que la mayoría de los sistemas internos.

El problema de tu UI **no es que se vea mal, es que no es uniforme**: la misma tarea se ve
distinta en dispositivos que en áreas. Por eso el trabajo correcto es homogeneizar, y por eso
tu spec acierta al empezar con "no rediseñar por gusto".

Si en algún momento un agente propone una paleta nueva, una librería de componentes nueva o
un rediseño total: eso es empezar de cero con el 7.5 que ya tienes, y perder seis meses de
decisiones acumuladas. La respuesta es no.
