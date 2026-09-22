# Equipo de agentes — proyecto RH + Academia + RBAC

Fusión de lo que ya tenías (7 agentes de UI, `.ui-work/`, `MODEL_MAP.md`, tiers de modelos)
con un equipo completo para el resto del proyecto: backend, MySQL, Firebird, ZKTeco,
academia, permisos, pruebas y revisión.

Tres cosas nuevas responden a lo que pediste:

1. **`decisiones`** — la IA deja de preguntarte "¿qué hago?" y te trae 2-4 opciones con
   costo, riesgo, reversibilidad y una recomendación. Tú eliges.
2. **`impacto`** — después de cada cambio, busca a todos los que dependen de lo tocado, los
   revisa y los arregla. Con un script real detrás, no con buena voluntad.
3. **Scripts de verificación** — el contrato público deja de depender de que un modelo
   diga "no toqué las rutas".

---

## Qué contiene

```
AGENTS.md                  tu bloque de Laravel Boost intacto + reglas del proyecto
opencode.json              tus 7 agentes tal cual + 14 nuevos + permisos duros globales
DIAGNOSTICO.md             ★ lo que encontré revisando tu repo (léelo)
INSTALACION.md             cómo fusionarlo sin pisar lo tuyo

.opencode/agents/          21 agentes
  team-lead ★              orquesta todo el proyecto
  decisiones ★             opciones comparadas para que decidas tú
  auditor ★                inventario end-to-end backend + vistas
  arquitecto ★             estructura, duplicación, acoplamiento
  backend ★                Laravel/PHP
  db-mysql ★               esquema, índices, N+1, migraciones
  db-firebird ★            lectura y estrategias de sincronización
  zkteco ★                 dispositivos, huellas, asistencias
  academia ★               ciclos, horarios, kárdex
  rbac ★                   módulos, permisos, menú
  impacto ★                revisa y arregla lo que tu cambio afectó
  qa ★                     rompe cosas y escribe pruebas
  revisor ★                veto final sobre el diff
  documentador ★           bitácora, decisiones, docs
  ui-orchestrator          (tuyo, actualizado)
  ui-auditor               (tuyo, sin cambios)
  ui-designer              (tuyo, sin cambios)
  ui-implementer           (tuyo + protocolo de impacto)
  code-fixer               (tuyo + arreglar a los afectados)
  route-safety             (tuyo + scripts de verificación)
  janitor                  (tuyo + script de cuarentena)

.opencode/commands/        /baseline /auditar /decidir /impacto /rutas
                           /verificar /modulo /revisar /limpiar /cerrar
.ai/guidelines/            13 guías, cargadas bajo demanda
scripts/                   baseline · comparar_rutas · verificar_referencias
                           impacto · verificar · limpiar   (PHP puro, corren en Windows)
ia.cmd                     atajo: ia baseline | impacto | verificar | limpiar
tests/Feature/ContratoDeRutasTest.php
docs/                      RUTAS-DEPRECADAS · DEUDA-TECNICA · BITACORA · DECISIONES
.ui-work/                  MODEL_MAP ampliado + PREGUNTAS + HALLAZGOS-EXTRA + plantillas
```

★ = nuevo

---

## Los seis scripts

Los prompts son instrucciones; los scripts son hechos. Un agente puede decir que revisó las
rutas. Un diff de rutas no miente.

| Script | Qué hace |
|---|---|
| `baseline.php` | Congela rutas, **claves de permiso**, vistas, componentes y assets. Valida que sea JSON antes de guardar y escribe UTF-8 |
| `comparar_rutas.php` | Rutas desaparecidas, renombradas, URI/método/parámetro cambiado, y **permisos retirados de una ruta** |
| `verificar_referencias.php` | `route()`, `view()`, `@include`, `<x-…>`, `asset()` rotos; URLs a mano; vistas huérfanas; permisos de ruta sin rastro en seeders |
| `impacto.php` ★ | Deduce qué expone lo que cambiaste y lista **quién lo consume**, con archivo:línea |
| `verificar.php` | Puerta de calidad: rutas resolubles · contrato · referencias · impacto · Pint · depuración y basura · pruebas |
| `limpiar.php` | Mueve basura a `.ui-work/_cuarentena/`; nunca borra, nunca toca archivos rastreados |

