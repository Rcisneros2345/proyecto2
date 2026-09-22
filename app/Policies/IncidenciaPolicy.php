<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Academia\Profesor;
use App\Models\Employee;
use App\Models\Incidencia;
use App\Models\User;

class IncidenciaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasModulePermission('incidencias', 'view');
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasModulePermission('incidencias', 'create');
    }

    public function createForEmployee(User $user, Employee $employee): bool
    {
        return $user->isAdmin()
            || $user->employeeAssignments()->whereKey($employee->getKey())->exists();
    }

    public function createForProfessor(User $user, Profesor $profesor): bool
    {
        return $user->isAdmin()
            || $user->professorAssignments()->whereKey($profesor->getKey())->exists();
    }

    public function approve(User $user, Incidencia $incidencia): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->hasModulePermission('incidencias', 'approve')) {
            return false;
        }

        $employeeIds = $user->employeeAssignments()->pluck('employees.id');

        $pendingApproval = $incidencia->approvals()
            ->where('status', 'pending')
            ->orderBy('sequence')
            ->first();

        if ($pendingApproval && $pendingApproval->approver_employee_id !== null) {
            return $employeeIds->contains($pendingApproval->approver_employee_id);
        }

        return $employeeIds->contains($incidencia->responsable_area_id)
            || $employeeIds->contains($incidencia->director_id)
            || $employeeIds->contains($incidencia->profesor?->director_id);
    }

    public function markViewed(User $user, Incidencia $incidencia): bool
    {
        return $user->isAdmin() || $incidencia->created_by_user_id === $user->id;
    }

    public function sign(User $user, Incidencia $incidencia): bool
    {
        return ($user->isAdmin() || $incidencia->created_by_user_id === $user->id)
            && $incidencia->estado === 'aprobada';
    }
}
