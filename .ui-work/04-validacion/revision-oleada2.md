# Revisión · Oleada 2 (setTime, restore, uploadFingerprintsOnDevice, copyFingerprint)

- Fecha: 2026-09-20
- Revisor: revisor (veto final)
- Diff revisado: `git diff 3e19264..HEAD` (commit 7d93d07)
- Veredicto: **CAMBIOS REQUERIDOS**

## Diff revisado

| Archivo | +/- |
|---|---|
| `.ui-work/02-decisiones/D-004-copy-fingerprint-estado-local.md` | +40 |
| `.ui-work/ESTADO.md` | +53 / -48 |
| `app/Http/Controllers/DeviceSyncController.php` | +58 |
| `app/Http/Controllers/FingerprintController.php` | +105 |
| `tests/Feature/DeviceSyncControllerTest.php` | +126 |
| `tests/Feature/FingerprintControllerTest.php` | +246 |

## Salida de `php scripts/verificar.php`

```
──────── 1/7 Rutas resolubles        [ok] 182 rutas listadas
──────── 2/7 Contrato de rutas       [ok] contrato intacto (182 = 182)
──────── 3/7 Referencias cruzadas    [FALLA] welcome.blade.php:28 route('register') NO EXISTE (preexistente)
──────── 4/7 Impacto                 [ok] revisar con impacto.php
──────── 5/7 Estilo (Pint)           [FALLA] 293 archivos, 163 issues (preexistente; los 4 archivos de la Oleada 2 pasan Pint)
──────── 6/7 Depuración y basura     [ok]
──────── 7/7 Pruebas                 [FALLA] 9 failed, 1 risky, 194 passed (preexistente; los 30 tests de la Oleada 2 pasan)
VERIFICACIÓN EN ROJO: 3 problema(s)
```

Los 3 problemas de la puerta son **preexistentes** (ninguno fue introducido por la Oleada 2; ya documentados en `.ui-work/ESTADO.md` como DT-46 y "referencias rotas: 1"). Los 30 tests de la Oleada 2 pasan: `php artisan test --compact tests/Feature/DeviceSyncControllerTest.php tests/Feature/FingerprintControllerTest.php` → `30 passed (77 assertions)`.

## Bloqueantes

### B1 — Vista de copiar huella rota: envía `device_id`, el controlador valida `target_device_id`
- `resources/views/employees/edit.blade.php:440` — `<select name="device_id" ...>` (consumidor existente, no tocado por la Oleada 2)
- `app/Http/Controllers/FingerprintController.php:30` — `$request->validate(['target_device_id' => ['required', 'exists:devices,id']])`
- Antes de la Oleada 2, `copyFingerprint` era un stub que retornaba `['status' => 'copied']` sin validar nada. Ahora valida `target_device_id` y la vista sigue enviando `device_id`. **El formulario de copiar huella de `employees/edit` siempre responde 422** ("target_device_id es requerido").
- Los tests pasan porque envían `target_device_id` en el body (`tests/Feature/FingerprintControllerTest.php:266`), pero el consumidor real está roto. No hay JS que renombre el campo (verificado: `rg "copy-fingerprint|target_device_id" resources/js` → sin coincidencias).
- Fix mínimo: `resources/views/employees/edit.blade.php:440` → `name="target_device_id"` (1 línea). Alternativa: aceptar ambos nombres en el controlador. Decidir y aplicar antes de cerrar la Oleada 2.

## No bloqueantes (pasan a `docs/DEUDA-TECNICA.md`)

