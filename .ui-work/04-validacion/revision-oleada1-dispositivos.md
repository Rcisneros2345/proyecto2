---
agente: revisor
modulo: DISPOSITIVOS / HUELLAS (Oleada 1 — 3 métodos destructivos)
fecha: 2026-09-19
estado: APROBADO CON OBSERVACIONES
---

# Revisión: Oleada 1 completa — clearAttendance, removeFromDevice, deleteFingerprint

## Diff revisado

| Archivo | Cambio |
|---|---|
| `app/Http/Controllers/DeviceSyncController.php` | +28/-13 — `clearAttendance` stub → lógica real; borrado `queueSync` (privado); import `ZktecoConnectionException`; quitado `use DB`; formato Pint |
| `app/Http/Controllers/FingerprintController.php` | +62/-39 — `removeFromDevice` y `deleteFingerprint` stub → lógica real + fix de firma; borrados `assignFingerprint`, `uploadFingerprints`, `fingerprints` |
| `tests/Feature/DeviceSyncControllerTest.php` | +34/-2 — `test_clear_attendance` → success + failure + connection_error |
| `tests/Feature/FingerprintControllerTest.php` | +94/-6 — delete/remove → success + failure + connection_error; `test_delete_fingerprint` corregido de ruta equivocada |
| `routes/web.php` | **NO MODIFICADO** (verificado: no aparece en el diff) |

Nota: el árbol de trabajo contiene además cambios de Fase 0 (scripts de verificación,
`ContratoDeRutasTest`, `DEUDA-TECNICA.md`, `ESTADO.md`) que **no son de Oleada 1** — ver
observación 6.

## Verificación

```
== Comparación de rutas ==
Baseline: 182 rutas   |   Actual: 182 rutas
Contrato de rutas INTACTO.

== Verificación de referencias cruzadas ==
REFERENCIAS ROTAS (1) — BLOQUEAN EL COMMIT
  [X] resources/views/welcome.blade.php:28  route('register') NO EXISTE
   (preexistente, DT-29 — no lo causa este diff; la otra rota conocida,
    view('employee.fingerprints'), quedó ELIMINADA por esta Oleada)

php artisan test --compact --filter=DeviceSyncControllerTest
  Tests:    9 passed (16 assertions)
php artisan test --compact --filter=FingerprintControllerTest
  Tests:    10 passed (22 assertions)

vendor/bin/pint --dirty
  PASS  14 files
```

`php scripts/verificar.php` queda en rojo por 3 causas, **todas preexistentes y ajenas a
Oleada 1** (ver observación 7): referencia rota de `welcome.blade.php`, 7 pruebas que fallan
por tabla `horario_laborals` ausente en la BD de testing y por los 2 fallos conocidos de
contrato, y Pint que ya quedó limpio tras `--dirty`.

## Checklist

1. **Contrato de rutas** — Las 7 rutas afectadas conservan nombre, URI, método y parámetros:
   `devices.sync-users`, `devices.sync-fingerprints`, `devices.sync-attendances`,
   `devices.sync-all`, `devices.clear-attendance` (web.php:180-188),
   `devices.employees.remove` (web.php:185), `employees.delete-fingerprint` (web.php:208).
   `comparar_rutas.php`: 182 = 182. ✔
2. **Firmas de controller** — `clearAttendance(Device $device)`,
   `removeFromDevice(Device $device, Employee $employee)`,
   `deleteFingerprint(Employee $employee, Fingerprint $fingerprint)`: los tres usan
   route-model-binding y coinciden con los parámetros de ruta. ✔
3. **Patrón consistente** — Los 3 métodos usan `app(ZktecoService::class, ['device' => $device])`
   (constructor `ZktecoService.php:50` acepta `Device`), try/catch con JSON success/error,
   `status: 'completed'` en éxito y `status: 'error'` + 500 en fallo. ✔
4. **Manejo de errores** — `catch (ZktecoConnectionException)` antes que `catch (Throwable)`
   en los 3 métodos. Orden correcto y alcanzable: `ZktecoService::boot()` (líneas 125-163)
   lanza `ZktecoConnectionException` **fuera** del try/catch interno de
   `clearAttendance()`/`removeUserFromDevice()`/`removeFingerprint()`, así que propaga al
   controller. ✔
5. **Tests** — Cada método tiene success + failure + exception:
   clearAttendance (3), removeFromDevice (3), deleteFingerprint (3). Los mocks SÍ espiarían:
   Laravel cierra Mockery en teardown vía `InteractsWithTestCaseLifecycle.php:124-136`
   (verificado en vendor), así que `->once()` se verifica y un stub que no llamara al
   servicio fallaría con `InvalidCountException`. Los `->with(...)` de
   `removeUserFromDevice(1)` y los `Mockery::on(...)` de `removeFingerprint` comprueban
   argumentos reales. ✔
6. **Borrados** — `queueSync` (privado), `assignFingerprint`, `uploadFingerprints`
   (controller), `fingerprints` (vista inexistente): sin consumidores. Verificado con grep
   en `app/ routes/ resources/ tests/` y `git log -S` (solo aparecen en el commit inicial,
   nunca tuvieron implementación ni ruta). Las rutas que sí existen apuntan a métodos que
   siguen vivos (`uploadFingerprintsOnDevice`, `copyFingerprint`). ✔
7. **Alcance** — Los cambios de Oleada 1 están confinados a los 4 archivos. `routes/web.php`
   no se tocó. El árbol mezcla Fase 0 (ver observación 6). ✔
