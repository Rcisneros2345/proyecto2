---
decision: D-005
modulo: EMPLEADOS
fecha: 2026-09-20
estado: PROPUESTA
---

# D-005 · HorarioLaboral no declara `$table` y Laravel infiere `horario_laborals`

## El problema en una frase

`HorarioLaboral` (`app/Models/HorarioLaboral.php:11`) no define `$table`; Laravel infiere
`horario_laborals` (verificado: `(new HorarioLaboral())->getTable()` → `horario_laborals`),
pero la tabla real es `horarios_laborales` (`database/migrations/2026_09_16_000001_create_horarios_laborales_table.php:13`,
confirmada en el esquema MySQL). Todo query del modelo lanza "Table not found".

## Qué está en juego

No es solo `AttendanceController::index()`. Son **tres consumidores** los que consultan el
modelo y fallan:

| Consumidor | Línea | Qué hace |
|---|---|---|
| `AttendanceController::index()` | `app/Http/Controllers/AttendanceController.php:52-55` | precarga horarios por empleado |
| `Attendance::obtenerHorarioLaboral()` | `app/Models/Attendance.php:111` | usado por `observacionLlegada()` (:135), `observacionSalida()` (:163), `horario_entrada` (:187), `horario_salida` (:197) |
| `PersonaContratosResolver::extraerJornada()` | `app/Services/PersonaContratosResolver.php:75` | jornada fija del empleado en contratos |

`Attendance.php:118` crea `new HorarioLaboral([...])` en memoria — no toca la tabla, no falla.

Además: no existe factory ni test que cubra `HorarioLaboral` (usa `HasFactory` pero no hay
`HorarioLaboralFactory`; `tests/` no lo referencia). La regla 12 del proyecto exige prueba
nueva, así que el costo real de cualquier opción incluye crearla.

## Opciones

### Opción A — Declarar `protected $table = 'horarios_laborales';`
- **Qué implica:** 1 línea en el modelo.
- **Cuesta:** 1 línea + 1 prueba nueva (~30 líneas; hoy no hay factory ni test del modelo).
- **Gana:** arregla los 3 consumidores de una sola vez; no toca esquema ni contrato público.
- **Riesgo:** ninguno funcional. El nombre del modelo difiere de la tabla (convención rota,
  cosmético; el resto del dominio ya usa plurales en español: `areas`, `puestos`, `incidencias`).
- **Reversible:** sí, trivial (`git revert`).

### Opción B — Renombrar la tabla a `horario_laborals`
- **Qué implica:** migración `RENAME TABLE` + renombrar el FK
  `horarios_laborales_employee_id_foreign` + actualizar queries literales y la documentación
  (la auditoría ya documenta `horarios_laborales` en 3 archivos) + regenerar baseline.
- **Cuesta:** 1 migración + actualización de consumidores literales + baseline; ~1-2 horas.
- **Gana:** la inferencia de Laravel coincide con la tabla.
- **Riesgo:** toca esquema con datos en uso (viola el espíritu de `07-no-romper`: la tabla es
  contrato público). `horario_laborals` es un plural incorrecto en español. El constraint FK
  queda con nombre viejo. Cualquier consumidor externo (reportes, integraciones) que use el
  nombre literal se rompe sin que `impacto.php` lo vea.
- **Reversible:** sí, pero con otra migración sobre datos en uso.

### Opción C — `DB::table('horarios_laborales')` en AttendanceController
- **Qué implica:** refactor del controller para saltarse Eloquent.
- **Cuesta:** ~20 líneas + prueba.
- **Gana:** no toca modelo ni esquema.
- **Riesgo:** arregla 1 de 3 consumidores; `Attendance.php:111` y
  `PersonaContratosResolver.php:75` siguen rotos. Pierde Eloquent y duplica lógica (regla 8).
- **Reversible:** sí.

### Opción D — No hacer nada
- **Qué implica:** dejar el fallo activo.
- **Por qué se descarta:** el error es visible en al menos 3 flujos (asistencias,
  observaciones, contratos). No es una espera legítima; es una regresión en producción.

## Recomendación

**A.** Es el único fix que resuelve los 3 consumidores con 1 línea y sin tocar esquema ni
contrato público. Renombrar la tabla (B) para satisfacer la inferencia de Laravel introduce
riesgo de esquema para un beneficio nulo, y C deja 2 de 3 flujos rotos. La convención de
nombre es cosmética; la coherencia real del dominio ya está en `horarios_laborales`.

## Qué necesito de ti

Una sola respuesta: A, B o C. Si eliges A, la implementación es: agregar `$table` al modelo,
crear prueba que consulte el modelo contra la tabla real (creando el registro con
`HorarioLaboral::create()` o una factory nueva), y correr `pint`, `impacto.php`, el test y
`verificar.php`.

## Si nos arrepentimos

A y C: `git revert`. B: requiere plan de retirada propio (migración inversa) escrito antes
de empezar.