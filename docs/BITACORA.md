# Bitácora de cambios

Una entrada por cambio significativo. La escribe `documentador`.

```markdown
## 2026-09-20 — Incidente: pantalla /attendances caída (500)

**Alcance:** GET /attendances retornaba error 500 en toda visita autenticada.
La pantalla de asistencias estaba completamente inaccesible.

**Causa raíz:** El modelo `HorarioLaboral` no declaraba `$table`. Laravel
infería `horario_laborals` (convención inglesa), pero la tabla real en MySQL
es `horarios_laborales` (ambas palabras en plural español). Cualquier query
a través del modelo fallaba con "Base table or view not found".

**Consumidores afectados:**
- `AttendanceController::index()` — alimenta la vista principal
- `Attendance::buscarPorEmpleadoYDia()` — alimenta observaciones de llegada/salida
- `PersonaContratosResolver.php` — jornada fija en contratos

**Fix:** `protected $table = 'horarios_laborales';` en `app/Models/HorarioLaboral.php`

**Tests:** 6 arreglados (AttendanceFilterTest×5, DashboardRenderTest×1). De 8 fallos a 2.

**Posible alcance en producción:** Preguntar al humano si hay que revisar logs
de producción para confirmar cuánto tiempo llevaba caída esta pantalla y si
afectó a usuarios reales. La ruta es principal del módulo de asistencias.

**DT:** DT-46 cerrada. Referencia: D-005.
```

---