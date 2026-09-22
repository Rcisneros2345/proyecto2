# Acta de cierre de fusión RHges + laravelrelojnew

Fecha de auditoría: 2026-09-16  
Estado: **parcialmente verificable; requiere evidencia histórica externa para cierre definitivo**

## Alcance y arquitectura confirmada

- La aplicación operativa del checkout es Laravel/Blade/Bootstrap.
- MySQL es la base operativa primaria.
- Firebird se usa mediante `FirebirdReader` con consultas de lectura (`SELECT`, metadatos y conteos); no se detectaron escrituras hacia Firebird.
- Las estrategias activas son `CatalogSmartSync`, `CycleDirectSync`, `CustomSyncStrategy` y `FullSyncStrategy`.
- `sync_all` conserva las fases separadas: primero catálogos base y después ciclo/alumnos.
- La ejecución manual reclama cada sync mediante transición atómica `pending -> running`; el job encolado no vuelve a ejecutar un sync ya reclamado.

## Conteos actuales en MySQL

Estos son conteos del checkout/base consultada el 2026-09-16. Los conteos “antes” de la fusión no están versionados en este repositorio.

| Tabla | Antes de fusión | Después / actual | Evidencia |
|---|---:|---:|---|
| `employees` | No disponible | 287 | Consulta MySQL |
| `profesores` | No disponible | 229 | Consulta MySQL |
| `alumnos` | No disponible | 7,948 | Consulta MySQL |
| `grupos` | No disponible | 147 | Consulta MySQL |
| `materias` | No disponible | 4,432 | Consulta MySQL |
| `cursos` | No disponible | 139 | Consulta MySQL |
| `horarios_det` | No disponible | 3,842 | Consulta MySQL |
| `modules` | No disponible | 24 | Consulta MySQL |
| `permissions` | No disponible | 87 | Consulta MySQL |
| `permission_groups` | No disponible | 5 | Consulta MySQL |

## Validación automatizada

- Suite Feature: **180 pruebas pasan, 577 aserciones; 1 prueba marcada risky por PHPUnit, 0 fallos**.
- La matriz RBAC de Academia verifica acciones `create/update/delete` y distingue operador sin grupo, usuario con grupo y administrador.
- Las pruebas de notificaciones usan el contrato real HTML de `/notifications`.

## Artefactos legacy solicitados

| Elemento | Resultado en este checkout | Decisión/evidencia requerida |
|---|---|---|
| `resync_3_tables_v3.php` | No localizado | Confirmar fuera del checkout si se ejecutó; adjuntar log, fecha y conteos afectados. |
| `rh-dashboard2.php` | No localizado | Confirmar si fue reemplazado por el dashboard Laravel y conservar referencia al commit/origen. |
| Tablas `*_bak` | No localizadas en archivos del repositorio | Confirmar directamente en MySQL si existen; documentar retención, respaldo y autorización de eliminación. |
| Comparación final de conteos | No existe como artefacto versionado | Generar una comparación firmada entre origen Firebird, destino MySQL y fecha de corte. |

## Decisiones de integridad

- No se debe borrar Firebird ni convertirlo en base operativa.
- No se debe fusionar la lógica de `syncCiclo()` y `syncCatalogos()`; se mantienen como fases separadas.
- La ejecución manual de pendientes reclama la fila `pending -> running`; el job encolado abandona si otro proceso ya reclamó el sync.
- Las claves naturales se normalizan con `trim` y mayúsculas antes de comparar.

## Criterio para declarar cierre definitivo

El acta pasa de “parcialmente verificable” a “cerrada” cuando se anexen: conteos históricos antes/después, evidencia de ejecución de `resync_3_tables_v3.php` o su sustituto, inventario de tablas backup, checksum/commit de la migración y validación del responsable de datos.
