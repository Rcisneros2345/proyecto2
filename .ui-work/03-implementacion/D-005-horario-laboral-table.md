# D-005: Explicitar $table en HorarioLaboral

**Fecha:** 2026-09-20
**Agente:** backend

## Qué se hizo
- Agregado `protected $table = 'horarios_laborales';` en `app/Models/HorarioLaboral.php:15`
- Creado test Feature `tests/Feature/AttendancePageTest.php` con 2 pruebas

## Por qué
Sin `$table` explícito, Eloquent pluraliza `HorarioLaboral` → `horarios_laborals` (con 's').
La tabla real en MySQL es `horarios_laborales` (con 'es'). Cada consulta al modelo fallaba
silenciosamente o devolvía 0 filas contra la tabla correcta.

## Archivos modificados
| Archivo | Cambio |
|---|---|
| `app/Models/HorarioLaboral.php` | +1 línea: `protected $table = 'horarios_laborales';` |
| `tests/Feature/AttendancePageTest.php` | Archivo nuevo: 2 pruebas |

## Riesgo
Bajo. El `$table` explícito alinea el modelo con la tabla real. Todos los consumidores
(14 referencias a HorarioLaboral, 11 a `employee()`, 2 a `diaNombre()`) usan el modelo
vía Eloquent y no dependen del nombre de tabla inferido.

## Cómo revertir
Eliminar la línea `protected $table = 'horarios_laborales';` del modelo y borrar el test.

## Verificación
- `vendor/bin/pint --dirty` → PASS
- `php artisan test --compact --filter=AttendancePageTest` → 2 passed
- `php artisan test --compact --filter=AttendanceFilterTest` → 5 passed
- `php artisan test --compact --filter=DashboardRenderTest` → 6 passed
- `php scripts/impacto.php` → 27 referencias, 0 afectadas
- `php scripts/verificar.php` → route('register') en welcome.blade.php (pre-existente, no relacionado)
