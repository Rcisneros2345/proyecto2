<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Academia\Profesor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'asunto',
        'tipo_justificacion',
        'fecha_justificacion',
        'fecha_falta_programada',
        'tipo_duracion',
        'hora_inicio',
        'hora_fin',
        'fecha_creacion',
        'empleado_id',
        'profesor_clave',
        'area_id',
        'puesto_id',
        'director_id',
        'numero_empleado',
        'motivo',
        'comentarios',
        'solicitud',
        'estado',
        'responsable_area_id',
        'created_by_user_id',
        'autorizado_at',
        'autorizado_por_user_id',
        'visto_at',
        'visto_por_user_id',
        'firmado_at',
        'firmado_por_user_id',
    ];

    protected $casts = [
        'fecha_justificacion' => 'date',
        'fecha_falta_programada' => 'date',
        'fecha_creacion' => 'datetime',
        'autorizado_at' => 'datetime',
        'visto_at' => 'datetime',
        'firmado_at' => 'datetime',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'empleado_id');
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'profesor_clave', 'clave_profesor');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'director_id');
    }

    public function responsableArea(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsable_area_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por_user_id');
    }

    public function vistoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visto_por_user_id');
    }

    public function firmadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'firmado_por_user_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(IncidenciaApproval::class, 'incidencia_id');
    }
}
