# 02 · MySQL

## Índices y unicidad que el dominio exige

```sql
-- una fila por empleado y dispositivo
ALTER TABLE device_employee ADD UNIQUE KEY uq_device_employee (device_id, employee_id);
-- una huella por dedo, empleado y dispositivo
ALTER TABLE fingerprints ADD UNIQUE KEY uq_fingerprint (employee_id, device_id, finger_index);
```

**Nunca** apliques un único sin demostrar antes que no hay duplicados:

```sql
SELECT device_id, employee_id, COUNT(*) c FROM device_employee GROUP BY 1,2 HAVING c>1;
```

Si los hay, qué se conserva es **decisión humana**, no tuya.

## Collation vs. comparación en PHP (hallazgo abierto)

Las tablas usan `utf8mb4_unicode_ci`; `CatalogSmartSync::buildIdentityKey()` concatena sin
normalizar. PHP distingue lo que MySQL considera igual → duplicados lógicos, y el
`INSERT IGNORE` oculta la fila omitida. Regla: **la clave de comparación se normaliza igual
que la collation**, y todo skip por conflicto se registra como error auditable con la clave
completa, nunca en silencio.

## Consultas

- Sin `SELECT *`. Columnas explícitas.
- Sin consultas dentro de `foreach`: `with()`, `loadMissing()` o join.
- Paginación en todo listado; límite explícito en widgets; `chunk`/cursor en procesos.
  (La auditoría ya marcó `get()`/`all()` en listados grandes: no agregues más.)
- Subconsulta correlacionada → `JOIN` con agregado `GROUP BY`.
- Operación multi-tabla → `DB::transaction()`.
- Preferir `Model::query()` a `DB::` (regla de Boost en AGENTS.md).

## Migraciones

- Una migración = un cambio, con `down()` funcional. Verifica con `migrate --pretend` y
  pega el SQL en tu reporte. **Tú no ejecutas migraciones**: las ejecuta el humano.
- Nunca edites una ya ejecutada.
- Retiro de columna en dos pasos (dejar de usar → desplegar → borrar después).
- `deploy.sh` tiene un orden deliberado (workers antes de migrar, app en mantenimiento si
  algo falla). No propongas nada que lo rompa sin decirlo.

## Memoria en sincronizaciones

`syncCatalogTableChunked()` carga todo MySQL en memoria antes de recorrer Firebird: el
"chunk" limita el origen, no el destino. Alternativas a evaluar en `decisiones`: ventanas
por índice, staging + upsert por clave natural, o comparación por hash.
