# Matriz RBAC de rutas

Fecha: 2026-09-16  
Regla: ocultar navegación no autoriza una operación. La autorización efectiva combina middleware de módulo, rol global y policy cuando aplica.

## Convenciones

- `admin`: bypass global o autorización administrativa explícita.
- `module_permission:slug,action`: permiso heredado desde los grupos del usuario.
- `policy`: autorización por recurso; debe probarse además del middleware.
- Las rutas y nombres existentes se conservan. Cualquier reorganización del menú requiere Route Safety.

## Academia

| Ruta / conjunto | GET/index/show | POST/create | PUT/PATCH | DELETE | Policy / nota |
|---|---|---|---|---|---|
| `academia.ciclos.*` | `academia.ciclos,view` | `academia.ciclos,create` | `academia.ciclos,update` | `academia.ciclos,delete` | `CicloPolicy`; `activo` usa acción `activo`. |
| `academia.grupos.*` | `academia.grupos,view` heredado del hub | No expone create | POST asistencia requiere matriz específica | No expone delete | Revisar si captura de asistencia debe ser `asistencia` y no solo `view`. |
| `academia.alumnos.*` | `academia.alumnos,view` recomendado | No expone create | No expone update | No expone delete | Kardex/historial son lectura. |
| `academia.profesores.*` | `academia.profesores,view` recomendado | No expone create | Usuario: `academia.profesores,usuario` + `admin` | No expone delete | `ProfesorPolicy` hoy es admin-only para view/update/delete. |
| `academia.cursos.*` | `academia.cursos,view` | `academia.cursos,create` | `academia.cursos,update` | `academia.cursos,delete` | `materia` usa acción `materia`. |
| `academia.planes.*` | `academia.planes,view` | `academia.planes,create` | `academia.planes,update` | `academia.planes,delete` | `PlanPolicy`; validar también endpoints AJAX. |
| `academia.horarios.*` | `academia.horarios,view` | Asistencia: acción pendiente de definir | No aplica | No aplica | Separar consulta de captura en la matriz. |
| `academia.kardex.*` | `academia.alumnos,view` o permiso dedicado | No aplica | No aplica | No aplica | Elegir una sola semántica y documentarla. |

## Operación y administración

| Superficie | Acciones esperadas | Estado |
|---|---|---|
| Empleados | `view`, `create`, `update`, `delete`, `sync`/`enroll` | Middleware explícito en las acciones principales; revisar policies para recursos propios. |
| Dispositivos | `view`, `create`, `update`, `delete`, `sync` | Separación existente; probar cada POST/DELETE con operador, grupo y admin. |
| Firebird | `view`, `sync`, `execute`, `cancel`, `retry` | Varias operaciones usan `admin`; documentar si el permiso de módulo también debe ser obligatorio. |
| Usuarios | `view`, `create`, `update`, `delete`, `reset-password` | Gestión implementada; comprobar que no se pueda elevar `role` sin admin. |
| Grupos/permisos | administrar grupos, permisos y asignaciones | Actualmente bajo `admin`; mantener hasta cerrar matriz granular. |

## Matriz mínima de pruebas por método HTTP

Para cada fila operativa se deben ejecutar tres actores:

1. **Operador sin grupo:** espera `403` en acciones protegidas.
2. **Usuario con grupo:** espera `200/201/302` solo para acciones asignadas y `403` para las demás.
3. **Administrador:** espera éxito en todas las acciones permitidas por la ruta.

Casos mínimos: `GET index/show`, `GET create/edit`, `POST store`, `PUT/PATCH update`, `DELETE destroy`, y POSTs especiales (`activo`, `asistencia`, `materia`, `usuario`, sync).

## Criterio de cierre

La matriz se considera cerrada cuando cada ruta de `route:list` tenga módulo, acción, middleware, policy y tres pruebas de actor. La navegación se valida aparte y nunca sustituye estas pruebas.
