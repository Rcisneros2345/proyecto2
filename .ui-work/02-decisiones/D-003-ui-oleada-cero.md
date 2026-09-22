---
decision: D-003
modulo: NAVEGACION_UI
fecha: 2026-09-19
estado: PROPUESTA
decidido_por: —
---

# D-003 · Por dónde empezar la mejora de UI

## El problema en una frase

Hay dos documentos de UI que no se hablan: `diseño` (17 KB, en la raíz, sin extensión, con el
plan por oleadas) y `auditoria-ui-ux-2026-09-19.md` (con 16 problemas priorizados). Y el
primero es invisible para los agentes, que buscan en `docs/ui/` y `.ui-work/`.

## Opciones

### Opción A — Activar la spec y empezar por los 5 quick wins
- Mover `diseño` → `docs/ui/UX_SISTEMA_COMPLETO.md`, citarlo en `.ai/guidelines/05-ui.md`.
- Aplicar las 5 recomendaciones de prioridad alta (polling duplicado, toast cada 30 s,
  `table-cards` en empleados, `@if(false)` del layout, CSS skeleton muerto).
- **Cuesta:** una sesión, ~3 horas.
- **Gana:** resultados visibles hoy y la spec deja de estar muerta.
- **Riesgo:** BAJO, salvo el `@if(false)` del layout, que toca las 95 vistas → `impacto` obligatorio.

### Opción B — Empezar por el sistema de componentes (oleada 0 de la spec)
- Endurecer `x-page-header`, `x-filter-bar`, `x-data-table`, `x-badge` antes de tocar vistas.
- **Cuesta:** 2-3 sesiones.
- **Gana:** máximo apalancamiento: cada vista posterior sale más barata.
- **Riesgo:** MEDIO — cambiar defaults de `x-data-table` obliga a actualizar sus 4 consumidores
  en el mismo cambio.

### Opción C — Auditar primero las vistas que nadie revisó
- Firebird, RBAC, incidencias, áreas/puestos, navigation-items, operaciones: más de la mitad
  de las 95 vistas están sin auditar.
- **Cuesta:** 1-2 sesiones de solo lectura.
- **Gana:** evita "terminar la UI" con medio sistema sin mirar.
- **Riesgo:** ninguno; pero no produce mejora visible.

## Recomendación

**A, luego B, y C en paralelo cuando haya hueco.** Los quick wins compran credibilidad y son
independientes entre sí; el sistema de componentes es lo que hace barato lo demás; la
auditoría pendiente puede correr en cualquier momento porque es solo lectura.

Lo que **no** haría: empezar por las oleadas 2-4 (80 vistas) antes de endurecer los
componentes. Se paga el mismo trabajo dos veces.

## Qué necesito de ti

Confirmación de A, y una decisión adicional que no debe tomar el implementador: **hay dos
sistemas de drawer** (`x-drawer` y el CSS de `app.css:898`). ¿Cuál se queda?

## Nota de coordinación

Las pantallas de dispositivos y huellas están en reparación (D-001). Su UI se toca **después**
de que el backend quede resuelto, no en paralelo: dos agentes sobre los mismos archivos es
conflicto garantizado.
