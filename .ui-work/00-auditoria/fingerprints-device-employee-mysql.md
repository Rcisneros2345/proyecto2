# Auditoría MySQL — Fingerprint y DeviceEmployee: cascadas antes de borrado local

Fecha: 2026-09-19
Módulo: DISPOSITIVOS / HUELLAS
Propósito: verificar si `Fingerprint::delete()` y `DeviceEmployee::delete()` locales
disparan cascadas destructivas antes de agregarlos a `FingerprintController`.

## Método

- Migraciones leídas: `2024_01_01_000004_create_fingerprints_table`,
  `2026_08_20_000004_make_employees_and_fingerprints_global`,
  `2026_08_23_000002_add_device_id_to_fingerprints`,
  `2026_08_23_000001_create_device_employee`,
  `2026_09_09_000001_add_ignored_at_to_device_employee`,
  `2024_01_01_000003_create_attendances` y sus 4 migraciones posteriores,
  `2026_08_24_120000_create_device_sync_items`.
- Esquema real verificado contra MySQL vía `information_schema` (no solo migraciones).
- Búsqueda de FKs entrantes: `KEY_COLUMN_USAGE` + `REFERENTIAL_CONSTRAINTS`.
- Búsqueda de relaciones Eloquent: `Fingerprint.php`, `Pivots/DeviceEmployee.php`,
  `Attendance.php`, `Device.php`, `Employee.php`, `DeviceSyncItem.php`.
- Búsqueda de consumidores: `SyncEmployeeToDeviceJob.php`, `SobranteService.php`,
  `EmployeeController.php`, `ZktecoService.php`, vistas.

## Esquema real observado

### fingerprints
| Columna | Tipo | FK | OnDelete |
|---|---|---|---|
| id | bigint PK | — | — |
| employee_id | bigint | employees.id | **CASCADE** |
| device_id | bigint NULL | devices.id | **SET NULL** |
| finger | tinyint | — | — |
| template | blob | — | — |
| template_hash | varchar(64) | — | — |
| created_at / updated_at | timestamp | — | — |

Índices: único `(employee_id, finger, device_id)`; índice `(template_hash)`.
Sin `deleted_at`.

### device_employee
| Columna | Tipo | FK | OnDelete |
|---|---|---|---|
| id | bigint PK | — | — |
| device_id | bigint | devices.id | **CASCADE** |
| employee_id | bigint | employees.id | **CASCADE** |
| device_uid | int | — | — |
| role / card_number / password / active / fingerprint_count / ignored_at | varios | — | — |

Índices: únicos `(device_id, device_uid)` y `(device_id, card_number)`; índice `(employee_id)`.
Sin `deleted_at`.
**No existe constraint único `(device_id, employee_id)`** — hoy no hay duplicados
(verificado: 0 filas en el GROUP BY), pero la unicidad que la app asume no está impuesta.

### attendances
| Columna | Tipo | FK | OnDelete |
|---|---|---|---|
| id | bigint PK | — | — |
| device_id | bigint | devices.id | **CASCADE** |
| employee_id | bigint NULL | employees.id | **SET NULL** |
| user_id | varchar | **sin FK** | — |
| state / type / attendance_type / source / horas | varios | — | — |
| recorded_at | datetime | — | — |

**No tiene `fingerprint_id` ni `device_employee_id`.** `user_id` es el PIN global del
empleado (string), no el `device_uid` del pivot. La resolución PIN→empleado ocurre en
sync vía el pivot (`ZktecoService.php:422`), pero la fila de asistencia no depende de él.

## FKs entrantes (quién apunta a estas tablas)

Consulta ejecutada contra `information_schema`:

```sql
SELECT kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.CONSTRAINT_NAME,
       kcu.REFERENCED_TABLE_NAME, rc.DELETE_RULE, rc.UPDATE_RULE
FROM information_schema.KEY_COLUMN_USAGE kcu
JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
  ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
 AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
WHERE kcu.REFERENCED_TABLE_NAME IN ('fingerprints', 'device_employee')
  AND kcu.TABLE_SCHEMA = DATABASE()
ORDER BY kcu.REFERENCED_TABLE_NAME, kcu.TABLE_NAME;
```

Resultado (única fila):

| TABLE_NAME | COLUMN_NAME | CONSTRAINT_NAME | REFERENCED_TABLE | DELETE_RULE |
|---|---|---|---|---|
| device_sync_items | fingerprint_id | device_sync_items_fingerprint_id_foreign | fingerprints | **SET NULL** |

