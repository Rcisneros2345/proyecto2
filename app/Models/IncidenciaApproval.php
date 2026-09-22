<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenciaApproval extends Model
{
    use HasFactory;

    protected $table = 'incidencia_approvals';

    protected $fillable = [
        'incidencia_id',
        'sequence',
        'area_id',
        'approver_user_id',
        'approver_employee_id',
        'approver_professor_clave',
        'status',
        'approved_at',
        'rejected_at',
        'comment',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function incidencia(): BelongsTo
    {
        return $this->belongsTo(Incidencia::class, 'incidencia_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function approverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function approverEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function approverProfessor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academia\Profesor::class, 'approver_professor_clave', 'clave_profesor');
    }
}
