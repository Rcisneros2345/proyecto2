---
description: Revisión final del diff con derecho de veto
agent: revisor
subtask: true
---
!`git diff HEAD`
!`git status --short`
!`php scripts/verificar.php`

Aplica tu lista completa: alcance, contrato, **impacto** (¿se revisó cada consumidor o solo
el primero?), calidad y datos. Busca con `rg` quién más usa lo tocado; no aceptes
"sin impacto" sin evidencia.

Cierra con APROBADO / APROBADO CON OBSERVACIONES / CAMBIOS REQUERIDOS en
`.ui-work/04-validacion/revision-$ARGUMENTS.md`.
