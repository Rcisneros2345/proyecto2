---
description: Detecta y retira a cuarentena la basura de la sesión (modo seco primero)
agent: janitor
subtask: true
---
!`php scripts/limpiar.php`
!`git status --porcelain`

1. Dime qué se movería y qué archivos **rastreados** sospechosos NO tocaste.
2. Lista la depuración encontrada con `archivo:línea`.
3. Para cualquier código que propongas retirar, incluye las cinco búsquedas de verificación,
   incluida la de referencias dinámicas.
4. Solo tras mi confirmación: `php scripts/limpiar.php --apply`.

Escribe el reporte en `.ui-work/05-reportes/limpieza-$ARGUMENTS.md` con cómo revertir.
