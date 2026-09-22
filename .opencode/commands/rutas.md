---
description: Dictamen del contrato público de rutas, permisos y referencias
agent: route-safety
subtask: true
---
Dictamina sobre los cambios actuales.

!`php scripts/comparar_rutas.php`
!`php scripts/verificar_referencias.php`
!`git diff --stat`

Recuerda que aquí también es contrato: nombres de vista y componente, y las claves
`module_permission:<modulo>,<accion>`. Un permiso retirado de una ruta deja la ruta más
abierta que antes: eso es BLOQUEO.

Cierra con **APROBADO**, **APROBADO CON CONDICIONES** o **BLOQUEADO**, con la lista de
consumidores verificados. Ante la duda, BLOQUEADO.
