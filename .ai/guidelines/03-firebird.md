# 03 · Firebird (SOLO LECTURA)

Firebird es la fuente de verdad de empleados y del catálogo académico. MySQL es la copia
operativa. **Ninguna escritura contra Firebird, nunca.** Si una tarea parece requerirlo,
te detienes y escalas.

## SQL de Firebird ≠ SQL de MySQL

| MySQL | Firebird |
|---|---|
| `LIMIT 10` | `FIRST 10` / `ROWS 10` |
| `LIMIT 10 OFFSET 20` | `ROWS 21 TO 30` |
| `IFNULL()` | `COALESCE()` |
| `CONCAT(a,b)` | `a \|\| b` |
| `NOW()` | `CURRENT_TIMESTAMP` |
| `INSERT IGNORE` / `ON DUPLICATE` | no existe |

Dos trampas que ya costaron bugs:
1. `FIRST 1` sin `ORDER BY` devuelve una fila arbitraria.
2. Los `CHAR` vienen con espacios de relleno: sin `TRIM()` en el JOIN o en la clave, los
   registros "desaparecen".

## Reglas de toda sincronización

1. **Idempotente**: dos corridas seguidas no cambian nada la segunda vez (pruébalo).
2. **Claim atómico** (`pending → running` por ID) y lock. Nunca dos procesos sobre el mismo
   trabajo. El endpoint que ejecuta pendientes no puede competir con el worker.
3. **Limpieza de jobs por identificador**, jamás por `LIKE` sobre el payload serializado.
4. **Bitácora** en `FirebirdSync` / `FirebirdSyncItem`, con el motivo real de cada omisión.
5. **`--dry-run`** disponible.
6. **Orden por dependencias**: catálogos base antes que las tablas de ciclo, antes que
   alumnos/inscripciones. En base vacía, el orden equivocado falla por FK y deja datos
   incompletos (`FullSyncStrategy::execute()`).
7. **Blindaje contra vaciado**: 0 filas leídas o una caída mayor al umbral (p. ej. 20%)
   **aborta y avisa**. Eso es error de lectura, no baja masiva.
8. Bajas: marcar inactivo, nunca borrado en cascada.
9. Reanudable desde el último ítem en `ok`.

## Conciliación

Clave estable = número de empleado / clave natural del catálogo, **normalizada** (TRIM,
caso, separadores) igual que la collation de MySQL. Estados: `NUEVO`, `MODIFICADO`, `BAJA`,
`HUERFANO_CHECADOR` (existe en el equipo, no en el catálogo → pestaña de sobrantes, decisión
manual), `SIN_CAMBIO`.
