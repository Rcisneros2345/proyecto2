<?php

namespace Tests\Feature;

use App\Models\Academia\AttendanceCaptureAssignment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCaptureAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_capture_only_assigned_level_and_campus(): void
    {
        $user = User::factory()->create(['role' => 'operator']);
        $employee = Employee::create([
            'auth_user_id' => $user->id,
            'user_id' => 'CAP-001',
            'name' => 'Capturista',
            'type' => 'admin',
            'status_actual' => 'A',
        ]);
        $user->refresh();

        AttendanceCaptureAssignment::create([
            'user_id' => $user->id,
            'nivel' => 'LOG',
            'id_campus' => 'CENTRO',
            'inicial' => 2025,
            'final' => 2025,
            'periodo' => 3,
            'active' => true,
        ]);

        $this->assertTrue($user->canCaptureAttendanceLevelAndCampus('LOG', 'CENTRO', 2025, 2025, 3));
        $this->assertFalse($user->canCaptureAttendanceLevelAndCampus('LOG', 'NORTE', 2025, 2025, 3));
        $this->assertFalse($user->canCaptureAttendanceLevelAndCampus('ADM', 'CENTRO', 2025, 2025, 3));
        $this->assertFalse(app(\App\Services\AttendanceCaptureAuthorization::class)->canCaptureFilter($user, 'LOG', 'NORTE', 2025, 2025, 3));
        $this->assertTrue(app(\App\Services\AttendanceCaptureAuthorization::class)->canCaptureFilter($user, 'LOG', 'CENTRO', 2025, 2025, 3));
    }

    public function test_admin_can_capture_without_assignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->canCaptureAttendanceLevelAndCampus('LOG', 'CENTRO', 2025, 2025, 3));
    }

    public function test_admin_can_save_user_capture_assignments_from_user_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'operator']);

        $this->actingAs($admin)
            ->put(route('preferencia.usuarios.captura-asistencia.update', $user), [
                'assignments' => [[
                    'nivel' => 'LOG',
                    'id_campus' => 'CENTRO',
                    'inicial' => 2025,
                    'final' => 2025,
                    'periodo' => 3,
                    'active' => 1,
                ], [
                    'nivel' => 'ADM',
                    'id_campus' => 'NORTE',
                    'inicial' => 2025,
                    'final' => 2025,
                    'periodo' => 3,
                    'active' => 0,
                ]],
            ])
            ->assertRedirect(route('preferencia.usuarios.captura-asistencia.edit', $user));

        $this->assertDatabaseHas('attendance_capture_assignments', [
            'user_id' => $user->id,
            'nivel' => 'LOG',
            'id_campus' => 'CENTRO',
            'inicial' => 2025,
            'final' => 2025,
            'periodo' => 3,
            'active' => 1,
        ]);
        $this->assertDatabaseHas('attendance_capture_assignments', [
            'user_id' => $user->id,
            'nivel' => 'ADM',
            'id_campus' => 'NORTE',
            'active' => 0,
        ]);
    }
}
