<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioLaboral extends Model
{
    use HasFactory;

    protected $table = 'horarios_laborales';

    protected $fillable = [
        'employee_id',
        'dia_semana',
        'hora_entrada',
        'hora_salida',
        'hora_salida_comer',
        'hora_regreso_comer',
        'activo',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
        'hora_salida_comer' => 'datetime',
        'hora_regreso_comer' => 'datetime',
        'activo' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Días de la semana en español
     */
    public static function dias(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    /**
     * Nombre del día de la semana
     */
    public function diaNombre(): string
    {
        return self::dias()[$this->dia_semana] ?? 'Desconocido';
    }

    /**
     * Busca el horario laboral activo para un empleado en un día específico
     */
    public static function buscarPorEmpleadoYDia(int $employeeId, int $diaSemana): ?self
    {
        return static::where('employee_id', $employeeId)
            ->where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->first();
    }

    /**
     * Obtiene todos los horarios activos para un empleado
     */
    public static function horariosPorEmpleado(int $employeeId)
    {
        return static::where('employee_id', $employeeId)
            ->where('activo', true)
            ->orderBy('dia_semana')
            ->get();
    }
}