---

## Cómo se usa

```bash
opencode

/baseline                    # congela el contrato (primero de todo)
/auditar DISPOSITIVOS        # entender antes de tocar
/decidir orden de sync_all   # te traigo opciones, eliges tú
/modulo DISPOSITIVOS         # ciclo completo con puertas humanas
/impacto DISPOSITIVOS        # qué quedó afectado y arreglarlo
/verificar                   # puerta de calidad
/cerrar DISPOSITIVOS         # commit propuesto, nunca push
```

Para algo rápido, sin pipeline:

```
@backend agrega el filtro por área en el listado de empleados.
Lee antes .ai/guidelines/07-no-romper.md y al terminar corre impacto.
```

---

## El flujo

```
                   checkpoint git
                        │
                   baseline.php
                        │
                  ┌─ auditor ─┐
                  │           │
             arquitecto     rbac / seguridad
                  │           │
                  └─ decisiones ─→  TÚ ELIGES (2-4 opciones)
                        │
   ┌──────┬─────────────┼──────────┬─────────┬──────────┐
backend  db-mysql  db-firebird  zkteco   academia   ui-orchestrator
   └──────┴─────────────┼──────────┴─────────┴──────────┘
                        │
                    impacto ★         ← revisa y arregla a los consumidores
                        │
                       qa
                        │
                  route-safety
                        │
                     revisor
                        │
                     janitor
                        │
                  documentador
                        │
                   verificar.php  →  commit
```

---

## Qué cambió respecto a tu configuración

| Antes | Ahora |
|---|---|
| 7 agentes, todos de UI | 21: los tuyos intactos + backend, datos, dominio, calidad |
| `default_agent: ui-orchestrator` | `team-lead` (Tab te devuelve a `ui-orchestrator` cuando el trabajo sea visual) |
| Baseline de rutas roto (UTF-16 con un error dentro) | `baseline.php` valida y escribe UTF-8; si artisan falla, se detiene |
| `route-safety` comparaba a ojo | Compara con script, incluidas las claves de permiso |
| Sin análisis de impacto | `impacto` + script + guía 10, obligatorio antes de cerrar |
| Decisiones implícitas del agente | `decisiones` con opciones, costo y reversibilidad |
| Permisos por agente | Igual, más denegación global de `git push`, `reset --hard`, `clean`, `rm -rf`, `migrate:fresh/reset/rollback`, `composer update` |
| Reglas solo en los prompts | Reglas + `.ai/guidelines/` cargadas por `instructions` en cada sesión |
| `docs/` con 4 archivos | + RUTAS-DEPRECADAS, DEUDA-TECNICA (ya poblada desde tu auditoría), BITACORA, DECISIONES |

---

## Dónde está el límite honesto

Los modelos free de Zen que estás usando son suficientes para inventariar, editar Blade y
buscar consumidores. **No son suficientes para razonar solos sobre sincronización o
permisos.** Por eso el diseño no confía en el modelo: confía en los permisos de OpenCode,
en los scripts y en que las decisiones de riesgo las tomas tú.

Para `revisor` y `decisiones` en módulos críticos (Firebird, huellas, RBAC), vale la pena
un modelo grande aunque sea de pago: son pocas llamadas y son justo las que más daño evitan
cuando fallan.

Empieza por `DIAGNOSTICO.md`, sigue con `INSTALACION.md`, y haz el primer ciclo completo en
`AUTH_RBAC`, que es donde tienes mejor cobertura de pruebas para verificar que el pipeline
funciona.
