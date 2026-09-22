---
decision: D-002
modulo: AUTH_RBAC
fecha: 2026-09-20
estado: DECIDIDA
decidido_por: humano
---

# D-002 · Siete rutas autenticadas sin permiso de módulo

## El problema en una frase

`php scripts/permisos.php` reporta 13 rutas en zona gris (autenticado, sin
`module_permission`). Cuatro son correctas así (`login`, `logout`, `dashboard`, `api/user`).
Las otras siete exponen superficie operativa a cualquier usuario con sesión:

| Ruta | URI | Qué expone |
|---|---|---|
| `devices.check-status` | POST devices/{device}/check-status | consulta al equipo |
| `employees.sobrantes` | GET employees/sobrantes | pantalla de sobrantes |
| `employees.sobrantes.data` | GET employees/sobrantes/data | **datos de empleados del checador en JSON** |
| `firebird.index` | GET firebird | panel de sincronizaciones |
| `firebird.sync` | GET firebird/sync/{sync} | detalle de una sincronización |
| `firebird.status` | GET firebird/{sync}/status | estado de una sincronización |
| `operations.notifications` | GET notifications | notificaciones del sistema |
| `dashboard.kpisJson` | GET kpis/json | KPIs agregados |

## Qué está en juego

Un usuario con cuenta pero sin permisos de dispositivos puede leer el JSON de sobrantes y el
estado de las sincronizaciones. No es una brecha grave (requiere sesión válida), pero
contradice el modelo RBAC del resto del sistema: 101 rutas sí exigen permiso de módulo.

## Opciones

### Opción A — Cerrar las siete con `module_permission`
- `employees.sobrantes*` y `devices.check-status` → `dispositivos,view`
- `firebird.*` → módulo propio `sincronizacion,view` (o `dispositivos,view` si no se quiere
  crear módulo)
- `operations.notifications` y `kpis/json` → `dashboard,view`
- **Cuesta:** 1 archivo de rutas + seeder de permisos si se crea módulo nuevo + 7 pruebas.
- **Riesgo:** MEDIO — **cierra el acceso a usuarios que hoy entran**. Hay que revisar quién
  usa esas pantallas antes, o el lunes alguien se queda fuera.
- **Reversible:** sí, pero el daño operativo del rato en que alguien no puede trabajar ya ocurrió.

### Opción B — Cerrar solo las tres que exponen datos, documentar el resto
- Cerrar `employees.sobrantes`, `employees.sobrantes.data`, `firebird.*`.
- Dejar `kpis/json`, `notifications` y `check-status` a nivel auth, con un comentario en la
  ruta explicando que es deliberado.
- **Cuesta:** la mitad que A.
- **Riesgo:** BAJO.
- **Reversible:** sí.

### Opción C — Documentar todo y no cambiar nada por ahora
- Dejar constancia en `docs/rbac-matriz-rutas.md` de que estas 7 son auth a propósito.
- **Cuesta:** 20 minutos.
- **Gana:** cero riesgo operativo; deja de ser una duda abierta.
- **Riesgo:** la inconsistencia sigue, y la próxima ruta se escribirá con el mismo criterio ambiguo.

## Recomendación

**B.** Las dos de `sobrantes` devuelven datos de personas y las de `firebird` muestran el
estado de procesos que solo opera un administrador; esas sí se cierran. Las otras tres son de
lectura agregada y cerrarlas solo generaría fricción sin ganar nada.

Sea cual sea la elección, **queda escrito**: una inconsistencia documentada es una decisión;
una silenciosa es deuda que alguien repetirá.

## Qué necesito de ti

¿Quién usa hoy la pantalla de sobrantes y la de Firebird? Si solo administradores, B es
directa; si las usa RH, hay que crear el permiso antes de cerrar.

## Pendiente relacionado

`areas` y `puestos` usan `->middleware('admin')` en vez de `module_permission`. No es un
hueco (están protegidas), pero es el mismo criterio ambiguo. Decidirlo aquí o en su propia
ficha.
