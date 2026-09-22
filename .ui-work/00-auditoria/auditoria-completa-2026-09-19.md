# Auditoría Completa del Proyecto — 2026-09-19

> Generada por el agente `auditor`. Solo lectura; no se modificó ningún archivo de la aplicación.
> Inventario exhaustivo: esquema de BD, rutas, controllers, modelos, servicios, vistas, tests, RBAC, Firebird, ZKTeco.

---

## Índice

1. [Resumen ejecutivo](#1-resumen-ejecutivo)
2. [Stack tecnológico](#2-stack-tecnológico)
3. [Esquema de Base de Datos MySQL (TODAS las tablas)](#3-esquema-de-base-de-datos-mysql)
4. [Firebird (solo lectura)](#4-firebird-solo-lectura)
5. [Rutas (182)](#5-rutas)
6. [Controllers (29)](#6-controllers)
7. [Modelos y relaciones (40)](#7-modelos-y-relaciones)
8. [Servicios (15)](#8-servicios)
9. [Form Requests (6)](#9-form-requests)
10. [Policies (11)](#10-policies)
11. [Middleware (11)](#11-middleware)
12. [Vistas y componentes Blade (95 vistas, 8 componentes)](#12-vistas-y-componentes-blade)
13. [Assets JS/CSS/Vite](#13-assets-jscssvite)
14. [Jobs (5)](#14-jobs)
15. [Comandos Artisan (3)](#15-comandos-artisan)
16. [Eventos y Listeners](#16-eventos-y-listeners)
17. [RBAC / Permisos](#17-rbac--permisos)
18. [Seeders (5)](#18-seeders)
19. [Pruebas (38)](#19-pruebas)
20. [Configuración](#20-configuración)
21. [Scripts del proyecto](#21-scripts-del-proyecto)
22. [ZKTeco (dispositivos)](#22-zkteco-dispositivos)
23. [Hallazgos y deuda técnica](#23-hallazgos-y-deuda-técnica)

---

## 1. Resumen ejecutivo

| Dimensión | Cantidad |
|---|---|
| Rutas definidas | 182 |
| Controllers | 29 (8 en `Academia\`) |
| Modelos Eloquent | 40 (16 en `Academia\`) |
| Servicios | 15 (4 estrategias de sync) |
| Form Requests | 6 |
| Policies | 11 |
| Vistas Blade | 95 |
| Componentes Blade | 8 |
| Partials | 8 |
| Jobs | 5 |
| Comandos Artisan | 3 |
| Migraciones | 77 |
| Seeders | 5 |
| Tests (Feature+Unit) | 38 |
| Tablas MySQL | 33 |
| Assets JS | 4 archivos |
| Assets CSS | 1 archivo |

**Dominios:** Checador (empleados, dispositivos, huellas, asistencias) · Academia (ciclos, planes, cursos, grupos, alumnos, profesores, horarios, kárdex) · RBAC/plataforma (módulos, permisos, grupos, navegación, preferencias).

**Fuentes de datos:** MySQL (operativa), Firebird (solo lectura, fuente de verdad), ZKTeco (hardware real).

---

## 2. Stack tecnológico

| Componente | Versión |
|---|---|
| PHP | 8.3 |
| Laravel | 10.x |
| PHPUnit | 10.x |
| Pint | 1.x |
| Sanctum | 3.x |
| Bootstrap | 5.3.3 (CDN) |
| Bootstrap Icons | 1.11.3 (CDN) |
| Vite | laravel-vite-plugin |
| DB primaria | MySQL |
| DB secundaria | Firebird (PDO, solo lectura) |
| Hardware | ZKTeco (lib `codinglibs/zkteco-php`) |

---

## 3. Esquema de Base de Datos MySQL

### 3.1 `users`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| name | varchar(255) | NO | — | |
| email | varchar(255) | NO | — | |
| email_verified_at | timestamp | SÍ | NULL | |
| password | varchar(255) | NO | — | |
| remember_token | varchar(100) | SÍ | NULL | |
| role | varchar(20) | NO | 'operador' | CHECK: admin, operador, viewer |
| username | varchar(50) | SÍ | NULL | Único |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** PK `id`, UNIQUE `users_email_unique`, UNIQUE `users_username_unique`
**FK:** Ninguna

### 3.2 `employees`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| user_id | varchar(50) | NO | — | PIN/badge único global (no FK a users) |
| name | varchar(255) | SÍ | NULL | |
| type | varchar(20) | NO | 'biometric' | biometric, admin, teacher |
| numero_empleado | varchar(20) | SÍ | NULL | EMPLEADOS.NUMEMPLEADO (Firebird) |
| clave_profesor | varchar(20) | SÍ | NULL | PROFESORES.CLAVEPROFESOR (Firebird) |
| departamento | varchar(100) | SÍ | NULL | |
| cargo | varchar(100) | SÍ | NULL | |
| contrato | varchar(50) | SÍ | NULL | |
| status_actual | char(1) | SÍ | NULL | A=Activo, B=Baja |
| fecha_ingreso | date | SÍ | NULL | |
| id_campus | varchar(10) | SÍ | NULL | FK→sedes.id_campus |
| nivel | varchar(20) | SÍ | NULL | |
| tarjeta_id | varchar(50) | SÍ | NULL | |
| area_id | bigint | SÍ | NULL | FK→areas.id |
| puesto_id | bigint | SÍ | NULL | FK→puestos.id |
| auth_user_id | bigint | SÍ | NULL | FK→users.id |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** PK `id`, UNIQUE `employees_user_id_unique`, INDEX `employees_auth_user_id_foreign`, INDEX `idx_employees_status_actual` (status_actual)
**FK:** `employees_area_id_foreign` → `areas(id)` ON DELETE SET NULL, `employees_puesto_id_foreign` → `puestos(id)` ON DELETE SET NULL, `employees_auth_user_id_foreign` → `users(id)` ON DELETE SET NULL

### 3.3 `devices`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| name | varchar(255) | NO | — | |
| ip | varchar(45) | NO | — | Único |
| port | smallint | NO | 4370 | |
| password | varchar(255) | SÍ | NULL | |
| serial_number | varchar(100) | SÍ | NULL | Único |
| device_name | varchar(255) | SÍ | NULL | |
| status | enum('online','offline','unknown') | NO | 'unknown' | |
| last_seen_at | timestamp | SÍ | NULL | |
| description | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** PK `id`, UNIQUE `devices_ip_unique`, UNIQUE `devices_serial_number_unique`, INDEX `devices_status_updated_idx` (status, updated_at)

### 3.4 `device_employee` (Pivote empleado-dispositivo)
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | NO | — | FK→devices.id |
| employee_id | bigint | NO | — | FK→employees.id |
| device_uid | int | NO | — | UID local en el checador |
| role | tinyint | NO | 0 | |
| card_number | varchar(50) | SÍ | NULL | |
| password | varchar(255) | SÍ | NULL | |
| active | tinyint | NO | 1 | |
| fingerprint_count | int | NO | 0 | Contador cacheado |
| ignored_at | timestamp | SÍ | NULL | Para sobrantes ignorados |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** PK `id`, UNIQUE `device_employee_device_id_device_uid_unique` (device_id, device_uid), UNIQUE `device_employee_device_id_card_number_unique` (device_id, card_number), INDEX `device_employee_employee_id_index` (employee_id)
**FK:** `device_employee_device_id_foreign` → `devices(id)` ON DELETE CASCADE, `device_employee_employee_id_foreign` → `employees(id)` ON DELETE CASCADE

### 3.5 `fingerprints`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | SÍ | NULL | FK→devices.id, NULL=legado |
| employee_id | bigint | NO | — | FK→employees.id |
| finger | tinyint | NO | — | 0-9 |
| template | longtext | NO | — | Plantilla biométrica |
| template_hash | varchar(64) | NO | — | SHA-256 |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** PK `id`, UNIQUE `fingerprints_device_employee_finger_unique` (device_id, employee_id, finger), INDEX `fingerprints_employee_id_index` (employee_id)
**FK:** `fingerprints_device_id_foreign` → `devices(id)` ON DELETE CASCADE, `fingerprints_employee_id_foreign` → `employees(id)` ON DELETE CASCADE

### 3.6 `attendances`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | NO | — | FK→devices.id |
| employee_id | bigint | SÍ | NULL | FK→employees.id, SET NULL on delete |
| user_id | varchar(50) | NO | — | PIN del checador |
| state | tinyint | NO | — | |
| type | tinyint | NO | 0 | |
| attendance_type | enum('biometric','manual') | NO | 'biometric' | |
| source | enum('zkteco','manual','import') | NO | 'zkteco' | |
| hora_salida | time | SÍ | NULL | |
| hora_salida_comer | time | SÍ | NULL | |
| hora_regreso_comer | time | SÍ | NULL | |
| recorded_at | datetime | NO | — | Timestamp del registro |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** PK `id`, UNIQUE `attendance_device_employee_unique` (device_id, employee_id, recorded_at), INDEX `att_device_recorded_idx` (device_id, recorded_at), INDEX `att_employee_recorded_id_idx` (employee_id, recorded_at, id), INDEX `att_state_recorded_idx` (state, recorded_at), INDEX `idx_attendances_type_recorded_at` (type, recorded_at), INDEX `idx_attendances_type_source` (attendance_type, source)
**FK:** `attendances_device_id_foreign` → `devices(id)` ON DELETE CASCADE, `attendances_employee_id_foreign` → `employees(id)` ON DELETE SET NULL

### 3.7 `device_syncs`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | NO | — | FK→devices.id |
| status | varchar(20) | NO | 'pending' | pending, running, completed, failed |
| operation | varchar(50) | NO | 'all' | all, users, attendances, fingerprints |
| stage | varchar(50) | SÍ | NULL | |
| employee_id | bigint | SÍ | NULL | Para sync de empleado individual |
| started_at | timestamp | SÍ | NULL | |
| finished_at | timestamp | SÍ | NULL | |
| processed | int | NO | 0 | |
| total | int | NO | 0 | |
| created_count | int | NO | 0 | |
| updated_count | int | NO | 0 | |
| error_message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**FK:** `device_syncs_device_id_foreign` → `devices(id)` ON DELETE CASCADE

### 3.8 `device_sync_items`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_sync_id | bigint | NO | — | FK→device_syncs.id |
| fingerprint_id | bigint | SÍ | NULL | FK→fingerprints.id |
| credential_type | varchar(20) | SÍ | NULL | |
| finger | tinyint | SÍ | NULL | |
| status | varchar(20) | NO | 'pending' | |
| attempts | tinyint | NO | 0 | |
| message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**FK:** `device_sync_items_device_sync_id_foreign` → `device_syncs(id)` ON DELETE CASCADE, `device_sync_items_fingerprint_id_foreign` → `fingerprints(id)` ON DELETE SET NULL

### 3.9 `firebird_syncs`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| status | varchar(20) | NO | 'pending' | pending, running, completed, failed |
| ciclo | varchar(20) | SÍ | NULL | Ej: 2025-2025-3 |
| strategy | varchar(50) | NO | 'full' | full, catalog_smart, cycle_direct, custom |
| started_at | timestamp | SÍ | NULL | |
| finished_at | timestamp | SÍ | NULL | |
| created_count | int | NO | 0 | |
| updated_count | int | NO | 0 | |
| deleted_count | int | NO | 0 | |
| processed | int | NO | 0 | |
| total | int | NO | 0 | |
| stage | varchar(50) | SÍ | NULL | |
| error_message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.10 `firebird_sync_items`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| firebird_sync_id | bigint | NO | — | FK→firebird_syncs.id |
| table_name | varchar(100) | NO | — | |
| record_id | varchar(100) | SÍ | NULL | |
| action | varchar(20) | NO | — | created, updated, deleted, skipped |
| status | varchar(20) | NO | 'pending' | |
| message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**FK:** `firebird_sync_items_firebird_sync_id_foreign` → `firebird_syncs(id)` ON DELETE CASCADE

### 3.11 `modules`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| slug | varchar(100) | NO | — | Único |
| name | varchar(255) | NO | — | |
| description | text | SÍ | NULL | |
| active | tinyint | NO | 1 | |
| icon | varchar(50) | SÍ | NULL | Bootstrap icon class |
| sort_order | int | NO | 0 | |
| is_system | tinyint | NO | 0 | |
| group_name | varchar(100) | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** UNIQUE `modules_slug_unique`

### 3.12 `permissions`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| module_id | bigint | NO | — | FK→modules.id |
| slug | varchar(150) | NO | — | Ej: academia.ciclos |
| action | varchar(50) | NO | — | view, create, update, delete, activo |
| name | varchar(255) | NO | — | |
| description | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** UNIQUE `permissions_slug_unique`, UNIQUE `permissions_module_id_action_unique` (module_id, action)
**FK:** `permissions_module_id_foreign` → `modules(id)` ON DELETE CASCADE

### 3.13 `permission_groups`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| name | varchar(255) | NO | — | |
| description | text | SÍ | NULL | |
| is_default | tinyint(1) | NO | 0 | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.14 `permission_group_permissions` (Pivote)
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| permission_group_id | bigint | NO | — | FK→permission_groups.id |
| permission_id | bigint | NO | — | FK→permissions.id |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**Índices:** UNIQUE `permission_group_permissions_permission_group_id_permission_id_unique`
**FK:** FK→`permission_groups(id)` ON DELETE CASCADE, FK→`permissions(id)` ON DELETE CASCADE

### 3.15 `employee_permission_groups` (Pivote)
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| employee_id | bigint | NO | — | FK→employees.id |
| permission_group_id | bigint | NO | — | FK→permission_groups.id |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**FK:** FK→`employees(id)` ON DELETE CASCADE, FK→`permission_groups(id)` ON DELETE CASCADE

### 3.16 `profesor_permission_groups` (Pivote)
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| profesor_clave_profesor | varchar(20) | NO | — | FK→profesores.clave_profesor |
| permission_group_id | bigint | NO | — | FK→permission_groups.id |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.17 `navigation_items`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| module_id | bigint | SÍ | NULL | FK→modules.id |
| section | varchar(100) | NO | — | Grupo visual en el menú |
| label | varchar(255) | NO | — | Texto mostrado |
| route_name | varchar(255) | NO | — | Nombre de ruta Laravel |
| icon | varchar(50) | SÍ | NULL | Bootstrap icon class |
| permission_action | varchar(50) | SÍ | NULL | Acción requerida |
| sort_order | int | NO | 0 | |
| admin_only | tinyint(1) | NO | 0 | |
| active | tinyint(1) | NO | 1 | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.18 `areas`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| identificador | varchar(100) | NO | — | Único |
| descripcion | varchar(255) | SÍ | NULL | |
| empleado_responsable_id | bigint | SÍ | NULL | FK→employees.id |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

**FK:** `areas_empleado_responsable_id_foreign` → `employees(id)` ON DELETE SET NULL

### 3.19 `puestos`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| identificador | varchar(100) | NO | — | Único |
| descripcion | varchar(255) | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.20 `incidencias`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| employee_id | bigint | NO | — | FK→employees.id |
| type | varchar(50) | NO | — | |
| description | text | SÍ | NULL | |
| date | date | NO | — | |
| status | varchar(20) | NO | 'pending' | pending, approved, rejected |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.21 `horarios_laborales`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| employee_id | bigint | SÍ | NULL | FK→employees.id |
| dia_semana | tinyint | NO | — | 1-7 |
| hora_entrada | time | NO | — | |
| hora_salida | time | NO | — | |
| tolerancia_min | tinyint | NO | 15 | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 3.22 Tablas Academia

#### `ciclos`
| Columna | Tipo | Notes |
|---|---|---|
| id | bigint | PK |
| inicial | int | Año inicio |
| final | int | Año fin |
| periodo | tinyint | 1, 2, 3 |
| descripcion | varchar(255) | |
| fecha_inicial | date | |
| fecha_final | date | |
| activo | tinyint | |

**UK:** (inicial, final, periodo)

#### `alumnos`
| Columna | Tipo | Notes |
|---|---|---|
| id | bigint | PK |
| numero_alumno | int | Único, fuente principal |
| matricula | varchar | |
| matricula_oficial | varchar | |
| paterno, materno, nombre | varchar | |
| curp | varchar(18) | Único |
| fecha_nacimiento | date | |
| sexo | enum | |
| nivel, grado, subnivel, turno | varchar/tinyint | |
| id_campus | varchar | FK→sedes |
| estatus | varchar | INSCRITO, BAJA, etc. |
| ... | | +20 columnas más |

**UK:** numero_alumno, curp. **Índices:** (estatus, nivel, turno), (paterno, materno, nombre)

#### `alumnos_grupos`
FK→`grupos(codigo_grupo,inicial,final,periodo)` ON DELETE CASCADE, FK→`alumnos(numero_alumno)` ON DELETE CASCADE

#### `alumnos_kardex`
FK→`materias(clave_asignatura)`, FK→`metodos_eval(id_eval)`, FK→`alumnos(numero_alumno)` ON DELETE CASCADE

#### `alumnos_cursos`
**UK compuesta:** (inicial, final, periodo, codigo_curso, numero_alumno, id_tipoeval, id_etapa, clave_asignatura, version, tipoexamen)

#### `alumnos_niveles`
FK→`alumnos(numero_alumno)` ON DELETE CASCADE

#### `alumnos_asistencias`
**UK:** (inicial, final, periodo, numero_alumno, codigo_grupo, clave_profesor, clave_asignatura, dia, sesion, fecha)

#### `grupos`
| Columna | Tipo | Notes |
|---|---|---|
| codigo_grupo | varchar | Parte de UK compuesta |
| inicial, final, periodo | int/tinyint | Ciclo |
| nivel, turno, grado | varchar | |
| inscritos | smallint | |
| ... | | |

**UK:** (codigo_grupo, inicial, final, periodo)

#### `cursos`
**UK:** (inicial, final, periodo, clave_curso). FK→grupos.

#### `cursos_det`
FK→`cursos(id)` ON DELETE CASCADE, FK→`materias(clave_asignatura)`
**UK:** (curso_id, clave_asignatura), (curso_id, inicial, final, periodo, codigo_curso, dia, hora_inicial, hora_final, id_campus, edificio, aula)

#### `materias`
| clave_asignatura (UK), nombre_asignatura, horas_teoria, horas_practica, creditos, ... |

#### `planes`
| id_plan (UK), nombre, ... FK→niveles |

#### `niveles`
| clave_nivel (UK), descripcion, ... |

#### `sedes`
| id_campus (UK), descripcion, ... |

#### `turnos`
| clave_turno (UK), descripcion, ... |

#### `profesores`
| clave_profesor (UK), nombre, paterno, materno, nombre_completo, ... FK→users(auth_user_id) |

#### `contratos`
| contrato (UK), descripcion, tipo_personal (enum), tiene_antiguedad, tiene_prestaciones, activo, ... |

#### `sesiones_base`
| nivel, turno, sesion (UK compuesta), hora_inicio, hora_fin, receso, orden, descripcion, ... |

#### `horarios_det`
FK→grupos, FK→profesores, FK→materias, FK→sedes, FK→sesiones_base
**Campos:** inicial, final, periodo, codigo_grupo, clave_profesor, clave_asignatura, dia, sesion, id_campus, edificio, aula, sesion, activo, origen_horario, ...

#### `metodos_eval`
| id_eval (UK), nombre_corto, descripcion, es_final, ... |

#### `docentes_asistencias`
Registro de asistencia de docentes por clase.

#### `grupo_asistencias`
Registro de asistencia de grupos.

### 3.23 Otras tablas

| Tabla | Descripción |
|---|---|
| `password_reset_tokens` | Tokens de reset de contraseña |
| `personal_access_tokens` | Tokens Sanctum |
| `failed_jobs` | Jobs fallidos |
| `jobs` | Cola dejobs |
| `job_batches` | Lotes de jobs |

---

## 4. Firebird (solo lectura)

### 4.1 Conexión
- **Config:** `config/database.php:95` — conexión `firebird` (driver firebird, DSN via `FIREBIRD_DSN`)
- **Reader:** `app/Services/FirebirdReader.php:12` — PDO directo con `getColumns()`, `fetchRows()`, `fetchRowsChunked()`, `countRows()`, `ping()`

### 4.2 Estrategias de sincronización
Todas implementan `SyncStrategyInterface::execute()`:

| Estrategia | Archivo | Descripción |
|---|---|---|
| `FullSyncStrategy` | `app/Services/SyncStrategies/FullSyncStrategy.php` | Sync completa de todas las tablas |
| `CatalogSmartSync` | `app/Services/SyncStrategies/CatalogSmartSync.php` | Solo tablas de catálogo, detecta cambios |
| `CycleDirectSync` | `app/Services/SyncStrategies/CycleDirectSync.php` | Sync directa de datos por ciclo |
| `CustomSyncStrategy` | `app/Services/SyncStrategies/CustomSyncStrategy.php` | Tablas específicas elegidas por el usuario |

### 4.3 Tablas Firebird → MySQL

| Firebird | MySQL | Mapeo |
|---|---|---|
| EMPLEADOS | employees | user_id, name, numero_empleado, departamento, cargo, contrato, status_actual, fecha_ingreso |
| PROFESORES | profesores | clave_profesor, nombre_completo, ... |
| ALUMNOS | alumnos | numero_alumno, nombre, paterno, materno, curp, ... |
| CICLOS | ciclos | inicial, final, periodo, descripcion |
| PLANES | planes | id_plan, nombre, nivel |
| MATERIAS | materias | clave_asignatura, nombre_asignatura |
| CURSOS | cursos | clave_curso, codigo_grupo, clave_asignatura, clave_profesor |
| CURSOS_DET | cursos_det | Detalle de cursos con horarios |
| GRUPOS | grupos | codigo_grupo, nivel, turno, grado |
| HORARIOS_DET | horarios_det | Horarios de clases |
| SESIONES_BASE | sesiones_base | Sesiones por nivel/turno |
| SEDES | sedes | Sedes/campus |
| TURNOS | turnos | Turnos escolares |
| NIVELES | niveles | Niveles educativos |
| METODOS_EVAL | metodos_eval | Métodos de evaluación |
| CONTRATOS | contratos | Tipos de contrato |

**Regla irrompible:** Firebird es SOLO LECTURA. Ninguna escritura, nunca.

---

## 5. Rutas

### 5.1 Dashboard
| Nombre | Método | URI | Controller | Permiso |
|---|---|---|---|---|
| `dashboard` | GET | `/` | `DashboardController@index` | Authenticate |
| `dashboard.kpisJson` | GET | `/kpis/json` | `DashboardController@kpisJson` | — |

### 5.2 Auth
| `login` | GET | `/login` | `AuthController@create` |
| `login.store` | POST | `/login` | `AuthController@store` |
| `logout` | POST | `/logout` | `AuthController@destroy` |

### 5.3 Empleados (20 rutas)
`employees.index`, `employees.create`, `employees.store`, `employees.search`, `employees.sobrantes`, `employees.sobrantes.data`, `employees.sobrantes.ignore`, `employees.sobrantes.unignore`, `employees.sobrantes.remove`, `employees.edit`, `employees.update`, `employees.destroy`, `employees.update-card`, `employees.enroll-device`, `employees.enrollment-diff`, `employees.delete-fingerprint`, `employees.copy-fingerprint`, `employees.sync-devices`, `employees.sync-progress`, `fingerprints.index`

### 5.4 Dispositivos (22 rutas)
`devices.index`, `devices.create`, `devices.store`, `devices.deduplicate`, `devices.show`, `devices.edit`, `devices.update`, `devices.destroy`, `devices.check-status`, `devices.progress`, `devices.refresh-data`, `devices.sync-now`, `devices.sync-status`, `devices.sync-all`, `devices.sync-users`, `devices.sync-attendances`, `devices.sync-fingerprints`, `devices.set-time`, `devices.clear-attendance`, `devices.restore`, `devices.employees.remove`, `devices.employees.upload-fingerprints`

### 5.5 Asistencias
`attendances.index`, `attendances.export`, `attendances.print`, `puntualidad.index`

### 5.6 Incidencias
`incidencias.index`, `incidencias.create`, `incidencias.store`, `incidencias.estado`

### 5.7 Catálogos
`areas.*` (7 CRUD), `puestos.*` (7 CRUD)

### 5.8 RBAC (22 rutas)
`modules.index`, `modules.update`, `permissions.*` (8), `permission-groups.*` (12), `navigation-items.*` (6)

### 5.9 Preferencias
`preferencia.usuarios.*` (5)

### 5.10 Firebird (8 rutas)
`firebird.index`, `firebird.start`, `firebird.sync`, `firebird.delete`, `firebird.cancel`, `firebird.retry`, `firebird.status`, `firebird.execute-pending`

### 5.11 Operaciones
`operations.notifications`, `operations.queue`, `operations.queue.data`, `operations.queue.data.unified`, `operations.delete`, `operations.cancel`, `operations.retry`

### 5.12 Academia (52 rutas)
**Ciclos:** `academia.ciclos.*` (8)
**Cursos:** `academia.cursos.*` (9)
**Planes:** `academia.planes.*` (7)
**Grupos:** `academia.grupos.*` (4)
**Alumnos:** `academia.alumnos.*` (4)
**Profesores:** `academia.profesores.*` (4)
**Horarios:** `academia.horarios.*` (6)
**Kárdex:** `academia.kardex.*` (4)
**API:** `api.academia.*` (11)
**Otros:** `academia.dashboard`, `academia.set-ciclo`

---

## 6. Controllers

### 6.1 Root (`App\Http\Controllers`)

| Controller | Archivo | Métodos | FormRequest | Policy | Servicios |
|---|---|---|---|---|---|
| `AuthController` | `AuthController.php` | create, store, destroy | — | — | — |
| `DashboardController` | `DashboardController.php` | index, kpisJson | — | — | CicloActualService |
| `DeviceController` | `DeviceController.php` | index, create, store, show, edit, update, destroy, checkStatus, syncNow, syncStatus, progress, refreshData, deduplicate | DeviceFormRequest | DevicePolicy | ZktecoService |
| `DeviceSyncController` | `DeviceSyncController.php` | syncAll, syncUsers, syncAttendances, syncFingerprints, setTime, clearAttendance, restore | — | DevicePolicy | ZktecoService |
| `EmployeeController` | `EmployeeController.php` | index, create, store, search, edit, update, destroy, updateCard, enrollOnDevice, enrollmentDiff, syncToDevices, syncProgress, sobrantes, sobrantesData, sobrantesIgnore, sobrantesUnignore, sobrantesRemove, fingerprints | EmployeeFormRequest | EmployeePolicy | ZktecoService, SobranteService, EmployeeDeviceSyncService |
| `FingerprintController` | `FingerprintController.php` | removeFromDevice, uploadFingerprints | — | — | ZktecoService |
| `AttendanceController` | `AttendanceController.php` | index, export, print | AttendanceFilterRequest | — | — |
| `PuntualidadController` | `PuntualidadController.php` | index | — | — | — |
| `FirebirdController` | `FirebirdController.php` | index, startSync, sync, destroy, cancel, retry, status, executePending | — | — | FirebirdReader |
| `OperationsController` | `OperationsController.php` | notifications, queue, queueData, queueDataUnified, delete, cancel, retry | — | — | — |
| `AreaController` | `AreaController.php` | index, create, store, show, edit, update, destroy | — | — | — |
| `PuestoController` | `PuestoController.php` | index, create, store, show, edit, update, destroy | — | — | — |
| `IncidenciaController` | `IncidenciaController.php` | index, create, store, updateStatus | — | IncidenciaPolicy | — |
| `ModuleController` | `ModuleController.php` | index, update | — | — | — |
| `PermissionController` | `PermissionController.php` | index, create, store, edit, update, destroy, assignGroups, saveGroups | — | — | — |
| `PermissionGroupController` | `PermissionGroupController.php` | index, create, store, edit, update, destroy, permissions, savePermissions, assignEmployees, saveEmployees, assignProfesores, saveProfesores | — | — | — |
| `NavigationItemController` | `NavigationItemController.php` | index, create, store, edit, update, destroy | — | — | — |
| `PreferenciaUsuarioController` | `PreferenciaUsuarioController.php` | index, create, store, edit, update | — | — | — |

### 6.2 Academia (`App\Http\Controllers\Academia`)

| Controller | Métodos | FormRequest | Policy |
|---|---|---|---|
| `DashboardController` | index | — | — |
| `CicloController` | index, create, store, show, edit, update, destroy, setActivo | CicloFormRequest | CicloPolicy |
| `CursoController` | index, create, store, show, edit, update, destroy, addMateria, removeMateria | CursoFormRequest | CursoPolicy |
| `PlanController` | index, create, store, show, edit, update, destroy | PlanFormRequest | PlanPolicy |
| `GrupoController` | index, show, asistencia, guardarAsistencia | — | GrupoPolicy |
| `AlumnoController` | index, show, historial, kardex | — | AlumnoPolicy |
| `ProfesorController` | index, show, horario, asignarUsuario | — | ProfesorPolicy |
| `HorarioController` | base, clase, aula, profesor, persona, guardarAsistenciaClase | — | HorarioPolicy |
| `KardexController` | index, show, historial, print | — | — |
| `ApiController` | alumnosPorGrupo, ciclosDisponibles, grupoDetalle, gruposPorCiclo, horarioBase, materiasPorPlan, metodosEval, niveles, planesPorNivel, sedes, turnos | — | — |

---

## 7. Modelos y relaciones

### 7.1 Root namespace

| Modelo | Tabla | $fillable (resumen) | $casts | Relaciones |
|---|---|---|---|---|
| `User` | users | name, email, password, role, username | — | employee(), profesor(), permissionGroups(), isAdmin() |
| `Employee` | employees | user_id, name, type, numero_empleado, clave_profesor, departamento, cargo, contrato, status_actual, fecha_ingreso, id_campus, nivel, tarjeta_id, area_id, puesto_id, auth_user_id | fecha_ingreso→date | user(), devices()(BelongsToMany+DeviceEmployee), attendances(), fingerprints(), syncs(), sede(), area(), puesto(), authUser(), permissionGroups() |
| `Device` | devices | name, ip, port, password, serial_number, device_name, status, last_seen_at, description | — | employees()(BelongsToMany), syncs(), fingerprints() |
| `DeviceEmployee` (pivot) | device_employee | device_uid, role, card_number, password, active, fingerprint_count, ignored_at | — | employee(), device() |
| `Fingerprint` | fingerprints | device_id, employee_id, finger, template, template_hash | — | employee(), device() |
| `Attendance` | attendances | device_id, employee_id, user_id, type, state, recorded_at, source, attendance_type | recorded_at→datetime | employee(), device() |
| `DeviceSync` | device_syncs | device_id, operation, status, stage, processed, total, ... | — | device(), items() |
| `DeviceSyncItem` | device_sync_items | device_sync_id, employee_id, fingerprint_id, status, message | — | sync(), employee() |
| `FirebirdSync` | firebird_syncs | status, ciclo, strategy, ... | — | items() |
| `FirebirdSyncItem` | firebird_sync_items | firebird_sync_id, table_name, record_id, action, status, message | — | sync() |
| `Module` | modules | slug, name, description, active, icon, sort_order, is_system, group_name | — | permissions(), navigationItems() |
| `Permission` | permissions | module_id, slug, action, name, description | — | module(), groups() |
| `PermissionGroup` | permission_groups | name, description, is_default | is_default→boolean | permissions(), employees(), profesores() |
| `PermissionGroupPermission` | permission_group_permissions | permission_group_id, permission_id | — | — |
| `NavigationItem` | navigation_items | module_id, section, label, route_name, icon, permission_action, sort_order, admin_only, active | admin_only→boolean, active→boolean | module() |
| `Area` | areas | identificador, descripcion, empleado_responsable_id | — | empleadoResponsable() |
| `Puesto` | puestos | identificador, descripcion | — | — |
| `Incidencia` | incidencias | employee_id, type, description, date, status | — | employee() |
| `HorarioLaboral` | horarios_laborales | employee_id, dia_semana, hora_entrada, hora_salida, tolerancia_min | — | employee() |

### 7.2 Academia namespace

| Modelo | Tabla | Relaciones clave |
|---|---|---|
| `Ciclo` | ciclos | inicial, final, periodo, descripcion, activo. Accessor `label` |
| `Alumno` | alumnos | numero_alumno, nombre, paterno, materno, curp, estatus. grupos(), kardex(), asistencias() |
| `AlumnoGrupo` | alumnos_grupos | alumno(), grupo() |
| `AlumnoCurso` | alumnos_cursos | alumno(), curso() |
| `AlumnoKardex` | alumnos_kardex | alumno(), materia(), metodoEval() |
| `AlumnoAsistencia` | alumno_asistencias | — |
| `Grupo` | grupos | codigo_grupo, nivel, turno, grado. alumnos(), horarios() |
| `Curso` | cursos | clave_curso, codigo_grupo. grupo(), detalles() |
| `CursoDet` | cursos_det | curso(), materia() |
| `Materia` | materias | clave_asignatura, nombre_asignatura |
| `Plan` | planes | id_plan, nombre. nivel() |
| `Nivel` | niveles | clave_nivel, descripcion |
| `Profesor` | profesores | clave_profesor, nombre_completo. permissionGroups(), user() |
| `Contrato` | contratos | contrato, descripcion, tipo_personal |
| `HorarioDet` | horarios_det | grupo(), profesor(), materia(), sede(), sesionBase() |
| `SesionBase` | sesiones_base | nivel, turno, sesion, hora_inicio, hora_fin, receso |
| `Sede` | sedes | id_campus, descripcion |
| `Turno` | turnos | clave_turno, descripcion |
| `MetodoEval` | metodos_eval | id_eval, nombre_corto, es_final |
| `DocenteAsistencia` | docentes_asistencias | — |
| `GrupoAsistencia` | grupo_asistencias | — |

---

## 8. Servicios

| Servicio | Archivo | Métodos públicos | Consumido por |
|---|---|---|---|
| `ZktecoService` | `app/Services/ZktecoService.php:19` | client(), connect(), deviceStatus(), info(), getUsers(), getAttendances(), getUsersAndAttendances(), syncUsers(), syncAttendances(), syncFingerprints(), uploadFingerprints(), uploadFingerprint(), removeFingerprint(), setUser(), enrollEmployee(), removeUserFromDevice(), removeUser(deprecated), setTime(), clearAttendance(), restoreDevice() | DeviceController, DeviceSyncController, EmployeeController, FingerprintController, SobranteService, EmployeeDeviceSyncService, SyncDeviceJob, SyncEmployeeToDeviceJob |
| `FirebirdReader` | `app/Services/FirebirdReader.php:12` | connect(), getConnection(), getColumns(), getPrimaryKey(), countRows(), countRowsIn(), fetchRows(), fetchRowsIn(), fetchRowsChunked(), cleanRows(), safeVal(), ping() | Todas las estrategias de sync, FirebirdController |
| `EmployeeDeviceSyncService` | `app/Services/EmployeeDeviceSyncService.php:13` | packageFor(), synchronize() | EmployeeController |
| `SobranteService` | `app/Services/SobranteService.php:28` | query(), get(), getStats(), findById(), ignore(), unignore(), classify(), remove() | EmployeeController |
| `CicloActualService` | `app/Services/CicloActualService.php:12` | getCurrent(), resolve(), findByLabel(), getDefaultCiclo(), current(), storeInSession(), getFromSession(), clearSession(), getAllForSelector() | DashboardController, admin layout, vistas Academia |
| `PermissionResolver` | `app/Services/PermissionResolver.php:14` | getUserPermissions(), userCan(), invalidateForUser(), invalidateForGroup(), invalidateAll() | RequireModulePermission middleware |
| `HorarioResolver` | `app/Services/HorarioResolver.php:17` | getClaseAsistenciaGrid(), getClaseAsistenciaStats(), buildHorarioGrid(), getHorarioBase(), getHorarioProfesor(), detectarConflictosAula() | HorarioController |
| `KardexCalculator` | `app/Services/KardexCalculator.php:12` | calcular(), calcularKardexCompleto(), getHistorialAlumno() | KardexController |
| `PersonaContratosResolver` | `app/Services/PersonaContratosResolver.php` | Resolución de contratos | ProfesorController |
| `EmployeeCatalogMover` | `app/Services/EmployeeCatalogMover.php` | Mover empleados en catálogo | — |
| `SyncStrategies\SyncStrategyInterface` | `app/Services/SyncStrategies/SyncStrategyInterface.php:11` | execute() | FirebirdSyncJob |
| `SyncStrategies\FullSyncStrategy` | `app/Services/SyncStrategies/FullSyncStrategy.php` | Sync completa | FirebirdSyncJob |
| `SyncStrategies\CatalogSmartSync` | `app/Services/SyncStrategies/CatalogSmartSync.php` | Sync inteligente catálogos | FirebirdSyncJob |
| `SyncStrategies\CycleDirectSync` | `app/Services/SyncStrategies/CycleDirectSync.php` | Sync directa por ciclo | FirebirdSyncJob |
| `SyncStrategies\CustomSyncStrategy` | `app/Services/SyncStrategies/CustomSyncStrategy.php` | Sync personalizada | FirebirdSyncJob |

---

## 9. Form Requests

| Request | Archivo | Campos que valida |
|---|---|---|
| `DeviceFormRequest` | `app/Http/Requests/DeviceFormRequest.php` | name, ip, port, password, serial_number, device_name, description |
| `EmployeeFormRequest` | `app/Http/Requests/EmployeeFormRequest.php` | user_id, name, type, numero_empleado, departamento, cargo, contrato, status_actual, fecha_ingreso, id_campus, area_id, puesto_id |
| `AttendanceFilterRequest` | `app/Http/Requests/AttendanceFilterRequest.php` | date_from, date_to, employee_id, device_id, type |
| `CicloFormRequest` | `app/Http/Requests/CicloFormRequest.php` | inicial, final, periodo, descripcion, fecha_inicial, fecha_final |
| `CursoFormRequest` | `app/Http/Requests/CursoFormRequest.php` | clave_curso, codigo_grupo, clave_asignatura, clave_profesor |
| `PlanFormRequest` | `app/Http/Requests/PlanFormRequest.php` | id_plan, nombre, nivel |

---

## 10. Policies

| Policy | Modelo | Acciones verificadas |
|---|---|---|
| `DevicePolicy` | Device | view, create, update, delete, sync |
| `EmployeePolicy` | Employee | view, create, update, delete |
| `AlumnoPolicy` | Alumno | view |
| `CicloPolicy` | Ciclo | view, create, update, delete, activo |
| `CursoPolicy` | Curso | view |
| `GrupoPolicy` | Grupo | view |
| `HorarioPolicy` | HorarioDet | view |
| `IncidenciaPolicy` | Incidencia | view, create, update |
| `MateriaPolicy` | Materia | view |
| `PlanPolicy` | Plan | view |
| `ProfesorPolicy` | Profesor | view |

---

## 11. Middleware

| Middleware | Archivo | Función |
|---|---|---|
| `Authenticate` | `app/Http/Middleware/Authenticate.php` | Redirige a login si no autenticado |
| `RequireModulePermission` | `app/Http/Middleware/RequireModulePermission.php` | Verifica `module_permission:{slug},{action}` via PermissionResolver. Admin bypass. |
| `EnsureAdmin` | `app/Http/Middleware/EnsureAdmin.php` | Solo usuarios con role='admin' |
| `RedirectIfAuthenticated` | `app/Http/Middleware/RedirectIfAuthenticated.php` | Redirige si ya autenticado |
| `TrustProxies` | `app/Http/Middleware/TrustProxies.php` | Proxy trust |
| `TrustHosts` | `app/Http/Middleware/TrustHosts.php` | Host trust |
| `PreventRequestsDuringMaintenance` | `app/Http/Middleware/PreventRequestsDuringMaintenance.php` | Modo mantenimiento |
| `TrimStrings` | `app/Http/Middleware/TrimStrings.php` | Trim automático de strings |
| `EncryptCookies` | `app/Http/Middleware/EncryptCookies.php` | Cookies encriptadas |
| `ValidateCsrfToken` | `app/Http/Middleware/ValidateCsrfToken.php` | Protección CSRF |
| `ValidateSignature` | `app/Http/Middleware/ValidateSignature.php` | Firma de URL |

---

## 12. Vistas y componentes Blade

### 12.1 Layout
- `layouts/admin.blade.php` — Shell principal: sidebar, topbar, toast stack, command palette (Ctrl+K), notificaciones flyout, ciclo selector, Bootstrap 5.3.3 CDN + Vite.

### 12.2 Componentes (8)
| Componente | Archivo | Uso |
|---|---|---|
| `<x-data-table>` | `components/data-table.blade.php` | Tabla con paginación |
| `<x-badge>` | `components/badge.blade.php` | Badge de estado |
| `<x-stat-card>` | `components/stat-card.blade.php` | Tarjeta KPI |
| `<x-page-header>` | `components/page-header.blade.php` | Encabezado |
| `<x-filter-bar>` | `components/filter-bar.blade.php` | Barra de filtros |
| `<x-drawer>` | `components/drawer.blade.php` | Panel lateral |
| `<x-navigation-menu>` | `components/navigation-menu.blade.php` | Menú dinámico RBAC |
| `<x-academia.ciclo-selector>` | `components/academia/ciclo-selector.blade.php` | Selector de ciclo |

### 12.3 Partials (8)
`_sync-progress-panel`, `_sync-preview-drawer`, `_sync-devices-form`, `_quick-filters`, `_fingerprint-badge`, `empty-state`, `sparkline`, `donut`

### 12.4 Vistas por módulo (95 total)
- Auth: `auth/login`
- Dashboard: `dashboard`
- Empleados: `employees/{index,create,edit,show}`, `employees/sobrantes`
- Dispositivos: `devices/{index,create,edit,show}`
- Huellas: `fingerprints/index`
- Asistencias: `attendances/{index,print}`
- Puntualidad: `puntualidad/index`
- Incidencias: `incidencias/{index,create}`
- Áreas: `areas/{index,create,edit,show}`
- Puestos: `puestos/{index,create,edit,show}`
- RBAC: `modules/index`, `permissions/{index,create,edit,assign-groups}`, `permission-groups/{index,create,edit,permissions,assign-employees,assign-profesores}`, `navigation-items/{index,create,edit,form}`
- Preferencias: `preferencia/{index,crear,editar}`
- Firebird: `firebird/{index,sync}`
- Operaciones: `operations/{queue,notifications}`
- Academia: `academia/dashboard/index`, `academia/ciclos/{index,create,edit,show}`, `academia/empty-ciclos`, `academia/cursos/{index,create,edit,show}`, `academia/planes/{index,create,edit,show}`, `academia/grupos/{index,show,asistencia}`, `academia/alumnos/{index,show,historial,kardex}`, `academia/profesores/{index,show,horario}`, `academia/horarios/{base,clase,aula,profesor,persona}`, `academia/kardex/{index,show,historial,print}`
- Errores: `errors/500`
- Welcome: `welcome`

---

## 13. Assets JS/CSS/Vite

### Vite (`vite.config.js`)
- Input: `resources/css/app.css`, `resources/js/app.js`
- Plugin: `laravel-vite-plugin`

### JavaScript
| Archivo | Líneas | Función |
|---|---|---|
| `resources/js/app.js` | 686 | Theme (light/dark/system), Heartbeat (KPIs cada 20s), Sidebar (Ctrl+B), Toasts, Confirm dialog, Notifications flyout, Command palette (Ctrl+K), Alertas globales, Sync forms→toast, Lazy-load employees-index.js |
| `resources/js/bootstrap.js` | — | Bootstrap setup |
| `resources/js/employees-index.js` | — | Funcionalidad específica de índice de empleados |
| `resources/css/app.css` | — | Estilos del proyecto (variables CSS, dark mode) |

### CDN
- Bootstrap 5.3.3 CSS/JS
- Bootstrap Icons 1.11.3

---

## 14. Jobs

| Job | Archivo | Descripción |
|---|---|---|
| `SyncDeviceJob` | `app/Jobs/SyncDeviceJob.php` | Sync completa de un dispositivo (users+attendances+fingerprints) |
| `SyncEmployeeToDeviceJob` | `app/Jobs/SyncEmployeeToDeviceJob.php` | Sync de un empleado a un dispositivo |
| `VerifyDeviceConnectionJob` | `app/Jobs/VerifyDeviceConnectionJob.php` | Verifica conexión de dispositivo |
| `FirebirdSyncJob` | `app/Jobs/FirebirdSyncJob.php` | Ejecuta estrategia de sync desde Firebird |
| `DeprovisionEmployeeJob` | `app/Jobs/DeprovisionEmployeeJob.php` | Desprovisiona empleado de dispositivos |

---

## 15. Comandos Artisan

| Comando | Archivo | Función |
|---|---|---|
| `FirebirdSyncCommand` | `app/Console/Commands/FirebirdSyncCommand.php` | Sync manual Firebird vía CLI |
| `MigrateEmployeesToCentral` | `app/Console/Commands/MigrateEmployeesToCentral.php` | Migra empleados al catálogo central |
| `GenerateUsersForEmployeesAndProfessors` | `app/Console/Commands/GenerateUsersForEmployeesAndProfessors.php` | Genera Users para empleados y profesores |

**Schedule:** Sin tareas programadas activas en `Console\Kernel`.

---

## 16. Eventos y Listeners

| Evento | Implementa | Broadcast |
|---|---|---|
| `SyncProgressUpdated` (`app/Events/SyncProgressUpdated.php`) | ShouldBroadcast, ShouldBroadcastNow | Channel: `sync-progress.{syncId}` |

**Listeners:** Ninguno registrado en `app/Listeners/`.
**Broadcasting:** Driver `null` (no activo). Progreso vía AJAX polling.

---

## 17. RBAC / Permisos

### Modelos
- `Module`: slug (único), name, active, icon, sort_order, is_system, group_name
- `Permission`: module_id (FK), slug (único), action (único por módulo), name
- `PermissionGroup`: name, is_default. Relaciones: permissions(), employees(), profesores()
- `NavigationItem`: module_id, section, label, route_name, icon, permission_action, sort_order, admin_only, active

### Middleware
`RequireModulePermission:{slug},{action}` → `PermissionResolver::userCan()` → Cache 15min por usuario. Admin bypass.

### Módulos conocidos
`academia`, `academia.ciclos`, `employees`, `dispositivos`, `asistencias`, `incidencias`

### Seeders
`ModulePermissionSeeder`: Crea módulos + permisos + vinculación.

---

## 18. Seeders

| Seeder | Archivo | Crea |
|---|---|---|
| `DatabaseSeeder` | `database/seeders/DatabaseSeeder.php` | Orquestador |
| `AdminUserSeeder` | `database/seeders/AdminUserSeeder.php` | Usuario admin inicial |
| `DeviceSeeder` | `database/seeders/DeviceSeeder.php` | Dispositivos de ejemplo |
| `ModulePermissionSeeder` | `database/seeders/ModulePermissionSeeder.php` | Módulos y permisos RBAC |
| `PermissionSeeder` | `database/seeders/PermissionSeeder.php` | Seeds de permisos |

---

## 19. Pruebas

### Feature (37)
`AcademiaHierarchyTest`, `AcademiaRbacMatrixTest`, `AdminLayoutComposerTest`, `AreaPuestoRelationsTest`, `AttendanceFilterTest`, `CicloActualServiceTest`, `CicloControllerTest`, `ContratoDeRutasTest`, `DashboardQueryTest`, `DashboardRenderTest`, `DeviceSyncControllerTest`, `EmployeeEditTest`, `EmployeeEnrollmentDiffTest`, `EmployeeIndexTest`, `EmployeeUpdateCardTest`, `EmployeeUpdateTest`, `ExampleTest`, `FirebirdSyncQueueTest`, `FirebirdSyncTest`, `FingerprintControllerTest`, `IncidenciaModuleTest`, `ModulePermissionsSeederTest`, `OperationsQueueUnifiedTest`, `PermissionGroupsTest`, `PuntualidadModuleTest`, `SobranteRemoveTest`, `SobranteServiceTest`, `SyncQueueTest`, `ZktecoSyncTest`

### Permission (6)
`ModulePermissionSeederTest`, `PermissionGroupPermissionsTest`, `PermissionResolverCacheTest`, `RequireModulePermissionMiddlewareTest`, `UserGroupAssignmentTest`, `UserPermissionInheritanceTest`

### Unit (1)
`ExampleTest`

---

## 20. Configuración

| Archivo | Claves relevantes |
|---|---|
| `config/database.php` | mysql (default), firebird (solo lectura), redis |
| `config/auth.php` | Providers, guards |
| `config/broadcasting.php` | Driver: null |
| `config/queue.php` | Driver: sync |
| `config/sanctum.php` | Tokens API |
| `config/cors.php` | CORS |

**Env:** `DB_*` (MySQL), `FIREBIRD_DSN/USER/PASS`, `BROADCAST_DRIVER`, `QUEUE_CONNECTION`

---

## 21. Scripts del proyecto

| Script | Función |
|---|---|
| `php scripts/baseline.php` | Congela contrato: rutas, vistas, componentes, assets |
| `php scripts/comparar_rutas.php` | Compara baseline actual vs guardado |
| `php scripts/verificar_referencias.php` | Busca route()/view()/@include/<x-comp>/asset() rotos |
| `php scripts/impacto.php` | Identifica consumidores afectados por un cambio |
| `php scripts/verificar.php` | Puerta de calidad completa |
| `php scripts/limpiar.php` | Limpieza a cuarentena |

---

## 22. ZKTeco (dispositivos)

### ZktecoService
- Constructor: `Device` model
- Reintentos: `MAX_RETRIES=3`, `MAX_RETRIES_LONG=5`, backoff progresivo
- Timeout adaptativo: `SOCKET_TIMEOUT=15s`, `TIMEOUT_STEP=10s`, `TIMEOUT_MAX=60s`
- `boot()`: mantiene `devices.status` (online/offline)
- Operaciones: connect, getUsers, getAttendances, syncUsers, syncAttendances, syncFingerprints, uploadFingerprints, uploadFingerprint, removeFingerprint, setUser, enrollEmployee, removeUserFromDevice, setTime, clearAttendance, restoreDevice, info

### EmployeeDeviceSyncService
- `packageFor()`: Empaqueta credenciales para un dispositivo
- `synchronize()`: Sube usuario + huellas

### SobranteService
- Tipo A: device_employee sin employee válido (huérfano)
- Tipo B: employee con status_actual='B' (baja)
- Métodos: query, get, getStats, findById, ignore, unignore, classify, remove

---

## 23. Hallazgos y deuda técnica

> **Auditoría de bases de datos detallada**: ver `.ui-work/00-auditoria/auditoria-bases-datos-2026-09-19.md`

### Positivos
1. Arquitectura clara por dominios (Checador, Academia, RBAC)
2. Firebird solo lectura respetado consistentemente
3. RBAC completo con cache e invalidación
4. 38 tests cubriendo flujos críticos
5. Contrato de rutas test dedicado
6. ZktecoService con reintentos adaptativos robustos
7. Datos sensibles protegidos (encryptación, no salen en logs)
8. Esquema MySQL bien estructurado con FKs, UKs compuestas e índices de rendimiento
9. Sync Firebird→MySQL idempotente (INSERT IGNORE) con deduplicación
10. Cascada completa en device_employee (borrado en cadena de fingerprints/attendances)

### Deuda técnica
1. Broadcasting no activo (driver null, progress vía polling AJAX)
2. `removeUser()` deprecated pero aún presente en ZktecoService
3. Menú antiguo muerto en layout admin (bloque `@if(false)`)
4. Sin listeners para SyncProgressUpdated
5. Schedule vacío en Console\Kernel
6. API routes `api/academia/*` sin auth:sanctum ni permisos
7. Áreas y Puestos sin permisos RBAC en sus rutas
8. Broadcast driver null — websockets no funcionan

### Deuda técnica de bases de datos
9. Tablas MySQL sincronizadas pero no usadas en la app: `empleados_cfghorarios`, `empleados_cfghorarios_det`, `empleados_horarios`, `profesores_horarios`, `profesores_horarios_det`
10. `ALUMNOS_KARDEX` excluido del sync directo de CycleDirectSync
11. `docentes_asistencias` y `grupo_asistencias` sin mapeo Firebird visible
12. Muy pocas columnas de Firebird mapeadas (ej: CURSOS mapea ~9 de ~20 columnas)
13. Sync de ALUMNOS sin filtro ciclo — riesgo de performance con volúmenes grandes
14. `attendances.employee_id` nullable — asistencias huérfanas sin empleado asociado
