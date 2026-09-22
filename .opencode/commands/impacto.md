---
description: Revisa y arregla todo lo que quedó afectado por los cambios actuales
agent: impacto
subtask: true
---
Analiza el impacto de los cambios en curso.

!`git diff --name-only HEAD`
!`php scripts/impacto.php`

Procedimiento obligatorio (`@.ai/guidelines/10-impacto.md`):

1. Clasifica cada archivo cambiado y confirma los símbolos públicos que expone.
2. Por cada consumidor de la lista: **ábrelo** y comprueba que sigue recibiendo lo que espera.
3. Añade a mano lo que el script no ve: `view($var)`, `@include($vista)`, nombres construidos
   por concatenación, URLs armadas en JS.
4. Arregla lo que el cambio rompió, con el mínimo. Lo que ya estaba roto va a
   `.ui-work/HALLAZGOS-EXTRA.md`.
5. Si algo exige tocar rutas → `[BLOQUEADO: ROUTE SAFETY]`. Si exige backend fuera de tu
   ámbito → `[GAP: BACKEND]` con el detalle exacto.
6. Si aparecen más de 10 consumidores afectados, **detente** y dilo: el cambio estaba mal
   dimensionado.

Verifica al final con `verificar_referencias`, `comparar_rutas` y `php artisan test --compact`,
y escribe `.ui-work/04-validacion/impacto-$ARGUMENTS.md`.
