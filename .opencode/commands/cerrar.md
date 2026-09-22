---
description: Cierra un módulo: verificación, limpieza, documentación y mensaje de commit
agent: team-lead
---
Cierra el módulo **$ARGUMENTS**.

!`php scripts/verificar.php`
!`git status --short`
!`git diff --stat HEAD`

No cierres si falta algo:
- [ ] `verificar.php` en verde (pega la salida)
- [ ] `impacto` corrido y cada consumidor afectado revisado o escalado
- [ ] `route-safety`: APROBADO
- [ ] `qa`: PASA, con prueba que falla si se revierte el cambio
- [ ] `revisor`: APROBADO
- [ ] `janitor`: sin basura ni depuración en el diff
- [ ] decisión registrada en `.ui-work/02-decisiones/` y bitácora actualizada
- [ ] `.ui-work/ESTADO.md` refleja la realidad

Si todo se cumple, propón el commit y **espera mi confirmación**. Nunca hagas push.

    <modulo>: <qué cambió en una línea>

    Motivo: ...
    Decisión: D-NNN
    Contrato público: sin cambios | detalle
    Impacto: N consumidores revisados, N ajustados
    Pruebas: ...
    Revertir: git revert <sha>
