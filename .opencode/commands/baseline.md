---
description: Congela el contrato público (rutas, permisos, vistas, componentes, assets) antes de trabajar
agent: build
---
Ejecuta y analiza:

!`php scripts/baseline.php`

Confirma cuántas rutas quedaron registradas y cuántas van sin `->name()`.
Si el comando falló porque `php artisan route:list` no devuelve JSON, eso es un **BLOQUEO**:
repórtalo antes que cualquier otra cosa y no sigas con ninguna otra tarea.

No modifiques archivos de la aplicación en este paso.
