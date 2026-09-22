# 01 · Arquitectura

## Dónde vive cada cosa

| Tipo de código | Ubicación en este repo |
|---|---|
| Orquestación HTTP y nada más | `app/Http/Controllers` (+ `Academia/`) |
| Validación | `app/Http/Requests` |
| Autorización | `app/Policies` + middleware `RequireModulePermission` |
| Lógica de dominio | `app/Services` |
| Lectura de Firebird y sync | `app/Services/FirebirdReader.php`, `app/Services/SyncStrategies/` |
| Hardware ZKTeco | `app/Services/ZktecoService.php`, `EmployeeDeviceSyncService` |
| Trabajo lento o con hardware | `app/Jobs` (en cola) |
| Tareas operativas | `app/Console/Commands` (deben llamar al mismo Service que el controller) |
| Presentación | `resources/views` + `resources/views/components` + `app/View/Components` |
| Composición de layout | `app/View/Composers/AdminLayoutComposer.php` |

## Servicios que ya existen (no los dupliques)

`CicloActualService` · `HorarioResolver` · `KardexCalculator` · `PersonaContratosResolver`
`PermissionResolver` · `EmployeeDeviceSyncService` · `EmployeeCatalogMover` · `SobranteService`
`FirebirdReader` · `SyncStrategies\{FullSync, CatalogSmartSync, CycleDirectSync, CustomSync}`

Antes de escribir un servicio nuevo: `rg "class .*Service" app/Services` y lee el que más
se parezca. La falla más cara de esta base es tener dos implementaciones de lo mismo que
luego divergen.

## Señales de que algo está mal ubicado

- Controller de más de ~120 líneas o con más de 5 dependencias.
- Controller que abre sockets, lee Firebird o arma reportes.
- Vista Blade que consulta la base de datos.
- La misma consulta en tres pantallas.
- Un comando de consola con lógica que el controller repite.
- Un endpoint que ejecuta trabajo pendiente "por su cuenta", compitiendo con el worker
  (caso real: `FirebirdController::executePending()`).

## Aislamiento obligatorio

Hardware y Firebird se aíslan detrás de una interfaz para poder probarlos con dobles.
Ninguna prueba toca un equipo real ni una base Firebird real.

## Al proponer un refactor

Justifica con un problema observado, con `archivo:línea`. Nada de "sería más limpio con
un Repository". Si la arquitectura actual es imperfecta pero funciona: documenta la deuda
en `docs/DEUDA-TECNICA.md` y sigue. Si hay más de un camino razonable, va a `decisiones`.
