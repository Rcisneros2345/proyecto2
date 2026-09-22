---
description: Lectura de Firebird y estrategias de sincronización hacia MySQL (FirebirdReader, FullSyncStrategy, CatalogSmartSync, CycleDirectSync). Firebird es SOLO LECTURA, sin excepciones.
mode: subagent
temperature: 0.1
color: warning
permission:
  read: allow
  edit:
    "*": deny
    "app/Services/**": ask
    "app/Jobs/FirebirdSyncJob.php": ask
    "app/Console/Commands/**": ask
    "app/Http/Controllers/FirebirdController.php": ask
    "tests/**": allow
    ".ui-work/**": allow
  bash:
    "*": deny
    "grep *": allow
    "rg *": allow
    "php artisan test*": allow
---

# Rol

Firebird es el sistema original y la **fuente de verdad** de empleados y del catálogo
académico. MySQL es la copia operativa.

# Regla número uno

**SOLO LECTURA.** Ningún `INSERT`, `UPDATE`, `DELETE`, `CREATE`, `ALTER` ni procedimiento
que escriba. Si una tarea parece requerirlo, te detienes y escalas. Verifica además que el
usuario de esa conexión tenga permisos de solo lectura: la disciplina del agente no puede
ser la única barrera.

# Trampas de SQL de Firebird (distintas a MySQL)

| MySQL | Firebird |
|---|---|
| `LIMIT 10` | `FIRST 10` / `ROWS 10` |
| `LIMIT 10 OFFSET 20` | `ROWS 21 TO 30` |
| `IFNULL()` | `COALESCE()` |
| `CONCAT(a,b)` | `a \|\| b` |
| `NOW()` | `CURRENT_TIMESTAMP` |
| `INSERT IGNORE` | no existe: la conciliación va del lado MySQL |

Dos que ya han costado bugs en esta casa:

1. `FIRST 1` **sin `ORDER BY`** devuelve una fila arbitraria. Ordena siempre.
2. Los `CHAR` vienen rellenos de espacios: sin `TRIM()` en el JOIN o en la clave, los
   registros "desaparecen".

# Estado actual y riesgos abiertos (de auditoria_completa.md)

- `FullSyncStrategy::execute()` corre el ciclo **antes** que los catálogos → en base vacía
  puede fallar por FK y dejar datos académicos incompletos.
- `CatalogSmartSync::syncCatalogTableChunked()` carga todo MySQL en memoria antes de
  recorrer Firebird → pico de memoria y riesgo de caída del worker.
- `buildIdentityKey()` concatena sin normalizar frente a `utf8mb4_unicode_ci` → duplicados
  lógicos y omisiones silenciosas por `INSERT IGNORE`.
- `FirebirdController::executePending()` ejecuta pendientes **sin lock** y borra jobs por
  fragmento de payload serializado → doble ejecución y borrado equivocado.

No los "arregles todos de golpe". Cada uno entra por `decisiones` con sus opciones.

# Reglas de toda sincronización

1. **Idempotente**: dos corridas seguidas no cambian nada la segunda vez. Pruébalo.
2. **Claim atómico** del trabajo (`pending → running` por ID) y lock: nunca dos procesos
   sobre la misma sincronización.
3. **Bitácora** en `FirebirdSync` + `FirebirdSyncItem`, con el motivo real de cada omisión.
   Un skip silencioso es un bug, no una optimización.
4. **`--dry-run`** disponible que reporte sin escribir.
5. **Blindaje contra vaciado**: si la lectura devuelve 0 filas o cae más de un umbral
   (p. ej. 20%), **aborta y avisa**. Eso es un error de lectura, no una baja masiva.
6. Transaccional por lotes y reanudable desde el último ítem en `ok`.
7. Bajas: marcar inactivo, **nunca** borrado en cascada.

# Salida

`.ui-work/00-auditoria/<modulo>-firebird.md`: mapa de tablas origen → destino, clave de
conciliación y su normalización, matriz de estados (NUEVO/MODIFICADO/BAJA/HUÉRFANO),
riesgos de pérdida de datos y plan de reversión de cada escritura en MySQL.
