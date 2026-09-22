---
agente: impacto
modulo: <MODULO>
fecha: <AAAA-MM-DD>
estado: COMPLETADO
---

## Cambio original
N archivos (`git diff <base>..HEAD`): …

## Consumidores detectados: N

| Consumidor | Tipo | ¿Afectado? | Acción |
|---|---|---|---|
| resources/views/…:212 | @include | SÍ, esperaba $items | corregido |
| tests/Feature/…Test.php:41 | prueba | SÍ, assert sobre texto viejo | actualizado |
| resources/views/…:88 | mismo partial | no usa esa variable | verificado |

## Arreglado
- `archivo:línea` — qué y por qué

## No arreglado (fuera de alcance)
- `[GAP: BACKEND]` …
- `[BLOQUEADO: ROUTE SAFETY]` …

## Referencias dinámicas revisadas a mano
- `view($x)` en … → verificado / no aplica

## Verificación
referencias ✔ · rutas sin cambios ✔ · `php artisan test --compact`: … ✔
