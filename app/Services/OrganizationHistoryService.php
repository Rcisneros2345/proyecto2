<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Area;
use App\Models\Employee;
use App\Models\OrganizationHistory;
use App\Models\User;

class OrganizationHistoryService
{
    public static function recordAreaChange(
        Employee $employee,
        Area $fromArea,
        Area $toArea,
        User $actor,
        string $eventType = 'transfer',
        string $description = ''
    ): ?OrganizationHistory {
        if ($fromArea->id === $toArea->id) {
            return null;
        }

        return OrganizationHistory::create([
            'employee_id' => $employee->id,
            'from_area_id' => $fromArea->id,
            'to_area_id' => $toArea->id,
            'event_type' => $eventType,
            'actor_user_id' => $actor->id,
            'description' => $description,
            'metadata' => [
                'employee_name' => $employee->name,
                'from_area_name' => $fromArea->name,
                'to_area_name' => $toArea->name,
            ],
        ]);
    }
}
