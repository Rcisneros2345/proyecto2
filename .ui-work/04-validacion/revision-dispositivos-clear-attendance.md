---
agente: revisor
modulo: DISPOSITIVOS (clearAttendance)
fecha: 2026-09-19
estado: APROBADO CON OBSERVACIONES
---

# Revisión: clearAttendance en DeviceSyncController

## Diff revisado

- `app/Http/Controllers/DeviceSyncController.php` — +28/-2
  - `use App\Exceptions\ZktecoConnectionException;` (import nuevo)
  - `clearAttendance()` (líneas 208-235): stub `['status' => 'cleared']` → lógica real
    con `app(ZktecoService::class, ['device' => $device])`, `if ($service->clearAttendance())`,
    catch `ZktecoConnectionException` y catch `Throwable`.
- `tests/Feature/DeviceSyncControllerTest.php` — +34/-2
  - `test_clear_attendance` → `test_clear_attendance_success` + `test_clear_attendance_failure`
    con `Mockery::mock(ZktecoService::class)` y `app()->bind(...)`.

No se tocó `routes/web.php` ni ningún otro método del controller.

## Verificación

```
== Comparación de rutas ==
Baseline: 182 rutas   |   Actual: 182 rutas
Contrato de rutas INTACTO.

== Verificación de referencias cruzadas ==
Archivos: 280  |  Rutas con nombre: 181  |  route() dinámicas no verificables: 2
REFERENCIAS ROTAS (2) — BLOQUEAN EL COMMIT
  [X] resources/views/welcome.blade.php:28  route('register') NO EXISTE
  [X] app/Http/Controllers/FingerprintController.php:74  view('employee.fingerprints') NO EXISTE
```

Las 2 referencias rotas son **preexistentes** (archivos no tocados por este diff) → van a
`.ui-work/HALLAZGOS-EXTRA.md`, no bloquean este cambio.

```
php artisan test --compact tests/Feature/DeviceSyncControllerTest.php
  Tests:    8 passed (12 assertions)
```

## Puntos verificados

1. **Contrato de ruta** — `routes/web.php:188`:
   `Route::post('/{device}/clear-attendance', [DeviceSyncController::class, 'clearAttendance'])->name('clear-attendance');`
   intacta, dentro del grupo `module_permission:dispositivos,sync` + `throttle:30,1`.
   `comparar_rutas.php`: 182 = 182. ✔

2. **Patrón vs syncNow** — `DeviceController.php:364-389` usa `new ZktecoService($device)` →
   `$service->setTime(...)` → JSON. `clearAttendance` usa `app(ZktecoService::class, ['device' => $device])`
   → `$service->clearAttendance()` → JSON. Mismo patrón; `app()` con parámetros es incluso
   mejor para testabilidad. Constructor `__construct(protected Device $device)`
   (`ZktecoService.php:50`) acepta ambos. ✔

3. **Manejo de errores** — `boot()` (`ZktecoService.php:125-163`) lanza y re-lanza
   `ZktecoConnectionException` (líneas 138, 151, 155). `clearAttendance()` (`ZktecoService.php:1021-1042`)
   llama a `$this->boot()` **fuera** de su try/catch interno, así que la excepción de conexión
   sí propaga al controller. El catch de `ZktecoConnectionException` (línea 224) es alcanzable
   y va antes que `Throwable` (línea 229). Orden correcto. ✔

4. **Test realmente espiaría** — `shouldReceive('clearAttendance')->once()` se verifica en
   teardown vía `Mockery::close()` (`vendor/.../InteractsWithTestCaseLifecycle.php:130`).
   Si el controller volviera a ser stub sin llamar al servicio, el test fallaría con
   `InvalidCountException`. ✔

5. **Mock** — `app()->bind(ZktecoService::class, fn () => $mock)` intercepta
   `app(ZktecoService::class, ['device' => $device])`: Laravel invoca el closure con
   ($container, $parameters) y el closure ignora los parámetros y devuelve el mock.
   Confirmado empíricamente: los 2 tests pasan. ✔

6. **Alcance** — El diff toca solo los 2 archivos. Sin reformateo masivo, sin cambios de
   rutas, sin tocar otros métodos. ✔

## Consumidores revisados

| Consumidor | Tipo | ¿Afectado? | Acción |
|---|---|---|---|
| `resources/views/devices/show.blade.php:404` | form POST a `devices.clear-attendance` | NO | El handler `data-confirm` (`resources/js/app.js:402-416`) solo intercepta el submit y hace `form.submit()`; no lee el JSON. El cambio de `status: 'cleared'` → `status: 'completed'` no rompe nada. |
| `app/` (grep `clear-attendance`) | — | NO | Sin otras referencias. |

## Observaciones (no bloqueantes)

1. `DeviceSyncController.php:229` — `catch (Throwable $e)` no usa `$e` (mensaje genérico).
   Consistente con el patrón de `syncUsers`/`syncAll`, pero el catch de conexión sí expone
   `$e->getMessage()`. Inconsistencia menor.
2. No hay test para la ruta `ZktecoConnectionException` (fallo de conexión). Los 2 tests
   cubren éxito y retorno `false`; el camino de excepción queda sin cubrir. Sugerencia:
   añadir `test_clear_attendance_connection_error` con `shouldReceive('clearAttendance')->andThrow(...)`.
3. Referencias rotas preexistentes (ver salida arriba): `welcome.blade.php:28` y
   `FingerprintController.php:74`. No las causa este diff; registrar en HALLAZGOS-EXTRA.md.

## Veredicto

**APROBADO CON OBSERVACIONES** — el cambio es correcto, acotado y seguro para continuar
con la Oleada 1. Las observaciones 1-2 son mejoras de consistencia/cobertura; la 3 es
preexistente y se trackea aparte.