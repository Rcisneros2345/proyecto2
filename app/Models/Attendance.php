<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'employee_id',
        'user_id',
        'state',
        'type',
        'recorded_at',
    ];

    protected $casts = [
        'state' => 'integer',
        'type' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(AttendanceObservation::class, 'attendance_id');
    }

    public function attendanceTypeLabel(): string
    {
        return match ($this->attendance_type ?? 'biometric') {
            'manual_admin' => 'Manual administrativo',
            'manual_teacher' => 'Manual docente',
            'class' => 'Clase',
            default => 'Biométrica',
        };
    }

    public static function uniqueAttendances()
    {
        return self::query()
            ->whereIn('id', self::query()
                ->selectRaw('MAX(id)')
                ->groupBy('employee_id', 'recorded_at'))
            ->latest('recorded_at');
    }

    /**
     * Modo de checado efectivo del registro.
     */
    public function punchStatus(): int
    {
        if (isset(self::states()[$this->type])) {
            return $this->type;
        }

        return isset(self::states()[$this->state]) ? $this->state : $this->type;
    }

    public function stateLabel(): string
    {
        return self::states()[$this->punchStatus()] ?? 'Desconocido';
    }

    /**
     * Etiqueta corta para badges de UI.
     */
    public function shortStateLabel(): string
    {
        return match ($this->punchStatus()) {
            0 => 'Entrada',
            1 => 'Salida',
            4 => 'Entrada T.E.',
            5 => 'Salida T.E.',
            default => $this->stateLabel(),
        };
    }

    /**
     * Clase de color del sistema de tokens.
     */
    public function stateColorClass(): string
    {
        return match ($this->punchStatus()) {
            0 => 'cat-green',
            1 => 'cat-blue',
            4 => 'cat-purple',
            5 => 'cat-lavender',
            default => 'cat-gray',
        };
    }

    /**
     * Obtiene el horario laboral aplicable para esta asistencia.
     * Busca en horarios_laborales por employee_id y día de la semana.
     * Si no existe, retorna horario por defecto 08:00-17:00.
     */
    public function obtenerHorarioLaboral(): HorarioLaboral
    {
        $diaSemana = $this->recorded_at->dayOfWeekIso; // 1=Lun, 7=Dom
        $horario = HorarioLaboral::buscarPorEmpleadoYDia($this->employee_id, $diaSemana);

        if ($horario) {
            return $horario;
        }

        // Horario por defecto si no hay registro en horarios_laborales
        return new HorarioLaboral([
            'employee_id' => $this->employee_id,
            'dia_semana' => $diaSemana,
            'hora_entrada' => Carbon::createFromTime(8, 0),
            'hora_salida' => Carbon::createFromTime(17, 0),
        ]);
    }

    /**
     * Calcula observación de llegada: temprano, tarde o a tiempo.
     */
    public function observacionLlegada(): string
    {
        if (! $this->hora_entrada) {
            return '—';
        }

        $horario = $this->obtenerHorarioLaboral();
        $horaBase = Carbon::parse($horario->hora_entrada);
        $entrada = Carbon::createFromTime(
            $this->hora_entrada->hour,
            $this->hora_entrada->minute,
            $this->hora_entrada->second
        );

        $diferenciaMinutos = $entrada->diffInMinutes($horaBase);

        if ($entrada->greaterThan($horaBase)) {
            return "llegó tarde en {$diferenciaMinutos} min";
        } elseif ($entrada->lessThan($horaBase)) {
            return "llegó temprano en {$diferenciaMinutos} min";
        }

        return '—';
    }

    /**
     * Calcula observación de salida: temprano, tarde o a tiempo.
     */
    public function observacionSalida(): string
    {
        if (! $this->hora_salida) {
            return '—';
        }

        $horario = $this->obtenerHorarioLaboral();
        $horaBase = Carbon::parse($horario->hora_salida);
        $salida = Carbon::createFromTime(
            $this->hora_salida->hour,
            $this->hora_salida->minute,
            $this->hora_salida->second
        );

        $diferenciaMinutos = $salida->diffInMinutes($horaBase);

        if ($salida->lessThan($horaBase)) {
            return "saliendo temprano en {$diferenciaMinutos} min";
        } elseif ($salida->greaterThan($horaBase)) {
            return "saliendo tarde en {$diferenciaMinutos} min";
        }

        return '—';
    }

    /**
     * Retorna el horario base de entrada para esta asistencia.
     */
    public function getHorarioEntradaAttribute(): ?string
    {
        $horario = $this->obtenerHorarioLaboral();

        return $horario->hora_entrada ? Carbon::parse($horario->hora_entrada)->format('H:i') : null;
    }

    /**
     * Retorna el horario base de salida para esta asistencia.
     */
    public function getHorarioSalidaAttribute(): ?string
    {
        $horario = $this->obtenerHorarioLaboral();

        return $horario->hora_salida ? Carbon::parse($horario->hora_salida)->format('H:i') : null;
    }

    /**
     * Nota: en firmwares como el de este proyecto, el byte "type" del log es
     * el modo de checado (ver punchStatus()), NO el método de verificación.
     */
    public function verificationTypeLabel(): string
    {
        return self::verificationTypes()[$this->type] ?? 'Método '.$this->type;
    }

    public static function states(): array
    {
        return [
            0 => 'Entrada',
            1 => 'Salida',
            2 => 'Salida de descanso',
            3 => 'Regreso de descanso',
            4 => 'Entrada de tiempo extra',
            5 => 'Salida de tiempo extra',
            255 => 'Desconocido',
        ];
    }

    public static function verificationTypes(): array
    {
        return [
            0 => 'Huella',
            1 => 'Contraseña',
            2 => 'Código',
            3 => 'Tarjeta',
            4 => 'Huella',
            5 => 'Rostro',
        ];
    }
}