8. **Formato** — `vendor/bin/pint --dirty` pasa limpio (14 archivos). ✔

## Consumidores revisados

| Consumidor | Tipo | ¿Afectado? | Acción |
|---|---|---|---|
| `resources/views/devices/show.blade.php:404` | form POST `devices.clear-attendance` | NO | `data-confirm` (`app.js:402-416`) hace `form.submit()` y no lee el JSON; el cambio `cleared`→`completed` no rompe nada |
| `resources/views/devices/show.blade.php:298` | form DELETE `devices.employees.remove` | NO | Ídem; mensaje de confirmación coherente con la operación |
| `resources/views/employees/edit.blade.php:451` | form DELETE `employees.delete-fingerprint` | NO | Ídem |
| `app/Services/ZktecoService.php:786,926,1021` | métodos del servicio | NO | Firmas coinciden con las llamadas del controller |
| `app/Services/SobranteService.php:284` | `removeUserFromDevice` | NO | Usa el mismo servicio; no pasa por el controller |
| JS (`resources/js/*`) | — | NO | Ningún handler lee `status: 'cleared'/'removed'/'deleted'` (grep verificado) |

## Bloqueantes

Ninguno.

## Observaciones (no bloqueantes → deuda)

1. **`removeFromDevice` no elimina el pivot local** (`FingerprintController.php:79-116`).
   El patrón establecido en `SobranteService.php:257-262,284-330` es
   `removeUserFromDevice()` + `DeviceEmployee::delete()`. El controller solo hace la parte
   hardware; el docblock de `ZktecoService::removeUserFromDevice()` (líneas 920-925) dice
   explícitamente "La persistencia local debe manejarla el caller" y el caller no la maneja.
   Consecuencia: el empleado sigue en `device_employee`, la lista del dispositivo lo sigue
   mostrando y un sync-to-devices posterior lo re-enrolaría. El mensaje de la UI
   (`devices/show.blade.php:302`) promete "perderá sus accesos en él". → Decidir en Oleada 2:
   borrar el pivot tras éxito en hardware, o documentar como intencional.
2. **`deleteFingerprint` no elimina el registro local** (`FingerprintController.php:30-66`).
   `ZktecoService::removeFingerprint()` (786-825) solo quita la huella del hardware; el
   registro `Fingerprint` local queda. La vista (`employees/edit.blade.php:454`) dice "Se
   eliminará el dedo X del empleado y del checador de origen" — el dedo sigue en la UI y un
   sync lo re-subiría. Misma decisión que la 1.
3. **`test_assign_fingerprint` y `test_copy_fingerprint` mal nombrados y mal enrutados**
   (`FingerprintControllerTest.php:62-94`): postean a `devices.sync-fingerprints` y afirman
   `status => queued`; no prueban assign ni copy (métodos borrados). Preexistentes (el diff
   solo les cambió formato), pero el nombre engaña y duplican cobertura de
   `test_sync_fingerprints`. → Renombrar o eliminar.
4. **Caminos 404 sin test**: `removeFromDevice` con empleado no registrado
   (`FingerprintController.php:84-89`) y `deleteFingerprint` con huella sin dispositivo
   (`FingerprintController.php:35-40`) no tienen prueba. → Cobertura.
5. **`use App\Models\DeviceSync;` sin usar** (`DeviceSyncController.php:11`): solo aparece en
   un docblock. → Limpieza menor.
6. **Proceso — D-001 sigue PROPUESTA**: `.ui-work/02-decisiones/D-001-controllers-stub.md`
   tiene `estado: PROPUESTA, decidido_por: —` y `ESTADO.md` lo lista como
   "[REQUIERE DECISIÓN]". La Oleada 1 implementó 3 métodos y borró 4 sin que la decisión
   quede registrada como tomada. El encargo humano de esta revisión implica autorización,
   pero hay que actualizar el registro (marcar D-001 decidida o abrir D-004 con el alcance
   de Oleada 1). Además, el árbol mezcla Fase 0 (scripts, ContratoDeRutasTest, docs) con
   Oleada 1: conviene commit separado.
7. **Verificación en rojo por causas preexistentes** (no bloquean Oleada 1, pero impiden
   cerrar el módulo hasta resolverlas):
   - `welcome.blade.php:28 route('register')` — preexistente (DT-29).
   - 5× `AttendanceFilterTest` + `DashboardRenderTest`: tabla `horario_laborals` no existe
     en la BD de testing (error 1146, `AttendanceController.php:54`) — ajeno a Oleada 1.
   - 2× `ContratoDeRutasTest`: los 2 fallos conocidos documentados en `ESTADO.md` (ruta
     vendor `_boost/browser-logs` + `api/user` sin nombre).
   - `ZktecoSyncTest > users sync without duplicates`: pasa aislado (17 passed); falla solo
     en suite completa por estado de BD compartida — preexistente/flaky.

## Veredicto

**APROBADO CON OBSERVACIONES** — los 3 métodos destructivos están bien implementados,
acotados y con pruebas que realmente espiarían la invocación. Nada de esto bloquea la
Oleada 2. Las observaciones 1-2 son deuda funcional que conviene decidir pronto (el
"borrar del dispositivo" queda a medias sin la persistencia local); la 3-5 son deuda de
calidad; la 6-7 son de proceso y preexistentes. Pasar 1-5 a `docs/DEUDA-TECNICA.md`.