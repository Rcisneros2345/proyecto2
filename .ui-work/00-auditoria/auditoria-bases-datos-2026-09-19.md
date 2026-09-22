# Auditoría de Bases de Datos — 2026-09-19

> Complemento de la auditoría completa. Análisis exhaustivo de esquemas Firebird y MySQL, mapeo entre ambos, consultas raw, y diferencias de integridad.

---

## Índice

1. [Resumen de tablas](#1-resumen-de-tablas)
2. [Esquema MySQL completo (33 tablas)](#2-esquema-mysql-completo)
3. [Tablas Firebird consultadas (21)](#3-tablas-firebird-consultadas)
4. [Mapeo Firebird → MySQL (tabla por tabla)](#4-mapeo-firebird--mysql)
5. [Diferencias de esquema Firebird vs MySQL](#5-diferencias-de-esquema)
6. [Consultas raw a MySQL en el código](#6-consultas-raw-a-mysql)
7. [Flujo de sincronización](#7-flujo-de-sincronización)
8. [Índices y foreign keys](#8-índices-y-foreign-keys)
9. [Hallazgos de integridad](#9-hallazgos-de-integridad)

---

## 1. Resumen de tablas

| Base de datos | Tablas | Descripción |
|---|---|---|
| **MySQL** | 33 | Base operativa de la aplicación |
| **Firebird** | 21 | Fuente de verdad (solo lectura) |

### MySQL — Tablas por dominio

| Dominio | Tablas |
|---|---|
| **Core/Auth** | users, password_reset_tokens, personal_access_tokens, failed_jobs, jobs, job_batches |
| **Empleados/Checador** | employees, devices, device_employee, fingerprints, attendances, device_syncs, device_sync_items, areas, puestos, incidencias, horarios_laborales |
| **Firebird Sync** | firebird_syncs, firebird_sync_items |
| **RBAC** | modules, permissions, permission_groups, permission_group_permissions, employee_permission_groups, profesor_permission_groups, navigation_items |
| **Academia** | ciclos, alumnos, alumnos_grupos, alumnos_kardex, alumnos_cursos, alumnos_niveles, alumnos_asistencias, grupos, cursos, cursos_det, materias, planes, niveles, sedes, turnos, profesores, contratos, sesiones_base, horarios_det, metodos_eval, docentes_asistencias, grupo_asistencias |

---

## 2. Esquema MySQL completo

### 2.1 `users` (Auth)
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| name | varchar(255) | NO | — | |
| email | varchar(255) | NO | — | UNIQUE |
| email_verified_at | timestamp | SÍ | NULL | |
| password | varchar(255) | NO | — | |
| remember_token | varchar(100) | SÍ | NULL | |
| role | varchar(20) | NO | 'operador' | CHECK: admin, operador, viewer |
| username | varchar(50) | SÍ | NULL | UNIQUE |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 2.2 `employees`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| user_id | varchar(50) | NO | — | PIN/badge único global |
| name | varchar(255) | SÍ | NULL | |
| type | varchar(20) | NO | 'biometric' | biometric, admin, teacher |
| numero_empleado | varchar(20) | SÍ | NULL | EMPLEADOS.NUMEMPLEADO |
| clave_profesor | varchar(20) | SÍ | NULL | PROFESORES.CLAVEPROFESOR |
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

### 2.3 `devices`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| name | varchar(255) | NO | — | |
| ip | varchar(45) | NO | — | UNIQUE |
| port | smallint | NO | 4370 | |
| password | varchar(255) | SÍ | NULL | |
| serial_number | varchar(100) | SÍ | NULL | UNIQUE |
| device_name | varchar(255) | SÍ | NULL | |
| status | enum('online','offline','unknown') | NO | 'unknown' | |
| last_seen_at | timestamp | SÍ | NULL | |
| description | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 2.4 `device_employee` (Pivote)
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | NO | — | FK→devices.id CASCADE |
| employee_id | bigint | NO | — | FK→employees.id CASCADE |
| device_uid | int | NO | — | UID local en checador |
| role | tinyint | NO | 0 | |
| card_number | varchar(50) | SÍ | NULL | UNIQUE por device |
| password | varchar(255) | SÍ | NULL | |
| active | tinyint | NO | 1 | |
| fingerprint_count | int | NO | 0 | Cache |
| ignored_at | timestamp | SÍ | NULL | Sobrantes |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 2.5 `fingerprints`
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

### 2.6 `attendances`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | NO | — | FK→devices.id CASCADE |
| employee_id | bigint | SÍ | NULL | FK→employees.id SET NULL |
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

### 2.7 `device_syncs`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_id | bigint | NO | — | FK→devices.id CASCADE |
| status | varchar(20) | NO | 'pending' | pending, running, completed, failed |
| operation | varchar(50) | NO | 'all' | all, users, attendances, fingerprints |
| stage | varchar(50) | SÍ | NULL | |
| employee_id | bigint | SÍ | NULL | |
| started_at | timestamp | SÍ | NULL | |
| finished_at | timestamp | SÍ | NULL | |
| processed | int | NO | 0 | |
| total | int | NO | 0 | |
| created_count | int | NO | 0 | |
| updated_count | int | NO | 0 | |
| error_message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 2.8 `device_sync_items`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| device_sync_id | bigint | NO | — | FK→device_syncs.id CASCADE |
| fingerprint_id | bigint | SÍ | NULL | FK→fingerprints.id SET NULL |
| credential_type | varchar(20) | SÍ | NULL | |
| finger | tinyint | SÍ | NULL | |
| status | varchar(20) | NO | 'pending' | |
| attempts | tinyint | NO | 0 | |
| message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 2.9 `firebird_syncs`
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

### 2.10 `firebird_sync_items`
| Columna | Tipo | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint | NO | auto | PK |
| firebird_sync_id | bigint | NO | — | FK→firebird_syncs.id CASCADE |
| table_name | varchar(100) | NO | — | |
| record_id | varchar(100) | SÍ | NULL | |
| action | varchar(20) | NO | — | created, updated, deleted, skipped |
| status | varchar(20) | NO | 'pending' | |
| message | text | SÍ | NULL | |
| created_at | timestamp | SÍ | NULL | |
| updated_at | timestamp | SÍ | NULL | |

### 2.11 RBAC
| Tabla | Columnas clave | FKs |
|---|---|---|
| `modules` | slug (UNIQUE), name, active, icon, sort_order, is_system, group_name | — |
| `permissions` | module_id (FK→modules), slug (UNIQUE), action (UNIQUE por módulo) | FK→modules |
| `permission_groups` | name, is_default | — |
| `permission_group_permissions` | permission_group_id, permission_id | FK→permission_groups, FK→permissions |
| `employee_permission_groups` | employee_id, permission_group_id | FK→employees, FK→permission_groups |
| `profesor_permission_groups` | profesor_clave_profesor, permission_group_id | FK→profesores, FK→permission_groups |
| `navigation_items` | module_id, section, label, route_name, icon, permission_action, sort_order, admin_only, active | FK→modules |

### 2.12 Catálogos simples
| Tabla | Columnas clave |
|---|---|
| `areas` | id, identificador (UNIQUE), descripcion, empleado_responsable_id (FK→employees) |
| `puestos` | id, identificador (UNIQUE), descripcion |
| `incidencias` | id, employee_id, type, description, date, status |
| `horarios_laborales` | id, employee_id, dia_semana, hora_entrada, hora_salida, tolerancia_min |

### 2.13 Academia
| Tabla | Columnas PK/UK | FKs principales |
|---|---|---|
| `ciclos` | UK: (inicial, final, periodo) | — |
| `alumnos` | UK: numero_alumno, curp | — |
| `alumnos_grupos` | UK: (numero_alumno, codigo_grupo, inicial, final, periodo) | FK→grupos, FK→alumnos |
| `alumnos_kardex` | UK: (numero_alumno, inicial, final, periodo, clave_asignatura, id_eval) | FK→materias, FK→metodos_eval, FK→alumnos |
| `alumnos_cursos` | UK: (inicial, final, periodo, codigo_curso, numero_alumno, id_tipoeval, id_etapa, clave_asignatura, version, tipoexamen) | — |
| `alumnos_niveles` | UK: (numero_alumno, inicial, final, periodo) | FK→alumnos |
| `alumnos_asistencias` | UK: (inicial, final, periodo, numero_alumno, codigo_grupo, clave_profesor, clave_asignatura, dia, sesion, fecha) | — |
| `grupos` | UK: (codigo_grupo, inicial, final, periodo) | — |
| `cursos` | UK: (inicial, final, periodo, clave_curso) | — |
| `cursos_det` | UK: (curso_id, clave_asignatura), UK: (curso_id, inicial, final, periodo, codigo_curso, dia, hora_inicial, hora_final, id_campus, edificio, aula) | FK→cursos, FK→materias |
| `materias` | UK: clave_asignatura | — |
| `planes` | UK: id_plan | FK→niveles |
| `niveles` | UK: clave_nivel | — |
| `sedes` | UK: id_campus | — |
| `turnos` | UK: clave_turno | — |
| `profesores` | UK: clave_profesor | FK→users(auth_user_id) |
| `contratos` | UK: contrato | — |
| `sesiones_base` | UK: (nivel, turno, sesion) | — |
| `horarios_det` | — | FK→grupos, FK→profesores, FK→materias, FK→sedes, FK→sesiones_base |
| `metodos_eval` | UK: id_eval | — |
| `docentes_asistencias` | — | — |
| `grupo_asistencias` | — | — |

---

## 3. Tablas Firebird consultadas

| # | Tabla Firebird | Grupo | Descripción |
|---|---|---|---|
| 1 | `CFGSEDES` | base | Sedes/campus |
| 2 | `CFGNIVELES` | base | Niveles educativos |
| 3 | `CFGTURNOS` | base | Turnos escolares |
| 4 | `CICLOS` | base | Ciclos escolares |
| 5 | `CFGPLANES_MST` | base | Planes de estudio (maestro) |
| 6 | `CFGPLANES_DET` | base | Materias por plan (detalle) |
| 7 | `CFGTIPOSEVALUACION` | base | Métodos de evaluación |
| 8 | `EMPLEADOS_CONTRATOS_CAT` | base | Catálogo de contratos |
| 9 | `CFGSESIONES` | ciclo | Sesiones base por nivel/turno |
| 10 | `PROFESORES` | base | Profesores |
| 11 | `EMPLEADOS` | base | Empleados (nómina) |
| 12 | `EMPLEADOS_CFGHORARIOS` | base | Configuración de horarios laborales |
| 13 | `EMPLEADOS_CFGHORARIOS_DET` | base | Detalle de horarios laborales |
| 14 | `EMPLEADOS_HORARIOS` | base | Asignación de horarios a empleados |
| 15 | `PROFESORES_HORARIOS` | base | Horarios de profesores |
| 16 | `PROFESORES_HORARIOS_DET` | base | Detalle de horarios de profesores |
| 17 | `GRUPOS` | ciclo | Grupos por ciclo |
| 18 | `HORARIOS_DET` | ciclo | Horarios de clases |
| 19 | `CURSOS` | ciclo | Cursos por ciclo |
| 20 | `CURSOS_DET` | ciclo | Detalle de cursos (materias) |
| 21 | `ALUMNOS` | alumnos | Alumnos |
| 22 | `ALUMNOS_GRUupos` | ciclo | Inscripciones de alumnos a grupos |
| 23 | `ALUMNOS_CURSOS` | ciclo | Inscripciones de alumnos a cursos |
| 24 | `ALUMNOS_NIVELES` | alumnos | Niveles de alumnos por ciclo |

---

## 4. Mapeo Firebird → MySQL

### 4.1 Catálogos base (CatalogSmartSync)

| Firebird | MySQL | Columnas mapeadas (MySQL → Firebird) |
|---|---|---|
| `CFGSEDES` | `sedes` | id_campus←ID_CAMPUS, descripcion←DESCRIPCION, direccion←DOMICILIO |
| `CFGNIVELES` | `niveles` | nivel←NIVEL, descripcion←DESCRIPCION |
| `CFGTURNOS` | `turnos` | turno←TURNO, descripcion←DESCRIPCIONTURNO |
| `CICLOS` | `ciclos` | inicial←INICIAL, final←FINAL, periodo←PERIODO, descripcion←DESCRIPCION, fecha_inicial←FECHA_INICIAL, fecha_final←FECHA_FINAL, activo←ACTIVO |
| `CFGPLANES_MST` | `planes` | id_plan←ID_PLAN, nombre_plan←NOMBRE_PLAN, nivel←NIVEL |
| `CFGPLANES_DET` | `materias` | clave_asignatura←CLAVEASIGNATURA, id_plan←ID_PLAN, nombre_asignatura←NOMBREASIGNATURA, nombre_corto←NOMBRECORTO, creditos←CREDITOS, horas_teoria←HORAS_TEORIA, horas_practica←HORAS_PRACTICA |
| `CFGTIPOSEVALUACION` | `metodos_eval` | id_eval←ID_TIPOEVAL, descripcion←DESCRIPCION |
| `EMPLEADOS_CONTRATOS_CAT` | `contratos` | contrato←CONTRATO, descripcion←DESCRIPCION |
| `CFGSESIONES` | `sesiones_base` | nivel←NIVEL, turno←TURNO, sesion←SESION, descripcion←DESCRIPCION, hora_inicio←HORA_INICIO, hora_fin←HORA_FIN, receso←RECESO |
| `EMPLEADOS` | `employees` | user_id←NUMEMPLEADO, name←NOMBREEMPLEADO, numero_empleado←NUMEMPLEADO, departamento←DEPARTAMENTO, cargo←CARGO, contrato←CONTRATO, status_actual←STATUSACTUAL, fecha_ingreso←FECHA_INGRESO, id_campus←ID_CAMPUS, nivel←NIVEL, tarjeta_id←TARJETA_ID |
| `PROFESORES` | `profesores` | clave_profesor←CLAVEPROFESOR, nombre_profesor←NOMBREPROFESOR, departamento←DEPARTAMENTO, contrato←CONTRATO, status_actual←STATUSACTUAL, origen_horario←ORIGEN_HORARIO, fecha_ingreso←FECHA_INGRESO, id_campus←ID_CAMPUS, nivel←NIVEL, email←EMAIL |
| `EMPLEADOS_CFGHORARIOS` | `empleados_cfghorarios` | id_escuela←ID_ESCUELA, horario←HORARIO, nombre_horario←NOMBRE_HORARIO, tipo_horario←TIPO_HORARIO, tolerancia_entrada←TOLERANCIA_ENTRADA, tolerancia_regresodecomer←TOLERANCIA_REGRESODECOMER |
| `EMPLEADOS_CFGHORARIOS_DET` | `empleados_cfghorarios_det` | id_escuela←ID_ESCUELA, horario←HORARIO, dia_entrada←DIA_ENTRADA, hora_entrada←HORA_ENTRADA, dia_salida←DIA_SALIDA, hora_salida←HORA_SALIDA, receso_comida←RECESO_COMIDA, dia_salidaacomer←DIA_SALIDAACOMER, hora_salidaacomer←HORA_SALIDAACOMER, dia_regresodecomer←DIA_REGRESODECOMER, hora_regresodecomer←HORA_REGRESODECOMER, horas_variables←HORAS_VARIABLES |
| `EMPLEADOS_HORARIOS` | `empleados_horarios` | id_escuela←ID_ESCUELA, numempleado←NUMEMPLEADO, horario←HORARIO, fecha_inicial←FECHA_INICIAL, fecha_final←FECHA_FINAL |
| `PROFESORES_HORARIOS` | `profesores_horarios` | id_escuela←ID_ESCUELA, id_profesores_horarios←ID_PROFESORES_HORARIOS, descripcion←DESCRIPCION, fecha_desde←FECHA_DESDE, fecha_hasta←FECHA_HASTA |
| `PROFESORES_HORARIOS_DET` | `profesores_horarios_det` | id_escuela←ID_ESCUELA, id_profesores_horarios←ID_PROFESORES_HORARIOS, id_num←ID_NUM, dia←DIA, hora_desde←HORA_DESDE, hora_hasta←HORA_HASTA |

### 4.2 Datos de ciclo (CycleDirectSync)

| Firebird | MySQL | Columnas mapeadas |
|---|---|---|
| `GRUPOS` | `grupos` | codigo_grupo←CODIGO_GRUPO, inicial←INICIAL, final←FINAL, periodo←PERIODO, grado←GRADO, turno←TURNO, nivel←NIVEL, inscritos←INSCRITOS, id_campus←ID_CAMPUS |
| `HORARIOS_DET` | `horarios_det` | inicial←INICIAL, final←FINAL, periodo←PERIODO, codigo_grupo←CODIGO_GRUPO, clave_profesor←CLAVEPROFESOR, clave_asignatura←CLAVEASIGNATURA, dia←DIA, sesion←SESION, horas_teoria_practica←HORAS_TEORIA_PRACTICA, id_campus←ID_CAMPUS, edificio←EDIFICIO, aula←AULA |
| `CURSOS` | `cursos` | inicial←INICIAL, final←FINAL, periodo←PERIODO, clave_curso←CODIGO_CURSO, nombre_curso←DESCRIPCION, codigo_grupo←CODIGO_GRUPO, nivel←NIVEL, turno←TURNO, id_campus←ID_CAMPUS |
| `CURSOS_DET` | `cursos_det` | curso_id←(resuelto vía CURSOS), clave_asignatura←CLAVEASIGNATURA, dia←DIA, hora_inicial←HORA_INICIAL, hora_final←HORA_FINAL, id_campus←ID_CAMPUS, edificio←EDIFICIO, aula←AULA |
| `ALUMNOS` | `alumnos` | numero_alumno←NUMEROALUMNO, matricula←MATRICULA, matricula_oficial←MATRICULA_OFICIAL, paterno←PATERNO, materno←MATERNO, nombre←NOMBRE, sexo←GENERO, fecha_nacimiento←FECHA_NACIMIENTO, estado_civil←ESTADO_CIVIL, direccion←DOMICILIO, cp←CP, ciudad←CIUDAD, estado←ESTADO, telefono←TELEFONO, celular←CELULAR, email←EMAIL, lugar_nacimiento←LUGAR_NACIMIENTO, nacionalidad←NACIONALIDAD, nivel←NIVEL, turno←TURNO, id_campus←ID_CAMPUS, id_escuela←ID_ESCUELA, grado←GRADO, subnivel←SUBNIVEL, fecha_ingreso←FECHA_INGRESO, estatus←STATUS, observaciones←OBSERVACIONES |
| `ALUMNOS_GRUPOS` | `alumnos_grupos` | numero_alumno←NUMEROALUMNO, codigo_grupo←CODIGO_GRUPO, inicial←INICIAL, final←FINAL, periodo←PERIODO |
| `ALUMNOS_CURSOS` | `alumnos_cursos` | inicial←INICIAL, final←FINAL, periodo←PERIODO, codigo_curso←CODIGO_CURSO, numero_alumno←NUMEROALUMNO |
| `ALUMNOS_NIVELES` | `alumnos_niveles` | numero_alumno←NUMEROALUMNO, inicial←INICIAL, final←FINAL, periodo←PERIODO, nivel←NIVEL, grado←GRADO, status←STATUS |

---

## 5. Diferencias de esquema Firebird vs MySQL

### 5.1 Nombres de columnas diferentes

| MySQL | Firebird | Razón del cambio |
|---|---|---|
| `employees.user_id` | `EMPLEADOS.NUMEMPLEADO` | Mismo dato, nombre diferente |
| `employees.name` | `EMPLEADOS.NOMBREEMPLEADO` | Normalizado |
| `employees.status_actual` | `EMPLEADOS.STATUSACTUAL` | Normalizado |
| `alumnos.sexo` | `ALUMNOS.GENERO` | Normalizado |
| `alumnos.estatus` | `ALUMNOS.STATUS` | Normalizado |
| `alumnos.direccion` | `ALUMNOS.DOMICILIO` | Normalizado |
| `cursos.clave_curso` | `CURSOS.CODIGO_CURSO` | Normalizado |
| `cursos.nombre_curso` | `CURSOS.DESCRIPCION` | Normalizado |
| `materias.clave_asignatura` | `CFGPLANES_DET.CLAVEASIGNATURA` | Normalizado |
| `materias.nombre_asignatura` | `CFGPLANES_DET.NOMBREASIGNATURA` | Normalizado |
| `metodos_eval.id_eval` | `CFGTIPOSEVALUACION.ID_TIPOEVAL` | Normalizado |
| `turnos.descripcion` | `CFGTURNOS.DESCRIPCIONTURNO` | Normalizado |
| `sesiones_base.descripcion` | `CFGSESIONES.DESCRIPCION` | Normalizado |

### 5.2 Columnas que Firebird tiene pero MySQL NO

| Tabla Firebird | Columnas ausentes en MySQL | Notas |
|---|---|---|
| `EMPLEADOS` | CHECADOR, ULTIMOCHECADOR, FECHaultimochecado, etc. | Metadatos de checador no sync |
| `PROFESORES` | Muchas columnas de catálogo interno | Solo se mapean ~10 de ~30 |
| `ALUMNOS` | COLONIA, ID_ESCUELA, CARRERA, PLAN, TIPO_INGRESO | Algunas sí se mapean |
| `HORARIOS_DET` | ORIGEN_HORARIO, ACTIVO | ORIGEN_HORARIO se propaga post-sync |
| `CURSOS` | CLAVEPROFESOR, DESDE, HASTA, SESIONES, INSCRITOS, SUPLENTE | Solo ~9 de ~20 columnas |
| `CFGSESIONES` | ORDEN | No mapeado |
| `ALUMNOS_GRUPOS` | FECHA_INSCRIPCION, ESTATUS, OBSERVACIONES | Solo 5 columnas de ~8 |

### 5.3 Columnas que MySQL tiene pero Firebird NO (creadas localmente)

| Tabla MySQL | Columnas exclusivas | Origen |
|---|---|---|
| `employees` | type, auth_user_id, area_id, puesto_id | Laravel/app |
| `device_employee` | device_uid, role, card_number, password, active, fingerprint_count, ignored_at | ZKTeco sync |
| `fingerprints` | device_id, template, template_hash | ZKTeco sync |
| `attendances` | device_id, employee_id, attendance_type, source, hora_*, recorded_at | ZKTeco sync |
| `device_syncs` | status, operation, stage, processed, total, ... | App local |
| `firebird_syncs` | status, ciclo, strategy, ... | App local |
| `modules` | slug, active, icon, sort_order, is_system, group_name | RBAC local |
| `permissions` | module_id, slug, action | RBAC local |
| `navigation_items` | module_id, section, label, route_name, ... | RBAC local |
| `horarios_det` | origen_horario, activo, sesion | Propagado post-sync |

### 5.4 Tipos de datos diferentes

| MySQL | Firebird | Transformación |
|---|---|---|
| `tinyint(1)` (activo) | `VARCHAR(1)` ('S'/'N') | `CatalogSmartSync` convierte: S/SI/Y/YES/1/TRUE → true, resto → false |
| `date` | `DATE` | Directo |
| `datetime` | `TIMESTAMP` | Directo |
| `varchar` | `VARCHAR` | Trim automático via `FirebirdReader::cleanRows()` |
| `int` | `INTEGER` | Directo |
| `decimal` | `DECIMAL` | Directo |

### 5.5 Valores por defecto aplicados

| Tabla MySQL | Columna | Default | Razón |
|---|---|---|---|
| `sesiones_base` | receso | false | Firebird puede enviar NULL |
| `materias` | creditos | 0 | Firebird puede enviar NULL |

---

## 6. Consultas raw a MySQL en el código

### 6.1 `ZktecoService` (app/Services/ZktecoService.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 391 | `device_employee` | SELECT EXISTS | Verificar tarjeta duplicada |
| 439 | `attendances` | INSERT OR IGNORE | Insertar asistencia descargada del checador |
| 904 | `device_employee` | SELECT MAX(device_uid) | Calcular siguiente UID |

### 6.2 `SobranteService` (app/Services/SobranteService.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 84 | `device_employee` LEFT JOIN `employees` JOIN `devices` | SELECT con CASE | Query base de sobrantes |
| 190 | `device_employee` | SELECT EXISTS | Verificar existencia |
| 198 | `device_employee` | UPDATE (ignored_at) | Marcar ignorado |
| 213 | `device_employee` | SELECT EXISTS | Verificar existencia |
| 221 | `device_employee` | UPDATE (ignored_at=NULL) | Des-ignorar |

### 6.3 `HorarioResolver` (app/Services/HorarioResolver.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 58 | `alumnos_grupos` | SELECT COUNT + GROUP BY | Contar alumnos por grupo |
| 134 | `horarios_det` LEFT JOIN `grupos` LEFT JOIN `docentes_asistencias` | SELECT con agregaciones | Estadísticas de asistencia del día |
| 267 | `horarios_det` | SELECT + GROUP BY + HAVING | Detectar conflictos de aula |

### 6.4 `KardexCalculator` (app/Services/KardexCalculator.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 136 | `alumnos_kardex` JOIN `materias` JOIN `metodos_eval` | SELECT + GROUP BY | Historial completo de kárdex |

### 6.5 `EmployeeCatalogMover` (app/Services/EmployeeCatalogMover.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 67 | `employees` | SELECT con GROUP BY HAVING | Detectar duplicados por user_id |
| 74 | `employees` | SELECT WHERE IN | Obtener duplicados |
| 84 | `attendances` | UPDATE SET employee_id | Repuntar asistencias al consolidado |
| 92 | `employees` | DELETE | Eliminar empleado duplicado |
| 102 | `fingerprints` | SELECT con GROUP BY | Contar huellas por empleado |
| 137 | `device_employee` | INSERT | Crear enrolamiento |
| 190 | `fingerprints` | SELECT | Verificar conflictos |
| 207 | `fingerprints` | INSERT | Copiar huella |
| 219 | `device_employee` | SELECT COUNT | Contar enrolamientos |

### 6.6 `AttendanceController` (app/Http/Controllers/AttendanceController.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 96 | `docentes_asistencias as da` JOIN `horarios_det as h` | SELECT | Clases del día con estado de asistencia |

### 6.7 `DeviceController` (app/Http/Controllers/DeviceController.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 38 | `$table` (variable) | SELECT COUNT | Contar registros de cualquier tabla |
| 126 | `device_employee` | SELECT created_at | Verificar si hay enrolamientos |
| 299 | `employees` | DELETE WHERE device_id | Eliminar empleados de dispositivo |
| 314 | `attendances` | DELETE WHERE device_id | Eliminar asistencias de dispositivo |
| 327 | `fingerprints` | DELETE WHERE device_id | Eliminar huellas de dispositivo |

### 6.8 `CursoController` (app/Http/Controllers/Academia/CcursoController.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 43 | `horarios_det` | SELECT DISTINCT | Docentes de una materia |
| 56 | `alumnos_cursos` | SELECT COUNT | Contar alumnos inscritos |
| 62 | `alumnos_grupos` | SELECT COUNT | Contar alumnos por grupo |
| 106 | `horarios_det` | SELECT DISTINCT | Docentes disponibles |
| 121 | `alumnos_cursos as ac` JOIN `alumnos as a` | SELECT + ORDER BY | Lista de alumnos inscritos |
| 139 | `alumnos as a` JOIN `alumnos_grupos as ag` | SELECT + ORDER BY | Alumnos del grupo |

### 6.9 `User` model (app/Models/User.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 99 | `employee_permission_groups` | SELECT WHERE IN | Grupos de empleado |
| 106 | `profesor_permission_groups` | SELECT WHERE IN | Grupos de profesor |

### 6.10 `SyncEmployeeToDeviceJob` (app/Jobs/SyncEmployeeToDeviceJob.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 132 | `device_sync_items` | UPDATE SET attempts+1 | Incrementar intentos |
| 142 | `device_sync_items` | UPDATE SET attempts+1 | Incrementar intentos (error) |

### 6.11 `FirebirdController` (app/Http/Controllers/FirebirdController.php)

| Línea | Tabla | Operación | Descripción |
|---|---|---|---|
| 194 | `jobs` | SELECT COUNT | Verificar si hay worker activo |

---

## 7. Flujo de sincronización

### 7.1 Firebird → MySQL (CatalogSmartSync)

```
CFGSEDES      → sedes
CFGNIVELES    → niveles
CFGTURNOS     → turnos
CICLOS        → ciclos
CFGPLANES_MST → planes
CFGPLANES_DET → materias
CFGTIPOSEVALUACION → metodos_eval
EMPLEADOS_CONTRATOS_CAT → contratos
CFGSESIONES   → sesiones_base
EMPLEADOS     → employees
PROFESORES    → profesores
EMPLEADOS_CFGHORARIOS → empleados_cfghorarios
EMPLEADOS_CFGHORARIOS_DET → empleados_cfghorarios_det
EMPLEADOS_HORARIOS → empleados_horarios
PROFESORES_HORARIOS → profesores_horarios
PROFESORES_HORARIOS_DET → profesores_horarios_det
```

### 7.2 Firebird → MySQL (CycleDirectSync)

```
GRUPOS        → grupos
CURSOS        → cursos
CURSOS_DET    → cursos_det
HORARIOS_DET  → horarios_det
ALUMNOS       → alumnos
ALUMNOS_NIVELES → alumnos_niveles
ALUMNOS_GRUPOS  → alumnos_grupos
ALUMNOS_CURSOS  → alumnos_cursos
```

### 7.3 ZKTeco → MySQL (ZktecoService)

```
Dispositivo → device_employee (pivote)
Dispositivo → fingerprints
Dispositivo → attendances
```

### 7.4 Transformaciones en el camino

| Transformación | Dónde | Descripción |
|---|---|---|
| `VARCHAR 'S'/'N'` → `BOOLEAN` | `CatalogSmartSync` | Columnas receso, activo |
| `trim()` automático | `FirebirdReader::cleanRows()` | Todos los strings |
| `NULL`/`''` → `NULL` | `FirebirdReader::cleanRows()` | Normalización |
| Defaults para NOT NULL | `CatalogSmartSync::COLUMN_DEFAULTS` | receso→false, creditos→0 |
| Deduplicación | `CatalogSmartSync` | Por identity key, último gana |
| `ORIGEN_HORARIO` propagación | `FullSyncStrategy::propagateOrigenHorario()` | profesores → horarios_det |

---

## 8. Índices y foreign keys

### 8.1 Índices de rendimiento críticos

| Tabla | Índice | Columnas | Tipo |
|---|---|---|---|
| `attendances` | `attendance_device_employee_unique` | device_id, employee_id, recorded_at | UNIQUE |
| `attendances` | `att_employee_recorded_id_idx` | employee_id, recorded_at, id | INDEX |
| `attendances` | `att_device_recorded_idx` | device_id, recorded_at | INDEX |
| `attendances` | `idx_attendances_type_recorded_at` | type, recorded_at | INDEX |
| `device_employee` | `device_employee_device_id_device_uid_unique` | device_id, device_uid | UNIQUE |
| `device_employee` | `device_employee_device_id_card_number_unique` | device_id, card_number | UNIQUE |
| `employees` | `idx_employees_status_actual` | status_actual | INDEX |
| `devices` | `devices_status_updated_idx` | status, updated_at | INDEX |
| `alumnos` | `alumnos_curp_unique` | curp | UNIQUE |
| `alumnos` | `alumnos_numero_alumno_unique` | numero_alumno | UNIQUE |
| `alumnos` | `idx_alumnos_estatus_nivel_turno` | estatus, nivel, turno | INDEX |
| `alumnos_grupos` | `uk_alumnos_grupos_pk_compuesta` | numero_alumno, codigo_grupo, inicial, final, periodo | UNIQUE |
| `alumnos_kardex` | `uk_alumnos_kardex_pk_compuesta` | numero_alumno, inicial, final, periodo, clave_asignatura, id_eval | UNIQUE |
| `cursos` | `uk_cursos_pk_compuesta` | inicial, final, periodo, clave_curso | UNIQUE |
| `cursos_det` | `uk_cursos_det_curso_asignatura` | curso_id, clave_asignatura | UNIQUE |
| `horarios_det` | (compuesta por FKs) | inicial, final, periodo, codigo_grupo, ... | — |

### 8.2 Foreign Keys principales

| Tabla | FK | Referencia | ON DELETE |
|---|---|---|---|
| `device_employee` | device_id | devices.id | CASCADE |
| `device_employee` | employee_id | employees.id | CASCADE |
| `fingerprints` | device_id | devices.id | CASCADE |
| `fingerprints` | employee_id | employees.id | CASCADE |
| `attendances` | device_id | devices.id | CASCADE |
| `attendances` | employee_id | employees.id | SET NULL |
| `employees` | area_id | areas.id | SET NULL |
| `employees` | puesto_id | puestos.id | SET NULL |
| `employees` | auth_user_id | users.id | SET NULL |
| `areas` | empleado_responsable_id | employees.id | SET NULL |
| `alumnos_grupos` | codigo_grupo+inicial+final+periodo | grupos | CASCADE |
| `alumnos_grupos` | numero_alumno | alumnos | CASCADE |
| `alumnos_kardex` | clave_asignatura | materias | RESTRICT |
| `alumnos_kardex` | id_eval | metodos_eval | RESTRICT |
| `alumnos_kardex` | numero_alumno | alumnos | CASCADE |
| `alumnos_niveles` | numero_alumno | alumnos | CASCADE |
| `cursos_det` | curso_id | cursos | CASCADE |
| `cursos_det` | clave_asignatura | materias | RESTRICT |
| `device_syncs` | device_id | devices.id | CASCADE |
| `device_sync_items` | device_sync_id | device_syncs.id | CASCADE |
| `firebird_sync_items` | firebird_sync_id | firebird_syncs.id | CASCADE |
| `permission_group_permissions` | permission_group_id | permission_groups.id | CASCADE |
| `permission_group_permissions` | permission_id | permissions.id | CASCADE |
| `employee_permission_groups` | employee_id | employees.id | CASCADE |
| `employee_permission_groups` | permission_group_id | permission_groups.id | CASCADE |

---

## 9. Hallazgos de integridad

### 9.1 Positivos
1. **Cascada completa**: Borrar un device employee elimina fingerprints y attendances vía CASCADE.
2. **SET NULL inteligente**: Borrar un employee no elimina asistencias (se preservan historial).
3. **UK compuestas fuertes**: Tablas académicas tienen UKs que previenen duplicados a nivel de BD.
4. **INSERT IGNORE**: El sync usa INSERT IGNORE para ser idempotente.
5. **Deduplicación en sync**: `CatalogSmartSync` deduplica por identity key antes de insertar.
6. **Validación de columnas Firebird**: El sync verifica que las columnas existan antes de leer.

### 9.2 Riesgos identificados
1. **Sin FK en `alumnos_grupos` a `alumnos_grupos`** (ciclo heredado): La sync de ALUMNOS_GRUPOS hereda el ciclo desde GRUPOS porque Firebird no tiene las columnas de ciclo. Esto es un riesgo si un grupo no existe en MySQL.
2. **`horarios_det` sin FK a `sesiones_base`**: La FK existe pero el campo `sesion` puede venir de Firebird con valores no contemplados en sesiones_base.
3. **`attendances.employee_id` nullable**: Si un empleado no está en el catálogo, la asistencia se guarda con employee_id NULL. Esto es intencional pero dificulta reportes.
4. **`employees.user_id` no es FK a `users`**: El user_id de employees es el PIN/badge del checador, no una FK a users. La FK real es `auth_user_id`.
5. **Sync de ALUMNOS sin filtro ciclo**: `CycleDirectSync` sincroniza TODOS los alumnos sin filtro de ciclo, lo que puede ser lento con volúmenes grandes.
6. **Columnas Firebird no mapeadas**: Muchas columnas de Firebird se descartan en el sync (ver sección 5.2). Si la app necesita esos datos, habría que agregar el mapeo.
7. **`firebird_syncs` sin FK a `devices`**: A diferencia de `device_syncs`, `firebird_syncs` no tiene relación con dispositivos.

### 9.3 Deuda técnica
1. **Tablas huérfanas en MySQL**: `empleados_cfghorarios`, `empleados_cfghorarios_det`, `empleados_horarios`, `profesores_horarios`, `profesores_horarios_det` se sincronizan pero no se usan en la app (solo en el mapeo de CatalogSmartSync).
2. **`ALUMNOS_KARDEX` excluido del sync**: `CycleDirectSync` tiene la constante `TABLAS_ALUMNOS_CICLO` que excluye `ALUMNOS_KARDEX` del sync directo.
3. **`docentes_asistencias` y `grupo_asistencias`**: Tablas existen en MySQL pero no tienen mapeo Firebird visible. Podrían ser capturadas localmente o ser legacy.
