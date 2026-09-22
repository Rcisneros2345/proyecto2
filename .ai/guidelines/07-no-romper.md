# 07 · No romper nada (contrato público)

> La guía más importante del proyecto. Se lee **antes** de tocar rutas, vistas, componentes,
> firmas o columnas.

## Qué es contrato público aquí

| Tipo | Ejemplos de este repo |
|---|---|
| Nombre de ruta | `devices.sync`, `academia.ciclos.activo`, `employees.index` |
| URI, método y parámetros | `devices/{device}/sync`, `{ciclo}`, POST vs PUT |
| Nombre de vista | `devices.show`, `employees.sobrantes`, `academia.horarios.aula` |
| Componente Blade | `<x-data-table>`, `<x-badge>`, `<x-filter-bar>`, `<x-stat-card>`, `<x-drawer>`, `<x-page-header>`, `<x-academia.ciclo-selector>` |
| Partial | `employees/partials/_sync-progress-panel`, `partials/empty-state` |
| **Clave de permiso** | `module_permission:academia.ciclos,activo` |
| Columna y tabla | `device_employee.device_uid`, `fingerprints.finger_index` |
| Evento consumido por la UI | `SyncProgressUpdated` y su payload |
| Firma pública de servicio | `CicloActualService`, `HorarioResolver`, `PermissionResolver` |

**El contrato no se rompe en un refactor. Nunca.**

## Ritual

```bash
php scripts/baseline.php               # antes de empezar el módulo
php scripts/comparar_rutas.php         # después de cada cambio
php scripts/verificar_referencias.php
php scripts/impacto.php
```

> No generes el baseline con `php artisan route:list > archivo` desde PowerShell: escribe
> UTF-16 con BOM y el JSON queda ilegible. Si artisan falla, el archivo guarda el error y
> la comparación deja de comparar sin que nadie se entere. Ya pasó en este repo.

## Tabla de veredictos

| Situación | Veredicto |
|---|---|
| Desapareció un nombre de ruta | **BLOQUEO** |
| Cambió URI, método o nombre de parámetro | **BLOQUEO** |
| Se retiró un `module_permission` de una ruta (queda más abierta) | **BLOQUEO** |
| Vista, componente o partial renombrado sin actualizar consumidores | **BLOQUEO** |
| `route()`, `view()`, `@include`, `<x-…>` o `asset()` a algo inexistente | **BLOQUEO** |
| Columna renombrada con vistas o queries apuntando a la vieja | **BLOQUEO** |
| `php artisan route:list` falla | **BLOQUEO** (la app no resuelve sus rutas) |
| Ruta nueva con `->name()` | OK |
| Mismo nombre de ruta, controller distinto | OK |
| Se añadió un permiso a una ruta | REVISAR: ¿quién entraba antes y ahora no? |

## Cambio autorizado de ruta: dos pasos

1. Agregar la nueva; **conservar la vieja** (mismo controller o `Route::redirect()`).
2. Actualizar todas las referencias internas:
   `rg -n "route\('devices\.sync'" resources/ app/ tests/`
3. Registrar en `docs/RUTAS-DEPRECADAS.md` (vieja → nueva, fecha, retiro propuesto).
4. Regenerar baseline: `php scripts/baseline.php`.
5. El retiro de la vieja es **otra tarea**, con autorización humana.

## Cambio de nombre de columna sin romper

1. Migración que **agrega** la nueva.
2. Escritura doble durante una fase.
3. Lectura migrada: modelos, queries, vistas, exportaciones, seeders, factories.
4. Desplegar y verificar (ojo con el orden deliberado de `deploy.sh`).
5. Migración de retiro en tarea posterior aprobada.

Nunca `renameColumn` directo sobre datos en uso.

## Antes de borrar cualquier cosa: las cuatro búsquedas

```bash
rg -n "NombreDelSimbolo" app/ routes/ resources/ tests/ database/
php artisan route:list | grep -i nombre
rg -n "nombre\.de\.vista|<x-nombre" resources/ app/
git log -S "NombreDelSimbolo" --oneline | head
```

Y la quinta, la que rompe cosas: **referencias dinámicas**. `view($x)`, `@include($vista)`,
`"x-" . $tipo`, clases CSS armadas en JS por concatenación. Si hay construcción dinámica,
no lo tocas: `[REVISIÓN MANUAL]`.
