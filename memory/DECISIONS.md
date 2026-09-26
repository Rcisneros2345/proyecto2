# Technical Decisions

No se borra este archivo: es historial de arquitectura. Decisión superada se marca, no se elimina.
Tags permitidos: `#bug` `#arquitectura` `#deuda-tecnica` `#seguridad` `#rendimiento` `#decision`

## 2026-08-23 — #arquitectura #decision Migración a Catálogo Central de Empleados (multi-dispositivo)
- Decisión: Desacoplar a los empleados del `device_id` directo y mover la relación a una tabla pivote `device_employee`.
- Motivo: Los empleados y sus huellas digitales necesitan ser asignados a múltiples relojes checadores en el campus en lugar de pertenecer exclusivamente a un dispositivo físico.
- Alcance: Migraciones `2026_08_23_000001_create_device_employee_table.php` a `2026_08_23_000004_drop_legacy_columns_from_employees.php`, modelos `Employee`, `Device` y servicios de sincronización.
- Verificación: Procedimiento documentado en `deploy.sh` con respaldo previo de base de datos.

## 2026-09-05 — #arquitectura #seguridad #decision Sincronización desacoplada de Firebird (solo lectura)
- Decisión: Conectar a Firebird 2.5 como origen de datos de solo lectura y reflejar la información académica/personal en tablas intermedias MySQL con seguimiento de auditoría (`firebird_syncs`, `firebird_sync_items`).
- Motivo: Aislar la base de datos institucional legacy de modificaciones accidentales y asegurar alto rendimiento para el sistema web de asistencias.
- Alcance: Configuración en `config/database.php` y migraciones `2026_09_05_*`.
- Verificación: Auditoría y trazabilidad por lote en `firebird_sync_items`.

## 2026-09-26 — #arquitectura #decision Inicialización de memoria persistente con Universal Dev Team
- Decisión: Implementar el protocolo Universal Dev Team organizando la memoria en `memory/` con división por dominios y protocolo de contexto mínimo.
- Motivo: Reducir drásticamente el consumo de tokens entre sesiones y evitar re-investigación de stack, esquemas y convenciones.
- Alcance: Directorio `memory/` con los 8 archivos troncales.
- Verificación: Existencia y contenido de `memory/INDEX.md`, `PROJECT.md`, `DECISIONS.md`, `TASK_STATE.md`, `KNOWN_ISSUES.md`, `CONVENTIONS.md`, `SECURITY_NOTES.md` y `DB_SCHEMA_NOTES.md`.
