# Auditoría Completa del Proyecto — 2026-09-18

> Generada por el agente `auditor`. Solo lectura; no se modificó ningún archivo de la aplicación.

---

## Índice

1. [Resumen ejecutivo](#1-resumen-ejecutivo)
2. [Stack tecnológico](#2-stack-tecnológico)
3. [Rutas](#3-rutas)
4. [Controllers](#4-controllers)
5. [Modelos y relaciones](#5-modelos-y-relaciones)
6. [Servicios](#6-servicios)
7. [Form Requests](#7-form-requests)
8. [Policies](#8-policies)
9. [Middleware](#9-middleware)
10. [Vistas y componentes Blade](#10-vistas-y-componentes-blade)
11. [Assets (JS/CSS/Vite)](#11-assets-jscssvite)
12. [Jobs](#12-jobs)
13. [Comandos Artisan](#13-comandos-artisan)
14. [Eventos y Listeners](#14-eventos-y-listeners)
15. [Base de datos (MySQL)](#15-base-de-datos-mysql)
16. [Firebird (solo lectura)](#16-firebird-solo-lectura)
17. [ZKTeco (dispositivos)](#17-zkteco-dispositivos)
18. [RBAC / Permisos](#18-rbac--permisos)
19. [Seeders](#19-seeders)
20. [Pruebas](#20-pruebas)
21. [Configuración](#21-configuración)
22. [Hallazgos y deuda técnica](#22-hallazgos-y-deuda-técnica)

---

## 1. Resumen ejecutivo

| Dimensión | Cantidad |
|---|---|
| Rutas definidas | 182 |
| Controllers | 29 (incluye 8 en `Academia\`) |
| Modelos Eloquent | 40 (incluye 16 en `Academia\`) |
| Servicios | 15 (incluye 4 estrategias de sync) |
| Form Requests | 6 |
| Policies | 11 |
| Vistas Blade | 95 |
| Componentes Blade | 8 |
| Jobs | 5 |
| Comandos Artisan | 3 |
| Migraciones | 77 |
| Seeders | 5 |
| Tests Feature/Unit | 38 |
| Assets JS | 4 archivos |
| Assets CSS | 1 archivo |

**Dominios:** Checador (empleados, dispositivos, huellas, asistencias) · Academia (ciclos, planes, cursos, grupos, alumnos, profesores, horarios, kárdex) · RBAC/plataforma (módulos, permisos, grupos, navegación, preferencias).

**Fuentes de datos:** MySQL (operativa), Firebird (solo lectura, fuente de verdad de empleados y catálogo académico), Dispositivos ZKTeco (hardware real).

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

## 3. Rutas

### 3.1 Dashboard
| Nombre | Método | URI | Controller | Permiso |
|---|---|---|---|---|
| `dashboard` | GET | `/` | `DashboardController@index` | `Authenticate` |
| `dashboard.kpisJson` | GET | `/kpis/json` | `DashboardController@kpisJson` | — |

### 3.2 Autenticación
| Nombre | Método | URI | Controller |
|---|---|---|---|
| `login` | GET | `/login` | `AuthController@create` |
| `login.store` | POST | `/login` | `AuthController@store` |
| `logout` | POST | `/logout` | `AuthController@destroy` |

### 3.3 Empleados (`employees.*`)
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `employees.index` | GET | `/employees` | `employees,view` |
| `employees.create` | GET | `/employees/create` | `employees,create` |
| `employees.store` | POST | `/employees` | `employees,create` |
| `employees.search` | GET | `/employees/search` | `employees,view` |
| `employees.sobrantes` | GET | `/employees/sobrantes` | `employees,view` |
| `employees.sobrantes.data` | GET | `/employees/sobrantes/data` | `employees,view` |
| `employees.sobrantes.ignore` | POST | `/employees/sobrantes/{deviceId}/{deviceUid}/ignore` | `employees,view` |
| `employees.sobrantes.unignore` | POST | `/employees/sobrantes/{deviceId}/{deviceUid}/unignore` | `employees,view` |
| `employees.sobrantes.remove` | POST | `/employees/sobrantes/{deviceId}/{deviceUid}/{type}/remove` | `employees,delete` |
| `employees.edit` | GET | `/employees/{employee}/edit` | `employees,update` |
| `employees.update` | PUT | `/employees/{employee}` | `employees,update` |
| `employees.destroy` | DELETE | `/employees/{employee}` | `employees,delete` |
| `employees.update-card` | POST | `/employees/{employee}/card` | `employees,update` |
| `employees.enroll-device` | POST | `/employees/{employee}/enroll-device` | `employees,update` |
| `employees.enrollment-diff` | GET | `/employees/{employee}/enrollment-diff` | `employees,view` |
| `employees.delete-fingerprint` | DELETE | `/employees/{employee}/fingerprints/{fingerprint}` | `employees,update` |
| `employees.copy-fingerprint` | POST | `/employees/{employee}/fingerprints/{fingerprint}/copy` | `employees,update` |
| `employees.sync-devices` | POST | `/employees/{employee}/sync-devices` | `employees,update` |
| `employees.sync-progress` | GET | `/employees/{employee}/sync-progress` | `employees,view` |
| `fingerprints.index` | GET | `/fingerprints` | `employees,view` |

### 3.4 Dispositivos (`devices.*`)
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `devices.index` | GET | `/devices` | `dispositivos,view` |
| `devices.create` | GET | `/devices/create` | `dispositivos,create` |
| `devices.store` | POST | `/devices` | `dispositivos,create` |
| `devices.deduplicate` | POST | `/devices/deduplicate` | `dispositivos,update` |
| `devices.show` | GET | `/devices/{device}` | `dispositivos,view` |
| `devices.edit` | GET | `/devices/{device}/edit` | `dispositivos,update` |
| `devices.update` | PUT | `/devices/{device}` | `dispositivos,update` |
| `devices.destroy` | DELETE | `/devices/{device}` | `dispositivos,delete` |
| `devices.check-status` | POST | `/devices/{device}/check-status` | `dispositivos,view` |
| `devices.progress` | GET | `/devices/{device}/progress` | `dispositivos,view` |
| `devices.refresh-data` | GET | `/devices/{device}/refresh-data` | `dispositivos,view` |
| `devices.sync-now` | POST | `/devices/{device}/sync-now` | `dispositivos,sync` |
| `devices.sync-status` | GET | `/devices/{device}/sync-status` | `dispositivos,view` |
| `devices.sync-all` | POST | `/devices/{device}/sync-all` | `dispositivos,sync` |
| `devices.sync-users` | POST | `/devices/{device}/sync-users` | `dispositivos,sync` |
| `devices.sync-attendances` | POST | `/devices/{device}/sync-attendances` | `dispositivos,sync` |
| `devices.sync-fingerprints` | POST | `/devices/{device}/sync-fingerprints` | `dispositivos,sync` |
| `devices.set-time` | POST | `/devices/{device}/set-time` | `dispositivos,sync` |
| `devices.clear-attendance` | POST | `/devices/{device}/clear-attendance` | `dispositivos,sync` |
| `devices.restore` | POST | `/devices/{device}/restore` | `dispositivos,sync` |
| `devices.employees.remove` | DELETE | `/devices/{device}/employees/{employee}` | `dispositivos,update` |
| `devices.employees.upload-fingerprints` | POST | `/devices/{device}/employees/{employee}/upload-fingerprints` | `dispositivos,update` |

### 3.5 Asistencias
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `attendances.index` | GET | `/attendances` | `asistencias,view` |
| `attendances.export` | GET | `/attendances/export` | `asistencias,view` |
| `attendances.print` | GET | `/attendances/print` | `asistencias,view` |
| `puntualidad.index` | GET | `/puntualidad` | `asistencias,view` |

### 3.6 Incidencias
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `incidencias.index` | GET | `/incidencias` | `incidencias,view` |
| `incidencias.create` | GET | `/incidencias/create` | `incidencias,create` |
| `incidencias.store` | POST | `/incidencias` | `incidencias,create` |
| `incidencias.estado` | POST | `/incidencias/{incidencia}/estado` | `incidencias,update` |

### 3.7 Áreas
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `areas.index` | GET | `/areas` | — |
| `areas.create` | GET | `/areas/create` | — |
| `areas.store` | POST | `/areas` | — |
| `areas.show` | GET | `/areas/{area}` | — |
| `areas.edit` | GET | `/areas/{area}/edit` | — |
| `areas.update` | PUT/PATCH | `/areas/{area}` | — |
| `areas.destroy` | DELETE | `/areas/{area}` | — |

### 3.8 Puestos
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `puestos.index` | GET | `/puestos` | — |
| `puestos.create` | GET | `/puestos/create` | — |
| `puestos.store` | POST | `/puestos` | — |
| `puestos.show` | GET | `/puestos/{puesto}` | — |
| `puestos.edit` | GET | `/puestos/{puesto}/edit` | — |
| `puestos.update` | PUT/PATCH | `/puestos/{puesto}` | — |
| `puestos.destroy` | DELETE | `/puestos/{puesto}` | — |

### 3.9 RBAC (Permisos, Grupos, Módulos, Navegación)
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `modules.index` | GET | `/modules` | — |
| `modules.update` | PUT | `/modules/{module}` | — |
| `permissions.index` | GET | `/permissions` | — |
| `permissions.create` | GET | `/permissions/create` | — |
| `permissions.store` | POST | `/permissions` | — |
| `permissions.edit` | GET | `/permissions/{permission}/edit` | — |
| `permissions.update` | PUT | `/permissions/{permission}` | — |
| `permissions.destroy` | DELETE | `/permissions/{permission}` | — |
| `permissions.assign-groups` | GET | `/permissions/{permission}/groups` | — |
| `permissions.save-groups` | POST | `/permissions/{permission}/groups` | — |
| `permission-groups.index` | GET | `/permission-groups` | — |
| `permission-groups.create` | GET | `/permission-groups/create` | — |
| `permission-groups.store` | POST | `/permission-groups` | — |
| `permission-groups.edit` | GET | `/permission-groups/{permissionGroup}/edit` | — |
| `permission-groups.update` | PUT | `/permission-groups/{permissionGroup}` | — |
| `permission-groups.destroy` | DELETE | `/permission-groups/{permissionGroup}` | — |
| `permission-groups.permissions` | GET | `/permission-groups/{permissionGroup}/permissions` | — |
| `permission-groups.savePermissions` | POST | `/permission-groups/{permissionGroup}/permissions` | — |
| `permission-groups.assign-employees` | GET | `/permission-groups/{permissionGroup}/employees` | — |
| `permission-groups.save-employees` | POST | `/permission-groups/{permissionGroup}/employees` | — |
| `permission-groups.assign-profesores` | GET | `/permission-groups/{permissionGroup}/profesores` | — |
| `permission-groups.save-profesores` | POST | `/permission-groups/{permissionGroup}/profesores` | — |
| `navigation-items.index` | GET | `/navigation-items` | — |
| `navigation-items.create` | GET | `/navigation-items/create` | — |
| `navigation-items.store` | POST | `/navigation-items` | — |
| `navigation-items.edit` | GET | `/navigation-items/{navigation_item}/edit` | — |
| `navigation-items.update` | PUT/PATCH | `/navigation-items/{navigation_item}` | — |
| `navigation-items.destroy` | DELETE | `/navigation-items/{navigation_item}` | — |

### 3.10 Preferencias de usuario
| Nombre | Método | URI |
|---|---|---|
| `preferencia.usuarios.index` | GET | `/preferencia/usuarios` |
| `preferencia.usuarios.create` | GET | `/preferencia/usuarios/crear` |
| `preferencia.usuarios.store` | POST | `/preferencia/usuarios` |
| `preferencia.usuarios.edit` | GET | `/preferencia/usuarios/{user}/editar` |
| `preferencia.usuarios.update` | PUT | `/preferencia/usuarios/{user}` |

### 3.11 Firebird Sync
| Nombre | Método | URI |
|---|---|---|
| `firebird.index` | GET | `/firebird` |
| `firebird.start` | POST | `/firebird/start` |
| `firebird.sync` | GET | `/firebird/sync/{sync}` |
| `firebird.delete` | DELETE | `/firebird/{sync}` |
| `firebird.cancel` | POST | `/firebird/{sync}/cancel` |
| `firebird.retry` | POST | `/firebird/{sync}/retry` |
| `firebird.status` | GET | `/firebird/{sync}/status` |
| `firebird.execute-pending` | POST | `/firebird/execute-pending` |

### 3.12 Operaciones / Cola de sync
| Nombre | Método | URI |
|---|---|---|
| `operations.notifications` | GET | `/notifications` |
| `operations.queue` | GET | `/sync-queue` |
| `operations.queue.data` | GET | `/sync-queue/data` |
| `operations.queue.data.unified` | GET | `/sync-queue/data-unified` |
| `operations.delete` | DELETE | `/sync-queue/{sync}` |
| `operations.cancel` | POST | `/sync-queue/{sync}/cancel` |
| `operations.retry` | POST | `/sync-queue/{sync}/retry` |

### 3.13 Academia
| Nombre | Método | URI | Permiso |
|---|---|---|---|
| `academia.dashboard` | GET | `/academia` | `academia,view` |
| `academia.set-ciclo` | POST | `/academia/set-ciclo` | `academia,view` |
| **Ciclos** | | | |
| `academia.ciclos.index` | GET | `/academia/ciclos` | `academia,view` |
| `academia.ciclos.create` | GET | `/academia/ciclos/create` | `academia.ciclos,create` |
| `academia.ciclos.store` | POST | `/academia/ciclos` | `academia.ciclos,create` |
| `academia.ciclos.show` | GET | `/academia/ciclos/{ciclo}` | `academia,view` |
| `academia.ciclos.edit` | GET | `/academia/ciclos/{ciclo}/edit` | `academia.ciclos,update` |
| `academia.ciclos.update` | PUT | `/academia/ciclos/{ciclo}` | `academia.ciclos,update` |
| `academia.ciclos.destroy` | DELETE | `/academia/ciclos/{ciclo}` | `academia.ciclos,delete` |
| `academia.ciclos.activo` | POST | `/academia/ciclos/{ciclo}/activo` | `academia.ciclos,activo` |
| **Cursos** | | | |
| `academia.cursos.index` | GET | `/academia/cursos` | `academia,view` |
| `academia.cursos.create` | GET | `/academia/cursos/create` | `academia,view` |
| `academia.cursos.store` | POST | `/academia/cursos` | `academia,view` |
| `academia.cursos.show` | GET | `/academia/cursos/{curso}` | `academia,view` |
| `academia.cursos.edit` | GET | `/academia/cursos/{curso}/edit` | `academia,view` |
| `academia.cursos.update` | PUT | `/academia/cursos/{curso}` | `academia,view` |
| `academia.cursos.destroy` | DELETE | `/academia/cursos/{curso}` | `academia,view` |
| `academia.cursos.materia.add` | POST | `/academia/cursos/{curso}/materia` | `academia,view` |
| `academia.cursos.materia.remove` | DELETE | `/academia/cursos/{curso}/materia/{materia}` | `academia,view` |
| **Planes** | | | |
| `academia.planes.index` | GET | `/academia/planes` | `academia,view` |
| `academia.planes.create` | GET | `/academia/planes/create` | `academia,view` |
| `academia.planes.store` | POST | `/academia/planes` | `academia,view` |
| `academia.planes.show` | GET | `/academia/planes/{plan}` | `academia,view` |
| `academia.planes.edit` | GET | `/academia/planes/{plan}/edit` | `academia,view` |
| `academia.planes.update` | PUT | `/academia/planes/{plan}` | `academia,view` |
| `academia.planes.destroy` | DELETE | `/academia/planes/{plan}` | `academia,view` |
| **Grupos** | | | |
| `academia.grupos.index` | GET | `/academia/grupos` | `academia,view` |
| `academia.grupos.show` | GET | `/academia/grupos/{grupo}` | `academia,view` |
| `academia.grupos.asistencia` | GET | `/academia/grupos/{grupo}/asistencia` | `academia,view` |
| `academia.grupos.asistencia.guardar` | POST | `/academia/grupos/asistencia` | `academia,view` |
| **Alumnos** | | | |
| `academia.alumnos.index` | GET | `/academia/alumnos` | `academia,view` |
| `academia.alumnos.show` | GET | `/academia/alumnos/{alumno}` | `academia,view` |
| `academia.alumnos.historial` | GET | `/academia/alumnos/{alumno}/historial` | `academia,view` |
| `academia.alumnos.kardex` | GET | `/academia/alumnos/{alumno}/kardex` | `academia,view` |
| **Profesores** | | | |
| `academia.profesores.index` | GET | `/academia/profesores` | `academia,view` |
| `academia.profesores.show` | GET | `/academia/profesores/{profesor}` | `academia,view` |
| `academia.profesores.horario` | GET | `/academia/profesores/{profesor}/horario` | `academia,view` |
| `academia.profesores.usuario` | POST | `/academia/profesores/{profesor}/usuario` | `academia,view` |
| **Horarios** | | | |
| `academia.horarios.base` | GET | `/academia/horarios/base` | `academia,view` |
| `academia.horarios.clase` | GET | `/academia/horarios/clase` | `academia,view` |
| `academia.horarios.clase.asistencia.guardar` | POST | `/academia/horarios/clase/asistencia` | `academia,view` |
| `academia.horarios.aula` | GET | `/academia/horarios/aula` | `academia,view` |
| `academia.horarios.profesor` | GET | `/academia/horarios/profesor` | `academia,view` |
| `academia.horarios.persona` | GET | `/academia/horarios/persona` | `academia,view` |
| **Kárdex** | | | |
| `academia.kardex.index` | GET | `/academia/kardex` | `academia,view` |
| `academia.kardex.show` | GET | `/academia/kardex/show` | `academia,view` |
| `academia.kardex.historial` | GET | `/academia/kardex/historial` | `academia,view` |
| `academia.kardex.print` | GET | `/academia/kardex/print` | `academia,view` |
| **API Academia** | | | |
| `api.academia.alumnos-por-grupo` | GET | `/api/academia/alumnos-por-grupo` | — |
| `api.academia.ciclos-disponibles` | GET | `/api/academia/ciclos-disponibles` | — |
| `api.academia.grupo-detalle` | GET | `/api/academia/grupo-detalle` | — |
| `api.academia.grupos-por-ciclo` | GET | `/api/academia/grupos-por-ciclo` | — |
| `api.academia.horario-base` | GET | `/api/academia/horario-base` | — |
| `api.academia.materias-por-plan` | GET | `/api/academia/materias-por-plan` | — |
| `api.academia.metodos-eval` | GET | `/api/academia/metodos-eval` | — |
| `api.academia.niveles` | GET | `/api/academia/niveles` | — |
| `api.academia.planes-por-nivel` | GET | `/api/academia/planes-por-nivel` | — |
| `api.academia.sedes` | GET | `/api/academia/sedes` | — |
| `api.academia.turnos` | GET | `/api/academia/turnos` | — |

---

## 4. Controllers

### 4.1 Root namespace (`App\Http\Controllers`)

| Controller | Métodos públicos | FormRequest | Policy | Servicios inyectados |
|---|---|---|---|---|
| `AuthController` | `create`, `store`, `destroy` | — | — | — |
| `DashboardController` | `index`, `kpisJson` | — | — | `CicloActualService` |
| `DeviceController` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `checkStatus`, `syncNow`, `syncStatus`, `progress`, `refreshData`, `deduplicate` | `DeviceFormRequest` | `DevicePolicy` | `ZktecoService` |
| `DeviceSyncController` | `syncAll`, `syncUsers`, `syncAttendances`, `syncFingerprints`, `setTime`, `clearAttendance`, `restore` | — | `DevicePolicy` | `ZktecoService` |
| `EmployeeController` | `index`, `create`, `store`, `search`, `edit`, `update`, `destroy`, `updateCard`, `enrollOnDevice`, `enrollmentDiff`, `syncToDevices`, `syncProgress`, `sobrantes`, `sobrantesData`, `sobrantesIgnore`, `sobrantesUnignore`, `sobrantesRemove`, `fingerprints` | `EmployeeFormRequest` | `EmployeePolicy` | `ZktecoService`, `SobranteService`, `EmployeeDeviceSyncService` |
| `FingerprintController` | `removeFromDevice`, `uploadFingerprints` | — | — | `ZktecoService` |
| `AttendanceController` | `index`, `export`, `print` | `AttendanceFilterRequest` | — | — |
| `PuntualidadController` | `index` | — | — | — |
| `FirebirdController` | `index`, `startSync`, `sync`, `destroy`, `cancel`, `retry`, `status`, `executePending` | — | — | `FirebirdReader` |
| `OperationsController` | `notifications`, `queue`, `queueData`, `queueDataUnified`, `delete`, `cancel`, `retry` | — | — | — |
| `AreaController` | CRUD completo | — | — | — |
| `PuestoController` | CRUD completo | — | — | — |
| `IncidenciaController` | `index`, `create`, `store`, `updateStatus` | — | `IncidenciaPolicy` | — |
| `ModuleController` | `index`, `update` | — | — | — |
| `PermissionController` | `index`, `create`, `store`, `edit`, `update`, `destroy`, `assignGroups`, `saveGroups` | — | — | — |
| `PermissionGroupController` | `index`, `create`, `store`, `edit`, `update`, `destroy`, `permissions`, `savePermissions`, `assignEmployees`, `saveEmployees`, `assignProfesores`, `saveProfesores` | — | — | — |
| `NavigationItemController` | `index`, `create`, `store`, `edit`, `update`, `destroy` | — | — | — |
| `PreferenciaUsuarioController` | `index`, `create`, `store`, `edit`, `update` | — | — | — |
| `Controller` | (base) | — | — | — |

### 4.2 Academia namespace (`App\Http\Controllers\Academia`)

| Controller | Métodos públicos | FormRequest | Policy |
|---|---|---|---|
| `DashboardController` | `index` | — | — |
| `CicloController` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `setActivo` | `CicloFormRequest` | `CicloPolicy` |
| `CursoController` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `addMateria`, `removeMateria` | `CursoFormRequest` | `CursoPolicy` |
| `PlanController` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` | `PlanFormRequest` | `PlanPolicy` |
| `GrupoController` | `index`, `show`, `asistencia`, `guardarAsistencia` | — | `GrupoPolicy` |
| `AlumnoController` | `index`, `show`, `historial`, `kardex` | — | `AlumnoPolicy` |
| `ProfesorController` | `index`, `show`, `horario`, `asignarUsuario` | — | `ProfesorPolicy` |
| `HorarioController` | `base`, `clase`, `aula`, `profesor`, `persona`, `guardarAsistenciaClase` | — | `HorarioPolicy` |
| `KardexController` | `index`, `show`, `historial`, `print` | — | — |
| `ApiController` | `alumnosPorGrupo`, `ciclosDisponibles`, `grupoDetalle`, `gruposPorCiclo`, `horarioBase`, `materiasPorPlan`, `metodosEval`, `niveles`, `planesPorNivel`, `sedes`, `turnos` | — | — |

---

## 5. Modelos y relaciones

### 5.1 Root namespace

| Modelo | Tabla | `$fillable` (resumen) | `$casts` | Relaciones clave |
|---|---|---|---|---|
| `User` | `users` | name, email, password, role, username | — | `employee()`, `profesor()`, `permissionGroups()` |
| `Employee` | `employees` | user_id, name, type, numero_empleado, clave_profesor, departamento, cargo, contrato, status_actual, fecha_ingreso, id_campus, nivel, tarjeta_id, area_id, puesto_id, auth_user_id | fecha_ingreso→date | `user()`, `devices()` (BelongsToMany pivote), `attendances()`, `fingerprints()`, `syncs()`, `sede()`, `area()`, `puesto()`, `permissionGroups()` |
| `Device` | `devices` | name, ip, port, password, status, serial, model, last_seen_at | — | `employees()` (BelongsToMany), `syncs()`, `fingerprints()` |
| `DeviceEmployee` (pivot) | `device_employee` | device_uid, role, card_number, password, active, fingerprint_count, ignored_at | — | `employee()`, `device()` |
| `Fingerprint` | `fingerprints` | device_id, employee_id, finger, template, template_hash | — | `employee()`, `device()` |
| `Attendance` | `attendances` | device_id, employee_id, user_id, type, state, recorded_at, source, attendance_type | recorded_at→datetime | `employee()`, `device()` |
| `DeviceSync` | `device_syncs` | device_id, operation, status, stage, processed, total, created_count, updated_count, error_message | — | `device()`, `items()` |
| `DeviceSyncItem` | `device_sync_items` | device_sync_id, employee_id, status, message | — | `sync()`, `employee()` |
| `FirebirdSync` | `firebird_syncs` | status, ciclo, strategy, started_at, finished_at, created_count, updated_count, deleted_count, error_message | — | `items()` |
| `FirebirdSyncItem` | `firebird_sync_items` | firebird_sync_id, table_name, record_id, action, status, message | — | `sync()` |
| `Module` | `modules` | slug, name, description, active, icon, sort_order, is_system, group_name | — | `permissions()`, `navigationItems()` |
| `Permission` | `permissions` | module_id, slug, action, name, description | — | `module()`, `groups()` |
| `PermissionGroup` | `permission_groups` | name, description, is_default | is_default→boolean | `permissions()`, `employees()`, `profesores()` |
| `PermissionGroupPermission` | `permission_group_permissions` | permission_group_id, permission_id | — | — |
| `NavigationItem` | `navigation_items` | module_id, section, label, route_name, icon, permission_action, sort_order, admin_only, active | admin_only→boolean, active→boolean | `module()` |
| `Area` | `areas` | name, description | — | `employees()` |
| `Puesto` | `puestos` | name, description | — | `employees()` |
| `Incidencia` | `incidencias` | employee_id, type, description, date, status | — | `employee()` |
| `HorarioLaboral` | `horarios_laborales` | — | — | — |

### 5.2 Academia namespace

| Modelo | Tabla | Relaciones clave |
|---|---|---|
| `Ciclo` | `ciclos` | inicial, final, periodo, descripcion, activo. Accessor `label` ("{inicial}-{final}-{periodo}") |
| `Alumno` | `alumnos` | numero_alumno, nombre, apellido_paterno, apellido_materno, estatus. `grupos()`, `kardex()`, `asistencias()` |
| `AlumnoGrupo` | `alumnos_grupos` | alumno→Alumno, grupo→Grupo |
| `AlumnoCurso` | `alumnos_cursos` | alumno→Alumno, curso→Curso |
| `AlumnoKardex` | `alumnos_kardex` | alumno→Alumno, materia→Materia, metodoEval→MetodoEval |
| `AlumnoAsistencia` | `alumno_asistencias` | — |
| `Grupo` | `grupos` | codigo_grupo, inicial, final, periodo, nivel, turno, grado. `alumnos()`, `horarios()` |
| `Curso` | `cursos` | clave_curso, nombre, grupo→Grupo |
| `CursoDet` | `cursos_det` | curso→Curso, materia→Materia |
| `Materia` | `materias` | clave_asignatura, nombre_asignatura |
| `Plan` | `planes` | id_plan, nombre, nivel→Nivel |
| `Nivel` | `niveles` | clave_nivel, descripcion |
| `Profesor` | `profesores` | clave_profesor, nombre_completo. `permissionGroups()`, `user()` |
| `Contrato` | `contratos` | clave_profesor, contrato |
| `HorarioDet` | `horarios_det` | inicial, final, periodo, codigo_grupo, clave_profesor, clave_asignatura, dia, sesion. `grupo()`, `profesor()`, `materia()`, `sede()`, `sesionBase()` |
| `SesionBase` | `sesiones_base` | nivel, turno, sesion, hora_inicio, hora_fin, receso, orden |
| `Sede` | `sedes` | id_campus, descripcion |
| `Turno` | `turnos` | clave_turno, descripcion |
| `MetodoEval` | `metodos_eval` | id_eval, nombre_corto, es_final |
| `DocenteAsistencia` | `docentes_asistencias` | — |
| `GrupoAsistencia` | `grupo_asistencias` | — |

---

## 6. Servicios

| Servicio | Archivo | Métodos públicos | Consumido por |
|---|---|---|---|
| `ZktecoService` | `app/Services/ZktecoService.php:19` | `client()`, `connect()`, `deviceStatus()`, `info()`, `getUsers()`, `getAttendances()`, `getUsersAndAttendances()`, `syncUsers()`, `syncAttendances()`, `syncFingerprints()`, `uploadFingerprints()`, `uploadFingerprint()`, `removeFingerprint()`, `setUser()`, `enrollEmployee()`, `removeUserFromDevice()`, `removeUser()`, `setTime()`, `clearAttendance()`, `restoreDevice()` | DeviceController, DeviceSyncController, EmployeeController, FingerprintController, SobranteService, EmployeeDeviceSyncService, SyncDeviceJob, SyncEmployeeToDeviceJob |
| `FirebirdReader` | `app/Services/FirebirdReader.php:12` | `connect()`, `getConnection()`, `getColumns()`, `getPrimaryKey()`, `countRows()`, `countRowsIn()`, `fetchRows()`, `fetchRowsIn()`, `fetchRowsChunked()`, `cleanRows()`, `safeVal()`, `ping()` | Todas las estrategias de sync, FirebirdController |
| `EmployeeDeviceSyncService` | `app/Services/EmployeeDeviceSyncService.php:13` | `packageFor()`, `synchronize()` | EmployeeController |
| `SobranteService` | `app/Services/SobranteService.php:28` | `query()`, `get()`, `getStats()`, `findById()`, `ignore()`, `unignore()`, `classify()`, `remove()` | EmployeeController |
| `CicloActualService` | `app/Services/CicloActualService.php:12` | `getCurrent()`, `resolve()`, `findByLabel()`, `getDefaultCiclo()`, `current()`, `storeInSession()`, `getFromSession()`, `clearSession()`, `getAllForSelector()` | DashboardController, admin layout composer, todas las vistas de Academia |
| `PermissionResolver` | `app/Services/PermissionResolver.php:14` | `getUserPermissions()`, `userCan()`, `invalidateForUser()`, `invalidateForGroup()`, `invalidateAll()` | RequireModulePermission middleware, AdminLayoutComposer |
| `HorarioResolver` | `app/Services/HorarioResolver.php:17` | `getClaseAsistenciaGrid()`, `getClaseAsistenciaStats()`, `buildHorarioGrid()`, `getHorarioBase()`, `getHorarioProfesor()`, `detectarConflictosAula()` | HorarioController |
| `KardexCalculator` | `app/Services/KardexCalculator.php:12` | `calcular()`, `calcularKardexCompleto()`, `getHistorialAlumno()` | KardexController |
| `PersonaContratosResolver` | `app/Services/PersonaContratosResolver.php` | Resolución de contratos de persona | ProfesorController |
| `EmployeeCatalogMover` | `app/Services/EmployeeCatalogMover.php` | Mover empleados en catálogo | — |
| `SyncStrategies\SyncStrategyInterface` | `app/Services/SyncStrategies/SyncStrategyInterface.php:11` | `execute()` | FirebirdSyncJob |
| `SyncStrategies\FullSyncStrategy` | `app/Services/SyncStrategies/FullSyncStrategy.php` | Implementación de sync completa | FirebirdSyncJob |
| `SyncStrategies\CatalogSmartSync` | `app/Services/SyncStrategies/CatalogSmartSync.php` | Sync inteligente de catálogos | FirebirdSyncJob |
| `SyncStrategies\CycleDirectSync` | `app/Services/SyncStrategies/CycleDirectSync.php` | Sync directa de ciclos | FirebirdSyncJob |
| `SyncStrategies\CustomSyncStrategy` | `app/Services/SyncStrategies/CustomSyncStrategy.php` | Sync personalizada | FirebirdSyncJob |

---

## 7. Form Requests

| Request | Archivo | Valida |
|---|---|---|
| `DeviceFormRequest` | `app/Http/Requests/DeviceFormRequest.php` | Campos de dispositivo (name, ip, port, password, etc.) |
| `EmployeeFormRequest` | `app/Http/Requests/EmployeeFormRequest.php` | Campos de empleado (user_id, name, type, etc.) |
| `AttendanceFilterRequest` | `app/Http/Requests/AttendanceFilterRequest.php` | Filtros de asistencia (fechas, employee_id, device_id) |
| `CicloFormRequest` | `app/Http/Requests/CicloFormRequest.php` | Campos de ciclo (inicial, final, periodo, descripcion) |
| `CursoFormRequest` | `app/Http/Requests/CursoFormRequest.php` | Campos de curso |
| `PlanFormRequest` | `app/Http/Requests/PlanFormRequest.php` | Campos de plan de estudios |

---

## 8. Policies

| Policy | Modelo protegido | Acciones |
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

## 9. Middleware

| Middleware | Archivo | Función |
|---|---|---|
| `Authenticate` | `app/Http/Middleware/Authenticate.php` | Redirige a login si no autenticado |
| `RequireModulePermission` | `app/Http/Middleware/RequireModulePermission.php` | Verifica permiso `module_permission:{slug},{action}` via `PermissionResolver` |
| `EnsureAdmin` | `app/Http/Middleware/EnsureAdmin.php` | Solo usuarios admin |
| `RedirectIfAuthenticated` | `app/Http/Middleware/RedirectIfAuthenticated.php` | Redirige si ya autenticado |
| `TrustProxies` | `app/Http/Middleware/TrustProxies.php` | Proxy trust |
| `TrustHosts` | `app/Http/Middleware/TrustHosts.php` | Host trust |
| `PreventRequestsDuringMaintenance` | `app/Http/Middleware/PreventRequestsDuringMaintenance.php` | Mantenimiento |
| `TrimStrings` | `app/Http/Middleware/TrimStrings.php` | Trim automático |
| `EncryptCookies` | `app/Http/Middleware/EncryptCookies.php` | Cookies encriptadas |
| `ValidateCsrfToken` | `app/Http/Middleware/ValidateCsrfToken.php` | CSRF |
| `ValidateSignature` | `app/Http/Middleware/ValidateSignature.php` | Firma de URL |

---

## 10. Vistas y componentes Blade

### 10.1 Layout principal
- `resources/views/layouts/admin.blade.php` — Shell principal (sidebar, topbar, toast stack, command palette, notificaciones flyout, ciclo selector). Carga Bootstrap 5.3.3 CDN + Vite.

### 10.2 Componentes Blade reutilizables
| Componente | Archivo | Props/Slots |
|---|---|---|
| `<x-data-table>` | `resources/views/components/data-table.blade.php` | Tabla con paginación |
| `<x-badge>` | `resources/views/components/badge.blade.php` | Badge de estado |
| `<x-stat-card>` | `resources/views/components/stat-card.blade.php` | Tarjeta KPI |
| `<x-page-header>` | `resources/views/components/page-header.blade.php` | Encabezado de página |
| `<x-filter-bar>` | `resources/views/components/filter-bar.blade.php` | Barra de filtros |
| `<x-drawer>` | `resources/views/components/drawer.blade.php` | Panel lateral/drawer |
| `<x-navigation-menu>` | `resources/views/components/navigation-menu.blade.php` | Menú de navegación dinámico |
| `<x-academia.ciclo-selector>` | `resources/views/components/academia/ciclo-selector.blade.php` | Selector de ciclo escolar |

### 10.3 Partials
| Partial | Archivo | Usado por |
|---|---|---|
| `_sync-progress-panel` | `employees/partials/_sync-progress-panel.blade.php` | employees/show |
| `_sync-preview-drawer` | `employees/partials/_sync-preview-drawer.blade.php` | employees/show |
| `_sync-devices-form` | `employees/partials/_sync-devices-form.blade.php` | employees/show |
| `_quick-filters` | `employees/partials/_quick-filters.blade.php` | employees/index |
| `_fingerprint-badge` | `employees/partials/_fingerprint-badge.blade.php` | employees/index |
| `empty-state` | `partials/empty-state.blade.php` | Diversas vistas |
| `sparkline` | `partials/sparkline.blade.php` | Dashboard |
| `donut` | `partials/donut.blade.php` | Dashboard |

### 10.4 Vistas por módulo

| Módulo | Vistas |
|---|---|
| Auth | `auth/login.blade.php` |
| Dashboard | `dashboard.blade.php` |
| Empleados | `employees/{index,create,edit,show}.blade.php`, `employees/sobrantes.blade.php` |
| Dispositivos | `devices/{index,create,edit,show}.blade.php` |
| Huellas | `fingerprints/index.blade.php` |
| Asistencias | `attendances/{index,print}.blade.php` |
| Puntualidad | `puntualidad/index.blade.php` |
| Incidencias | `incidencias/{index,create}.blade.php` |
| Áreas | `areas/{index,create,edit,show}.blade.php` |
| Puestos | `puestos/{index,create,edit,show}.blade.php` |
| RBAC | `modules/index.blade.php`, `permissions/{index,create,edit,assign-groups}.blade.php`, `permission-groups/{index,create,edit,permissions,assign-employees,assign-profesores}.blade.php`, `navigation-items/{index,create,edit,form}.blade.php` |
| Preferencias | `preferencia/{index,crear,editar}.blade.php` |
| Firebird | `firebird/{index,sync}.blade.php` |
| Operaciones | `operations/{queue,notifications}.blade.php` |
| Academia Dashboard | `academia/dashboard/index.blade.php` |
| Academia Ciclos | `academia/ciclos/{index,create,edit,show}.blade.php`, `academia/empty-ciclos.blade.php` |
| Academia Cursos | `academia/cursos/{index,create,edit,show}.blade.php` |
| Academia Planes | `academia/planes/{index,create,edit,show}.blade.php` |
| Academia Grupos | `academia/grupos/{index,show,asistencia}.blade.php` |
| Academia Alumnos | `academia/alumnos/{index,show,historial,kardex}.blade.php` |
| Academia Profesores | `academia/profesores/{index,show,horario}.blade.php` |
| Academia Horarios | `academia/horarios/{base,clase,aula,profesor,persona}.blade.php` |
| Academia Kárdex | `academia/kardex/{index,show,historial,print}.blade.php` |
| Errores | `errors/500.blade.php` |
| Welcome | `welcome.blade.php` |

---

## 11. Assets (JS/CSS/Vite)

### Vite Config (`vite.config.js`)
- Input: `resources/css/app.css`, `resources/js/app.js`
- Plugin: `laravel-vite-plugin`

### JavaScript
| Archivo | Líneas | Función |
|---|---|---|
| `resources/js/app.js` | 686 | **Módulo principal**: Theme (light/dark/system), Heartbeat (KPIs en vivo cada 20s), Sidebar (colapsable, Ctrl+B), Toasts, Confirm dialog, Notifications flyout, Command palette (Ctrl+K), Alertas globales, Sync forms → toast, Lazy-load de `employees-index.js` |
| `resources/js/bootstrap.js` | — | Bootstrap setup |
| `resources/js/employees-index.js` | — | Funcionalidad específica del índice de empleados (lazy-loaded) |
| `resources/css/app.css` | — | Estilos del proyecto (variables CSS, dark mode, componentes) |

### CDN Externos
- Bootstrap 5.3.3 CSS/JS
- Bootstrap Icons 1.11.3

---

## 12. Jobs

| Job | Archivo | Cola | Descripción |
|---|---|---|---|
| `SyncDeviceJob` | `app/Jobs/SyncDeviceJob.php` | default | Sincroniza un dispositivo completo (users + attendances + fingerprints) |
| `SyncEmployeeToDeviceJob` | `app/Jobs/SyncEmployeeToDeviceJob.php` | default | Sincroniza un empleado específico a un dispositivo |
| `VerifyDeviceConnectionJob` | `app/Jobs/VerifyDeviceConnectionJob.php` | default | Verifica conexión de un dispositivo |
| `FirebirdSyncJob` | `app/Jobs/FirebirdSyncJob.php` | default | Ejecuta una estrategia de sincronización desde Firebird |
| `DeprovisionEmployeeJob` | `app/Jobs/DeprovisionEmployeeJob.php` | default | Desprovisiona empleado de dispositivos |

---

## 13. Comandos Artisan

| Comando | Archivo | Función |
|---|---|---|
| `FirebirdSyncCommand` | `app/Console/Commands/FirebirdSyncCommand.php` | Sync manual desde Firebird vía CLI |
| `MigrateEmployeesToCentral` | `app/Console/Commands/MigrateEmployeesToCentral.php` | Migra empleados al catálogo central |
| `GenerateUsersForEmployeesAndProfessors` | `app/Console/Commands/GenerateUsersForEmployeesAndProfessors.php` | Genera registros User para empleados y profesores |

### Schedule
El `Console\Kernel` no tiene tareas programadas activas (comentario `$schedule->command('inspire')->hourly()`).

---

## 14. Eventos y Listeners

| Evento | Archivo | Implementa | Broadcast |
|---|---|---|---|
| `SyncProgressUpdated` | `app/Events/SyncProgressUpdated.php` | `ShouldBroadcast`, `ShouldBroadcastNow` | Channel: `sync-progress.{syncId}` |

**Listeners:** No hay listeners registrados en `app/Listeners/`.

**Broadcasting:** Configurado en `config/broadcasting.php` con driver por defecto `null` (no activo en producción actualmente).

---

## 15. Base de datos (MySQL)

### 15.1 Tablas principales

| Tabla | Descripción | Migraciones asociadas |
|---|---|---|
| `users` | Usuarios de acceso | `2014_10_12_000000`, `2026_09_05_202614` (role check), `2026_09_16_000000` (username) |
| `employees` | Catálogo central de empleados | `2024_01_01_000002`, `2026_08_20_000004`, `2026_08_23_000003`, `2026_09_05_010214`, `2026_09_09_000002` |
| `devices` | Dispositivos ZKTeco | `2024_01_01_000001`, `2026_08_20_000002`, `2026_08_24_090807` |
| `device_employee` | Pivote empleado-dispositivo | `2026_08_23_000001`, `2026_09_09_000001` (ignored_at) |
| `fingerprints` | Huellas digitales | `2024_01_01_000004`, `2026_08_23_000002` |
| `attendances` | Registros de asistencia | `2024_01_01_000003`, `2026_08_20_000005`, `2026_09_06_000005`, `2026_09_07_000001` |
| `device_syncs` | Historial de sync con dispositivos | `2024_01_01_000006`, `2026_08_20_000001`, `2026_08_20_000003` |
| `device_sync_items` | Items individuales de sync | `2026_08_24_120000` |
| `firebird_syncs` | Historial de sync con Firebird | `2026_09_05_013318` |
| `firebird_sync_items` | Items de sync Firebird | `2026_09_05_013342` |
| `modules` | Módulos RBAC | `2026_09_12_140000` |
| `permissions` | Permisos | `2026_09_11_140000` |
| `permission_groups` | Grupos de permisos | `2026_09_11_140000` |
| `permission_group_permissions` | Pivote grupos-permisos | `2026_09_11_140000` |
| `employee_permission_groups` | Pivote empleados-grupos | `2026_09_11_140000` |
| `profesor_permission_groups` | Pivote profesores-grupos | `2026_09_11_140000` |
| `navigation_items` | Elementos de navegación | `2026_09_12_000001` |
| `areas` | Áreas | `2026_09_11_120000` |
| `puestos` | Puestos | `2026_09_11_120000` |
| `incidencias` | Incidencias | `2026_09_11_130000` |
| `horarios_laborales` | Horarios laborales | `2026_09_16_000001` |

### 15.2 Tablas Academia

| Tabla | Migración |
|---|---|
| `ciclos` | `2026_09_05_094422` |
| `niveles` | `2026_09_05_094433` |
| `sedes` | `2026_09_05_094435` |
| `turnos` | `2026_09_05_094435` |
| `planes` | `2026_09_05_094436` |
| `materias` | `2026_09_05_094437` |
| `metodos_eval` | `2026_09_05_094437` |
| `contratos` | `2026_09_05_094438` |
| `sesiones_base` | `2026_09_05_094439` |
| `alumnos` | `2026_09_05_094716` |
| `alumnos_grupos` | `2026_09_05_094717` |
| `alumnos_kardex` | `2026_09_05_094717` |
| `grupos` | `2026_09_05_094716` |
| `cursos` | `2026_09_05_094718` |
| `cursos_det` | `2026_09_05_094718` |
| `profesores` | `2026_09_05_094718` |
| `horarios_det` | `2026_09_05_094718` |
| `alumnos_cursos` | `2026_09_10_150000` |
| `alumno_asistencias` | `2026_09_10_120000` |
| `docentes_asistencias` | `2026_09_10_110000` |
| `grupo_asistencias` | `2026_09_10_120000` |
| `alumnos_niveles` | `2026_09_06_192836` |

### 15.3 Índices de rendimiento notables
- `employees`: índice en `status_actual` (`2026_09_09_000002`)
- `attendances`: índice único `attendance_device_employee_unique (device_id, employee_id, recorded_at)` + índice en `type_recorded_at` (`2026_09_07_000001`)
- `device_employee`: índice en `ignored_at` (`2026_09_09_000001`)

---

## 16. Firebird (solo lectura)

### Conexión
- Configurada en `config/database.php:95` como conexión `firebird` (driver firebird, DSN configurable).
- Acceso vía `FirebirdReader` (`app/Services/FirebirdReader.php:12`) que usa PDO directo.

### Estrategias de sincronización
Todas implementan `SyncStrategyInterface`:

| Estrategia | Archivo | Descripción |
|---|---|---|
| `FullSyncStrategy` | `app/Services/SyncStrategies/FullSyncStrategy.php` | Sync completa de todas las tablas |
| `CatalogSmartSync` | `app/Services/SyncStrategies/CatalogSmartSync.php` | Sync inteligente de catálogos (solo cambios) |
| `CycleDirectSync` | `app/Services/SyncStrategies/CycleDirectSync.php` | Sync directa por ciclo |
| `CustomSyncStrategy` | `app/Services/SyncStrategies/CustomSyncStrategy.php` | Sync personalizada (tablas específicas) |

### Tablas Firebird leídas
- `EMPLEADOS` → `employees`
- `PROFESORES` → `profesores`
- `ALUMNOS` → `alumnos`
- `CICLOS` → `ciclos`
- `PLANES` → `planes`
- `MATERIAS` → `materias`
- `CURSOS` / `CURSOS_DET` → `cursos` / `cursos_det`
- `GRUPOS` → `grupos`
- `HORARIOS_DET` → `horarios_det`
- `SESIONES_BASE` → `sesiones_base`
- `SEDES` → `sedes`
- `TURNOS` → `turnos`
- `NIVELES` → `niveles`
- `METODOS_EVAL` → `metodos_eval`
- `CONTRATOS` → `contratos`

**Regla irrompible:** Firebird es SOLO LECTURA. Ninguna escritura, nunca.

---

## 17. ZKTeco (dispositivos)

### ZktecoService (`app/Services/ZktecoService.php:19`)
- Constructor recibe `Device` model.
- Maneja reconexión con reintentos (`MAX_RETRIES = 3`, `MAX_RETRIES_LONG = 5`), backoff progresivo y timeout adaptativo (`SOCKET_TIMEOUT = 15s`, `TIMEOUT_STEP = 10s`, `TIMEOUT_MAX = 60s`).
- `boot()` mantiene `devices.status` fiel (online/offline).
- Operaciones: `connect`, `getUsers`, `getAttendances`, `syncUsers`, `syncAttendances`, `syncFingerprints`, `uploadFingerprints`, `uploadFingerprint`, `removeFingerprint`, `setUser`, `enrollEmployee`, `removeUserFromDevice`, `removeUser` (deprecated), `setTime`, `clearAttendance`, `restoreDevice`, `info`.

### EmployeeDeviceSyncService (`app/Services/EmployeeDeviceSyncService.php:13`)
- `packageFor()`: Empaqueta credenciales de empleado para un dispositivo.
- `synchronize()`: Sube usuario + huellas a un dispositivo.

### SobranteService (`app/Services/SobranteService.php:28`)
- Gestión de registros `device_employee` sin `employee` válido (Tipo A: huérfano) o con employee dado de baja (Tipo B: status_actual='B').
- Métodos: `query()`, `get()`, `getStats()`, `findById()`, `ignore()`, `unignore()`, `classify()`, `remove()`.

### Jobs relacionados
- `SyncDeviceJob`: Sync completa de un dispositivo.
- `SyncEmployeeToDeviceJob`: Sync de un empleado a un dispositivo.
- `VerifyDeviceConnectionJob`: Verificación de conexión.
- `DeprovisionEmployeeJob`: Desprovisionamiento de empleado.

---

## 18. RBAC / Permisos

### Modelos
- `Module`: slug, name, active, icon, sort_order, is_system, group_name. Scopes: `active()`, `byGroup()`.
- `Permission`: module_id, slug, action, name, description. Pertenece a un Module.
- `PermissionGroup`: name, description, is_default. Tiene permissions, employees, profesores.
- `NavigationItem`: module_id, section, label, route_name, icon, permission_action, sort_order, admin_only, active.

### Middleware `RequireModulePermission`
- Formato: `RequireModulePermission:{module_slug},{action}` (ej: `RequireModulePermission:academia,view`).
- Verifica vía `PermissionResolver::userCan()`.
- Admin bypass: `User::isAdmin()`.

### PermissionResolver
- Cachea permisos por usuario con key `user_perms_{userId}`, TTL 15 min.
- Invalidación: por usuario, por grupo, global.

### Seeders
- `ModulePermissionSeeder`: Crea módulos, permisos y los vincula.
- `PermissionSeeder`: Seeds iniciales de permisos.

### Modules conocidos (según rutas)
- `academia` (view)
- `academia.ciclos` (create, update, delete, activo)
- `employees` (view, create, update, delete)
- `dispositivos` (view, create, update, delete, sync)
- `asistencias` (view)
- `incidencias` (view, create, update)

---

## 19. Seeders

| Seeder | Archivo | Función |
|---|---|---|
| `DatabaseSeeder` | `database/seeders/DatabaseSeeder.php` | Orquestador principal |
| `AdminUserSeeder` | `database/seeders/AdminUserSeeder.php` | Crea usuario admin inicial |
| `DeviceSeeder` | `database/seeders/DeviceSeeder.php` | Crea dispositivos de ejemplo |
| `ModulePermissionSeeder` | `database/seeders/ModulePermissionSeeder.php` | Crea módulos y permisos RBAC |
| `PermissionSeeder` | `database/seeders/PermissionSeeder.php` | Seeds de permisos |

---

## 20. Pruebas

### 20.1 Feature Tests (37 archivos)

| Test | Archivo | Cubre |
|---|---|---|
| `AcademiaHierarchyTest` | `tests/Feature/AcademiaHierarchyTest.php` | Jerarquía académica |
| `AcademiaRbacMatrixTest` | `tests/Feature/AcademiaRbacMatrixTest.php` | Matriz RBAC de academia |
| `AdminLayoutComposerTest` | `tests/Feature/AdminLayoutComposerTest.php` | Composer del layout admin |
| `AreaPuestoRelationsTest` | `tests/Feature/AreaPuestoRelationsTest.php` | Relaciones Área-Puesto |
| `AttendanceFilterTest` | `tests/Feature/AttendanceFilterTest.php` | Filtros de asistencia |
| `CicloActualServiceTest` | `tests/Feature/CicloActualServiceTest.php` | CicloActualService |
| `CicloControllerTest` | `tests/Feature/CicloControllerTest.php` | CicloController CRUD |
| `ContratoDeRutasTest` | `tests/Feature/ContratoDeRutasTest.php` | Contrato de rutas |
| `DashboardQueryTest` | `tests/Feature/DashboardQueryTest.php` | Queries del dashboard |
| `DashboardRenderTest` | `tests/Feature/DashboardRenderTest.php` | Renderizado del dashboard |
| `DeviceSyncControllerTest` | `tests/Feature/DeviceSyncControllerTest.php` | DeviceSyncController |
| `EmployeeEditTest` | `tests/Feature/EmployeeEditTest.php` | Edición de empleados |
| `EmployeeEnrollmentDiffTest` | `tests/Feature/EmployeeEnrollmentDiffTest.php` | Diferencia de enrolamiento |
| `EmployeeIndexTest` | `tests/Feature/EmployeeIndexTest.php` | Índice de empleados |
| `EmployeeUpdateCardTest` | `tests/Feature/EmployeeUpdateCardTest.php` | Actualización de tarjeta |
| `EmployeeUpdateTest` | `tests/Feature/EmployeeUpdateTest.php` | Actualización de empleados |
| `ExampleTest` | `tests/Feature/ExampleTest.php` | Test base |
| `FirebirdSyncQueueTest` | `tests/Feature/FirebirdSyncQueueTest.php` | Cola de sync Firebird |
| `FirebirdSyncTest` | `tests/Feature/FirebirdSyncTest.php` | Sync Firebird |
| `FingerprintControllerTest` | `tests/Feature/FingerprintControllerTest.php` | FingerprintController |
| `IncidenciaModuleTest` | `tests/Feature/IncidenciaModuleTest.php` | Módulo de incidencias |
| `ModulePermissionsSeederTest` | `tests/Feature/ModulePermissionsSeederTest.php` | Seeder de módulos |
| `OperationsQueueUnifiedTest` | `tests/Feature/OperationsQueueUnifiedTest.php` | Cola unificada |
| `PermissionGroupsTest` | `tests/Feature/PermissionGroupsTest.php` | Grupos de permisos |
| `PuntualidadModuleTest` | `tests/Feature/PuntualidadModuleTest.php` | Módulo de puntualidad |
| `SobranteRemoveTest` | `tests/Feature/SobranteRemoveTest.php` | Eliminación de sobrantes |
| `SobranteServiceTest` | `tests/Feature/SobranteServiceTest.php` | SobranteService |
| `SyncQueueTest` | `tests/Feature/SyncQueueTest.php` | Cola de sincronización |
| `ZktecoSyncTest` | `tests/Feature/ZktecoSyncTest.php` | Sync ZKTeco |

### 20.2 Permission Tests (5 archivos)

| Test | Archivo |
|---|---|
| `ModulePermissionSeederTest` | `tests/Feature/Permission/ModulePermissionSeederTest.php` |
| `PermissionGroupPermissionsTest` | `tests/Feature/Permission/PermissionGroupPermissionsTest.php` |
| `PermissionResolverCacheTest` | `tests/Feature/Permission/PermissionResolverCacheTest.php` |
| `RequireModulePermissionMiddlewareTest` | `tests/Feature/Permission/RequireModulePermissionMiddlewareTest.php` |
| `UserGroupAssignmentTest` | `tests/Feature/Permission/UserGroupAssignmentTest.php` |
| `UserPermissionInheritanceTest` | `tests/Feature/Permission/UserPermissionInheritanceTest.php` |

### 20.3 Unit Tests
| Test | Archivo |
|---|---|
| `ExampleTest` | `tests/Unit/ExampleTest.php` |

---

## 21. Configuración

### Archivos de config relevantes
| Archivo | Claves relevantes |
|---|---|
| `config/app.php` | Nombre de la app, timezone, locale |
| `config/database.php` | Conexiones: mysql (default), firebird (solo lectura), redis |
| `config/auth.php` | Providers, guards |
| `config/broadcasting.php` | Driver: null (no activo) |
| `config/queue.php` | Driver: sync (default) |
| `config/sanctum.php` | Tokens de API |
| `config/cors.php` | Configuración CORS |

### Variables de entorno relevantes (`.env.example`/`.env`)
- `DB_*` — MySQL
- `FIREBIRD_DSN`, `FIREBIRD_USER`, `FIREBIRD_PASS` — Firebird
- `BROADCAST_DRIVER` — Broadcasting (null)
- `QUEUE_CONNECTION` — Cola

---

## 22. Hallazgos y deuda técnica

### Hallazgos positivos
1. **Arquitectura clara**: Separación por dominios (Checador, Academia, RBAC) con servicios dedicados.
2. **Firebird solo lectura**: Respetado consistentemente; todas las estrategias de sync son unidireccionales.
3. **RBAC completo**: Sistema de módulos → permisos → grupos → usuarios con cache y invalidación.
4. **Cobertura de pruebas razonable**: 38 tests cubriendo los flujos críticos.
5. **Contrato de rutas**: Test dedicado (`ContratoDeRutasTest`) que previene roturas.
6. **Manejo de errores robusto**: ZktecoService con reintentos adaptativos, timeouts progresivos, y manejo tolerante a fallos.
7. **Datos sensibles protegidos**: Migración de encryptación (`2024_01_01_000008`), no salen en logs/responses.

### Deuda técnica identificada
1. **Broadcasting no activo**: `SyncProgressUpdated` implementa `ShouldBroadcast` pero el driver es `null`. El progress polling actual usa AJAX cada 20s en vez de websockets.
2. **`removeUser()` deprecated** en ZktecoService: marcado con `@deprecated` pero aún presente.
3. **Duplicación en layout admin**: El menú antiguo (bloque `@if(false)`) se conserva como referencia muerta.
4. **Sin listeners**: `SyncProgressUpdated` no tiene listeners registrados; el broadcasting es inline.
5. **Schedule vacío**: No hay tareas programadas activas en el Kernel.
6. **API routes sin middleware de auth**: Las rutas `api/academia/*` no tienen `auth:sanctum` ni permisos.
7. **Areas y Puestos sin permisos RBAC**: Las rutas de áreas y puestos no tienen `RequireModulePermission`.

### Estado del proyecto según `ESTADO.md`
- Fase actual: `validado`
- Módulo completado: `employees` (100%)
- UI/UX de employees completada sobre 9 archivos
- 5 GAPs de backend documentados para fase posterior
- Sin modificar rutas, lógica ni contratos
