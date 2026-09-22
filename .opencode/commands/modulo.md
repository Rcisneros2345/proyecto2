---
description: Ciclo completo de mejora de un módulo (uso /modulo DISPOSITIVOS)
agent: team-lead
---
Trabaja el módulo **$ARGUMENTS** completo.

!`git status --short`
!`git log --oneline -5`

Secuencia, sin saltarte pasos:

1. Baseline reciente (`php scripts/baseline.php`) y checkpoint de git.
2. `auditor` → `.ui-work/00-auditoria/$ARGUMENTS.md`
3. `arquitecto` → hallazgos priorizados
4. `decisiones` → **preséntame opciones y espera mi respuesta** (riesgo MEDIO/ALTO siempre)
5. Especialista según el tema: `backend` · `db-mysql` · `db-firebird` · `zkteco` ·
   `academia` · `rbac` · `ui-orchestrator`
6. `impacto` → revisar y arreglar consumidores afectados
7. `qa` → casos límite y pruebas nuevas
8. `route-safety` → dictamen
9. `revisor` → veredicto
10. `janitor` → basura y cuarentena
11. `documentador` → bitácora y decisiones
12. Propón el mensaje de commit. **Nunca hagas push.**

Actualiza `.ui-work/ESTADO.md` tras cada paso, en una línea.
