<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Incidencia;
use App\Models\IncidenciaApproval;
use Illuminate\Support\Collection;

class IncidentApprovalService
{
    public function generateRoute(Incidencia $incidencia): Collection
    {
        $area = $incidencia->area;

        if (! $area) {
            return collect();
        }

        $approvers = collect();
        $currentArea = $area;
        $sequence = 1;

        while ($currentArea) {
            $head = $currentArea->head()->first();

            if ($head) {
                $approvers->push([
                    'sequence' => $sequence,
                    'area_id' => $currentArea->id,
                    'approver_employee_id' => $head->id,
                    'status' => 'pending',
                ]);
                $sequence++;
            }

            $currentArea = $currentArea->parent()->first();
        }

        $approvers->each(function (array $step, int $index) use ($incidencia): void {
            IncidenciaApproval::updateOrCreate(
                [
                    'incidencia_id' => $incidencia->id,
                    'sequence' => $step['sequence'],
                ],
                [
                    'area_id' => $step['area_id'],
                    'approver_employee_id' => $step['approver_employee_id'],
                    'status' => $step['status'],
                ]
            );
        });

        return $approvers;
    }

    public function getPendingApprover(Incidencia $incidencia): ?IncidenciaApproval
    {
        return $incidencia->approvals()
            ->where('status', 'pending')
            ->orderBy('sequence')
            ->first();
    }
}
