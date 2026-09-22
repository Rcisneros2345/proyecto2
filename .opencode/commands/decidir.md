---
description: Convierte un problema en opciones comparadas para que yo decida (uso /decidir orden de sync_all)
agent: decisiones
subtask: true
---
Tema a decidir: **$ARGUMENTS**

!`git log --oneline -5`

Antes de proponer nada, verifica el problema en el código y revisa si ya se decidió antes
en `.ui-work/02-decisiones/`, `docs/` o `auditoria_completa.md`.

Entrégame el formato de `@.ai/guidelines/11-decisiones.md`:
2 a 4 opciones reales (incluida "no hacerlo todavía" si es legítima), con costo en archivos
y líneas, qué gana, qué se rompe, si es reversible y cómo se deshace. Cierra con tu
recomendación en dos frases y **una sola pregunta concreta** para mí.

Guárdalo en `.ui-work/02-decisiones/D-<NNN>-<tema>.md` con estado PROPUESTA.