- **NB1** — Los tests de `copyFingerprint` no verifican que el servicio se instancie con el dispositivo DESTINO. `app()->bind(ZktecoService::class, fn () => $mock)` ignora el parámetro `['device' => $targetDevice]` (`tests/Feature/FingerprintControllerTest.php:262`). El código lo hace bien (`FingerprintController.php:51`), pero la prueba no lo cubre.
- **NB2** — Los tests de `copyFingerprint` no verifican argumentos de `uploadFingerprint`: `shouldReceive('uploadFingerprint')->once()` sin `with()` (`tests/Feature/FingerprintControllerTest.php:261,317,368`). No se comprueba que reciba el `Employee` y `Fingerprint` correctos.
- **NB3** — `setTime` valida solo `['required', 'string']` (`DeviceSyncController.php:200-202`). Un string no parseable ("abc") pasa la validación; `strtotime("abc")` = `false` → `date('Y', false)` = 1970 → el dispositivo se pondría en 1970-01-01. El formulario de la vista usa `datetime-local` con `required`, pero un request manual podría enviar basura. Sugerencia: validar con `date_format` o equivalente.
- **NB4** — `.ui-work/ESTADO.md` dice "Commits Oleada 2: pendiente de commit (post-revert de D-004: A→B)" pero el commit `7d93d07` ya existe. Documentación desactualizada tras el commit final.

## Verificado y correcto

- **Patrón try/catch**: los 4 métodos usan `app(ZktecoService::class, ['device' => $device])`, `ZktecoConnectionException` primero, `Throwable` después, JSON `status => completed|error`. Consistente con `clearAttendance` (Oleada 1).
- **copyFingerprint**: `DB::transaction()` (`FingerprintController.php:55`), `updateOrCreate` (`:56`), recalcula `fingerprint_count` (`:68-73`), valida `target_device_id` required+exists (`:30`), misma_device → 422 (`:42-47`), servicio instanciado con el DESTINO (`:51`). Confirmado que `ZktecoService::uploadFingerprint` usa `$this->device` como destino (`app/Services/ZktecoService.php:686-691`). Implementación fiel a D-004 opción B.
- **uploadFingerprintsOnDevice**: solo hardware, sin escrituras MySQL, recibe `Device $device` y `Employee $employee` de la ruta (`FingerprintController.php:154`). La vista `devices/show.blade.php:291` pasa `[$device, $employee]` — consistente.
- **restore**: solo hardware, mismo patrón que `clearAttendance` (`DeviceSyncController.php:266-293`).
- **setTime**: valida `datetime` (422 si falta) (`DeviceSyncController.php:200-202`), solo hardware. El formato `datetime-local` de la vista ("2026-01-15T10:30") es parseable por `strtotime` en `Util::encodeTime` (`vendor/coding-libs/zkteco-php/src/Libs/Services/Util.php:91`).
- **Rutas**: contrato intacto (182 = 182, `comparar_rutas.php`). Ninguna ruta cambió de nombre, URI, método o permiso. `uploadFingerprintsOnDevice` cambió de firma `(Device, Request)` → `(Device, Employee)` pero la ruta ya tenía `{employee}` y la vista ya pasaba ambos — sin consumidores rotos.
- **D-004**: `DECIDIDA` con opción B (`decidido_por: team-lead`). La implementación sigue los 3 pasos de la decisión.
- **Tests**: cada método tiene success + failure + connection_error; `copyFingerprint` además missing_target_device y same_device; `setTime` además missing_datetime. Usan Mockery mock con `shouldReceive()->once()` y `Mockery::close()` en `tearDown`.

## Consumidores revisados

| Método | Ruta | Vista consumidora | Estado |
|---|---|---|---|
| `setTime` | `devices.set-time` | `devices/show.blade.php:394` (envía `datetime`) | OK |
| `restore` | `devices.restore` | `devices/show.blade.php:413` (sin body) | OK |
| `uploadFingerprintsOnDevice` | `employees.upload-fingerprints` | `devices/show.blade.php:291` (pasa `[$device, $employee]`) | OK |
| `copyFingerprint` | `employees.copy-fingerprint` | `employees/edit.blade.php:438` (envía `device_id`) | **ROTO (B1)** |

## Conclusión

La implementación de los 4 métodos es sólida, sigue el patrón de la Oleada 1 y respeta D-004. Pero la Oleada 2 rompió el único consumidor real de `copyFingerprint` (la vista de edición de empleado), que ahora siempre responde 422. Ese desajuste de contrato vista↔controlador es bloqueante. Los 3 problemas de la puerta de calidad son preexistentes y ya están documentados; no bloquean la Oleada 2 en sí, pero la puerta no quedará en verde hasta que se atiendan por separado.