**Ninguna tabla referencia `device_employee`.**

## Tabla de consecuencias

| Entidad | FKs salientes | OnDelete | Consecuencia de borrar |
|---|---|---|---|
| **Fingerprint** | `device_sync_items.fingerprint_id` → fingerprints | **SET NULL** | El historial de sync sobrevive: la fila de `device_sync_items` conserva `credential_type='fingerprint'`, `finger`, `status`, `message`; solo pierde el puntero a la huella. `attendances` NO se tocan (no hay FK). `device_employee.fingerprint_count` queda **stale** (caché que solo se refresca en `ZktecoService::syncFingerprintForEmployee`, líneas 599-604). Si el hardware aún tiene la huella, el próximo sync la re-crea (`updateOrCreate` por device_id+employee_id+finger, `ZktecoService.php:571`). |
| **DeviceEmployee** | **ninguna** | — | Cero cascadas. `attendances` intactas (`employee_id` sobrevive vía FK a employees con SET NULL; `user_id` es string sin FK). `fingerprints` intactas (su FK es a employees/devices, no al pivot). Si el dispositivo aún tiene al usuario, el próximo sync re-crea el pivot (`ZktecoService.php:403`, attach) y las nuevas asistencias vuelven a resolverse. |

## Respuestas a las preguntas específicas

1. **¿Borrar un Fingerprint borra asistencias en cascada?** No. `attendances` no tiene
   `fingerprint_id`. El único efecto es `device_sync_items.fingerprint_id → NULL`
   (historial conservado) y `fingerprint_count` desincronizado hasta el próximo sync.

2. **¿Borrar un pivot device_employee borra fingerprints en cascada?** No. Las huellas
   dependen de `employees` y `devices`, no del pivot. Nada referencia `device_employee`.

3. **¿Hay datos que SOLO existan si el fingerprint/pivot existe?**
   - `device_sync_items.fingerprint_id`: el puntero se pierde (SET NULL), pero la traza
     del sync queda en `credential_type + finger + status + message`. No se pierde
     historial.
   - `device_employee.fingerprint_count`: caché derivado, se recalcula en sync.
   - Kárdex/reportes: dependen de `employees` + `attendances`, no de estas tablas.
   - `SyncEmployeeToDeviceJob.php:120` usa `fingerprint_id` solo como puntero de traza;
     el procesamiento usa `finger` + la colección de huellas, no el id. Un item con
     `fingerprint_id NULL` no rompe el job.

## Recomendación

**Borrado local (hard delete) es seguro desde integridad referencial.** No hay cascadas
destructivas: borrar Fingerprint solo hace SET NULL en `device_sync_items`; borrar
DeviceEmployee no toca nada. No se requiere soft delete: el único histórico que referencia
fingerprints sobrevive con SET NULL, y el patrón del proyecto ya hace hard delete del
pivot en sobrantes (`SobranteService::remove`, `EmployeeController::sobrantesRemove`).

Condiciones para implementarlo:

1. **Actualizar `fingerprint_count`** al borrar una huella (decrementar o recalcular el
   pivot `device_id + employee_id`), o la UI mostrará un conteo incorrecto
   (`employees/edit.blade.php:312`, `DeviceController.php:190`, `EmployeeController.php:163`).
2. **Orden hardware → local, y local SOLO si hardware confirmó** para `deleteFingerprint`:
   si el dispositivo está offline y se borra local, el próximo sync re-crea la huella
   (el usuario creerá que la borró y reaparecerá). El comportamiento actual (error si el
   hardware falla, sin borrado local) es correcto; el borrado local debe ir en la rama de
   éxito. Para `removeFromDevice` aplicar el mismo criterio (el patrón de sobrantes
   continúa con el borrado local aunque el hardware falle, pero ahí el dispositivo ya no
   tiene al usuario; en `removeFromDevice` el usuario SÍ está en el dispositivo).
3. **No hace falta migración** para el borrado en sí. Si se decide auditar el vínculo
   huella→sync más allá del SET NULL, ahí sí evaluar soft delete — no es necesario hoy.

## Hallazgo extra (fuera de alcance, no corregido)

- `device_employee` no tiene constraint único `(device_id, employee_id)`. Hoy no hay
  duplicados (verificado), pero la unicidad que la aplicación asume no está impuesta por
  la BD. Anotado para el reporte de unicidad, no para esta tarea.