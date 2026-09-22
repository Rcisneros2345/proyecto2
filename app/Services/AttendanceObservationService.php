<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceObservation;
use App\Models\Employee;
use App\Models\User;

class AttendanceObservationService
{
    public static function record(
        Attendance $attendance,
        Employee $employee,
        User $user,
        string $kind,
        string $message,
        string $source = 'manual'
    ): AttendanceObservation {
        return AttendanceObservation::create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'user_id' => $user->id,
            'kind' => $kind,
            'message' => $message,
            'source' => $source,
            'metadata' => [
                'recorded_at' => $attendance->recorded_at?->toIso8601String(),
            ],
        ]);
    }
}
