# Deuda técnica

Consolidado del 2026-09-19 a partir de: `auditoria_completa.md` (09-16),
`.ui-work/00-auditoria/auditoria-completa-2026-09-19.md`,
`auditoria-bases-datos-2026-09-19.md`, `auditoria-ui-ux-2026-09-19.md` y la verificación con
scripts del 09-19.

Estados: ABIERTA · EN CURSO · CERRADA · DESCARTADA (con motivo)

## Crítica — el sistema afirma cosas falsas

| ID | Descripción | Evidencia | Impacto | Responsable | Decisión | Estado |
|---|---|---|---|---|---|---|
| DT-20 | ~~7 métodos stub en FingerprintController~~ Todos implementados o borrados | Oleada 1+2 completadas | 0 stubs enrutados | zkteco | D-001 | CERRADA |
| DT-21 | ~~setTime, clearAttendance, restore stub~~ Todos implementados | Oleada 1+2 completadas | 0 stubs enrutados | zkteco | D-001 | CERRADA |
| DT-22 | `FingerprintController::fingerprints()` devolvía `view('employee.fingerprints')`, inexistente | FingerprintController.php (borrado en Oleada 1) | Método eliminado — sin consumidores | zkteco | D-001 | CERRADA |

## Alta — datos que se pierden o se exponen

| ID | Descripción | Evidencia | Impacto | Responsable | Decisión | Estado |
|---|---|---|---|---|---|---|
| DT-03 | Claves naturales sin normalizar frente a `utf8mb4_unicode_ci` + `INSERT IGNORE`: la fila omitida no deja rastro | CatalogSmartSync::buildIdentityKey() | Duplicados lógicos y omisiones silenciosas | db-mysql | **CERRADA** — fix: `Normalizer::normalize()` + `preg_replace('/\pM+/u', '', ...)` en `buildIdentityKey()` | — |
| DT-04 | `executePending()` sin claim atómico; borra jobs por `LIKE` del payload serializado | FirebirdController | Doble ejecución y borrado equivocado de jobs | db-firebird | — | ABIERTA |
| DT-48 | `sesiones_base.receso`: booleano PHP `false` → PDO `''` → MySQL strict mode rechaza; 7 syncs fallidos (ids 24-30), 3 sesiones de receso perdidas | CatalogSmartSync.php:503-507,834 | **Fix aplicado**: booleanos convertidos a '0'/'1' en safeVal() y líneas 503-507; también arregla el hallazgo safeVal(false)='' vs safeVal(0)='0' | db-mysql | — | ABIERTA |
| DT-23 | ~~7 rutas autenticadas sin module_permission~~ 5 cerradas con module_permission (D-002 B) | D-002 B: sobrantes→dispositivos,view; firebird→firebird,view | Admin bypass en 2 capas | rbac | D-002 | CERRADA |
| DT-24 | `x-data-table` renderiza HTML con `{!! !!}` (ciclos) | app.css / data-table.blade.php | Riesgo de XSS | seguridad | — | ABIERTA |

## Media — funcionamiento y consistencia

| ID | Descripción | Evidencia | Impacto | Responsable | Estado |
|---|---|---|---|---|---|
| DT-49 | Syncs stuck en status `running` para siempre: sin reclaim, sin timeout. Si el request muere tras claim, el sync queda bloqueado | FirebirdController::executePending() | Syncs del mismo ciclo pueden competir; cola bloqueada | db-firebird | ABIERTA |
| DT-01 | `sync_all` corre el ciclo antes que los catálogos | FullSyncStrategy::execute() | Falla por FK en base vacía; datos académicos incompletos | db-firebird | ABIERTA |
| DT-02 | `syncCatalogTableChunked()` carga todo MySQL en memoria | CatalogSmartSync | Pico de memoria, caída del worker | db-mysql | ABIERTA |
| DT-05 | `CURSOS_DET` con `clave_asignatura` ambigua frente a `materias (clave_asignatura, id_plan)` | migraciones académicas | Kárdex/curso incorrecto | academia | ABIERTA |
| DT-06 | Paginación no universal (`get()`/`all()` en listados grandes) | varios controllers | Lentitud y memoria al crecer | backend | ABIERTA |
| DT-25 | `ALUMNOS_KARDEX` excluido del sync directo de `CycleDirectSync` | CycleDirectSync::TABLAS_ALUMNOS_CICLO | ¿De dónde salen los datos que muestra kárdex? Pregunta abierta | academia | ABIERTA |
| DT-26 | Sync de ALUMNOS sin filtro de ciclo | CycleDirectSync | Lentitud con volúmenes grandes | db-firebird | ABIERTA |
| DT-27 | 5 tablas sincronizadas que la app no usa: `empleados_cfghorarios(_det)`, `empleados_horarios`, `profesores_horarios(_det)` | auditoría BD §9.3 | Tiempo de sync y espacio gastados; posible necesidad futura del módulo de horarios | db-firebird | ABIERTA |
| DT-28 | `docentes_asistencias` y `grupo_asistencias` sin mapeo Firebird visible | auditoría BD §9.3 | Origen de datos desconocido | academia | ABIERTA |
| DT-29 | ~~Referencia rota: route('register') en vista legacy~~ welcome.blade.php borrado (scaffold muerto sin consumidores) | 4 busquedas de evidencia: 0 referencias | janitor | CERRADA |
| DT-42 | `removeFromDevice` no borra el pivot `device_employee` — el empleado queda en la lista local y un sync lo re-enrolaría | FingerprintController.php:79-116 | Estado local inconsistente con hardware | zkteco | CERRADA |
| DT-43 | `deleteFingerprint` no borra el registro local de `Fingerprint` — la huella queda en la UI y un sync la re-subiría | FingerprintController.php:30-66 | Estado local inconsistente con hardware | zkteco | CERRADA |
| DT-44 | `use App\Models\DeviceSync;` sin usar en DeviceSyncController | DeviceSyncController.php:11 | Import muerto | janitor | ABIERTA |
| DT-45 | `test_assign_fingerprint`/`test_copy_fingerprint` postean a `devices.sync-fingerprints`, no prueban assign/copy | FingerprintControllerTest.php:62-94 | Tests mal nombrados, no cubren lo que dicen | qa | ABIERTA |
| DT-46 | Vista `GET /attendances` lanza error 500 (6 tests AttendanceFilterTest afectados) | HorarioLaboral.php sin $table → tabla `horario_laborals` inexistente | **CERRADA** — D-005: `protected $table = 'horarios_laborales'` | backend | CERRADA |
| DT-47 | Oleada 2 validada solo contra mocks — pendiente prueba con dispositivo físico real | DeviceSyncControllerTest, FingerprintControllerTest | Bloqueante de despliegue, no de desarrollo | zkteco | ABIERTA |
| DT-50 | ~~Pint detectaba deuda de formato global en 312 archivos (151 incidencias al 2026-09-20)~~ Formato global corregido por lotes: app (144), database (94), tests (41) y scripts (7) | `vendor/bin/pint --test` y `scripts/verificar.php` en verde el 2026-09-20 | La puerta global vuelve a validar estilo sin modificar comportamiento | backend | CERRADA |

