---
description: Auditoría end-to-end de un módulo (uso /auditar DISPOSITIVOS)
agent: auditor
subtask: true
---
Audita el módulo **$ARGUMENTS**.

!`php artisan route:list --json`
!`php scripts/verificar_referencias.php`

Produce `.ui-work/00-auditoria/$ARGUMENTS.md` con el trazado completo:

    ruta (nombre) [middleware de permiso] → Controller@accion → FormRequest → Policy
    → Service/Query → Modelo → Vista → componentes → assets → prueba

Reglas: no modificas código; cada afirmación con `archivo:línea`; lo no verificado se marca
`NO VERIFICADO`. Cierra con las 5 zonas de mayor riesgo y las preguntas abiertas.
