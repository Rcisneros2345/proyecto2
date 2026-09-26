# Checklist de Diseño/Cambios de Base de Datos (Database Architect)

Cualquier migración sobre una base con datos reales es riesgo **Alto** (destructiva sobre tabla con datos en producción, riesgo **Crítico**) según la tabla de "Niveles de riesgo y aprobación" del `SKILL.md` — requiere tu aprobación explícita antes de aplicarse, y dispara Tier 3 de verificación.

## Antes de crear o modificar una tabla

- ¿La tabla tiene una clave primaria clara? ¿Hace falta que sea autoincremental, UUID, o compuesta?
- ¿Los tipos de dato son los correctos para el rango real de valores (evitar `VARCHAR(255)` por default sin pensarlo, o `INT` para algo que puede superar el rango)?
- ¿Los campos obligatorios tienen `NOT NULL`, y los opcionales están claramente marcados como tal?
- ¿Se normalizó lo razonable (evitar duplicar el mismo dato en varias tablas) sin sobre-normalizar al punto de necesitar 6 JOINs para una consulta común?
- ¿Las relaciones (FK) están declaradas explícitamente, con la regla de borrado correcta (`CASCADE`, `RESTRICT`, `SET NULL`) pensada, no puesta por default sin revisar?

## Índices

- ¿Las columnas usadas en `WHERE`, `JOIN` y `ORDER BY` frecuentes tienen índice?
- ¿Hay índice único donde el dominio lo exige (ej. email de usuario, código de producto)?
- ¿Se evitó indexar todo "por si acaso"? (los índices de más ralentizan escrituras y ocupan espacio)

## Migraciones

- ¿La migración es reversible (tiene `down`/rollback) salvo que sea explícitamente irreversible por diseño?
- ¿Se probó la migración en una copia de datos real o representativa, no solo en una base vacía?
- ¿Si la tabla ya tiene datos en producción, el cambio de esquema contempla backfill/default para las filas existentes?
- ¿Se evita una migración destructiva (`DROP COLUMN`/`DROP TABLE`) sin backup y sin autorización explícita del usuario?

## Consultas y rendimiento

- Antes de optimizar, ¿se identificó la query real que es lenta (EXPLAIN/análisis), en vez de adivinar?
- ¿El problema es falta de índice, N+1 queries (ORM trayendo relaciones en loop), o volumen de datos sin paginar?
- ¿Se evitó traer columnas/tablas completas cuando solo se necesitan unos pocos campos?

## Backups y recuperación

Antes de cualquier migración de riesgo Alto/Crítico, esto no es opcional:

- ¿Existe un backup reciente de la base antes de aplicar el cambio (`mysqldump`/snapshot para MySQL, `gbak` para Firebird)?
- ¿El backup se probó alguna vez restaurándolo de verdad, no solo generándolo? Un backup nunca restaurado es una suposición, no una garantía.
- ¿Cuál es el RPO/RTO aceptable para este proyecto (cuánta pérdida de datos y cuánto tiempo de caída son tolerables)? Si no se sabe, preguntarlo antes de decidir la frecuencia de backup, no asumirlo.
- Si la sincronización Firebird↔MySQL falla a mitad de camino, ¿hay forma de restaurar el último estado consistente conocido en ambos sistemas?

## Sincronización entre sistemas (si el proyecto tiene más de una base de datos)

- ¿Está claro cuál es la fuente de verdad para cada dato?
- ¿Qué pasa si la sincronización falla a mitad de camino? ¿Es idempotente (correrla de nuevo no duplica datos)?
- ¿Hay forma de detectar/loguear registros que quedaron desincronizados?
- Documentar esto en `memory/DB_SCHEMA_NOTES.md` la primera vez que se toca — es exactamente el tipo de conocimiento caro de re-descubrir.

## Al cerrar la tarea

Registrar en `memory/DB_SCHEMA_NOTES.md`:
- tablas/entidades tocadas y su propósito si no es obvio,
- relaciones no evidentes (soft delete, multi-tenant, particularidades del dominio),
- decisiones de índices o normalización que alguien podría cuestionar sin este contexto.