## Verificaciones de integración Oleada 1/2

| Verificación | Resultado | Fecha |
|---|---|---|
| B1: vista copyFingerprint envía `device_id` en vez de `target_device_id` | **CORREGIDO** (employees/edit.blade.php:440) | 2026-09-20 |
| qa: 7 métodos — names de inputs vs params de controller | **7/7 PASAN** — solo B1 era el bug real | 2026-09-20 |

## Baja — limpieza y UI

| ID | Descripción | Evidencia | Responsable | Estado |
|---|---|---|---|---|
| DT-07 | Faltan `docs/ui/UI_ROUTE_MAP.md` y `UI_COMPONENTS.md`, citados por route-safety y janitor | docs/ui | documentador | ABIERTA |
| DT-30 | La spec de UX está en la raíz como archivo `diseño` sin extensión: invisible para los agentes | raíz del repo | documentador | ABIERTA |
| DT-31 | Doble polling de KPIs (dos temporizadores pidiendo lo mismo) | app.js:69-165 · dashboard.blade.php:224-311 | ui-implementer | ABIERTA |
| DT-32 | Toast automático cada 30 s en dashboard | dashboard.blade.php:277 | ui-implementer | ABIERTA |
| DT-33 | Tabla de empleados sin `table-cards` (ilegible en móvil) | employees/index.blade.php | ui-implementer | ABIERTA |
| DT-34 | 165 líneas de menú muerto `@if(false)` en un layout de 460 | layouts/admin.blade.php:46-210 | ui-implementer | ABIERTA |
| DT-35 | Dos sistemas de drawer coexistiendo | drawer.blade.php · app.css:898 | ui-designer | ABIERTA (D-003) |
| DT-36 | Bootstrap cargado por CDN y por Vite a la vez | admin.blade.php:24-26 | ui-implementer | ABIERTA |
| DT-37 | Broadcasting inactivo (driver log/null); progreso por polling; sin listeners para `SyncProgressUpdated` | config + Console\Kernel | backend | ABIERTA |
| DT-38 | `removeUser()` deprecated aún presente en ZktecoService | ZktecoService | zkteco | ABIERTA |
| DT-39 | `.route-baseline.json` y `.route-current.json` en UTF-16 con volcado de error; sustituidos por `.ai/baseline/` | raíz del repo | janitor | ABIERTA |
| DT-40 | `.ui-work/parse_routes.php` y `routes_parsed.txt`: herramienta desechable de una sesión de IA | .ui-work | janitor | ABIERTA |
| DT-41 | `auditoria-completa-2026-09-18.md` (51 KB) superada por la del 09-19; dos inventarios del mismo sistema | .ui-work/00-auditoria | janitor | ABIERTA |

## Descartadas (verificadas como falsas el 2026-09-19)

| ID | Hallazgo original | Por qué se descarta |
|---|---|---|
| DT-X1 | "API routes `api/academia/*` sin auth:sanctum ni permisos" | `routes/web.php:126` — están dentro de `auth` y `module_permission:academia,view`. Son rutas web para AJAX, no API de sanctum |
| DT-X2 | "Áreas y Puestos sin permisos RBAC" | `routes/web.php:228-232` — tienen `->middleware('admin')`. No es un hueco; es una inconsistencia de criterio (ver D-002) |

> Regla que originó esta sección: un hallazgo de auditoría no es un hecho hasta que se
> verifica en el código. Antes de abrir tarea, se comprueba.
