# Rutas deprecadas

Toda ruta reemplazada se registra aquí. La vieja **sigue funcionando** hasta que exista una
tarea aprobada para retirarla.

| Ruta vieja | Ruta nueva | Fecha del cambio | Motivo | Retiro propuesto | Estado |
|---|---|---|---|---|---|
| — | — | — | — | — | — |

## Procedimiento
1. Agregar la nueva; conservar la vieja (mismo controller o `Route::redirect()`).
2. Actualizar referencias internas: `rg -n "route\('vieja'" resources/ app/ tests/`
3. Registrar aquí · 4. `php scripts/baseline.php` · 5. Retiro en tarea aparte, aprobada.
