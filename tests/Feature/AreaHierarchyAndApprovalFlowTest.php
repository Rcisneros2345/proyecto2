<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Area;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Incidencia;
use App\Models\User;
use App\Services\AttendanceObservationService;
use App\Services\IncidentApprovalService;
use App\Services\OrganizationHierarchyService;
use App\Services\OrganizationHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaHierarchyAndApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_area_hierarchy_resolves_from_database_relations(): void
    {
        $rector = Employee::create([
            'user_id' => 'RECTOR-1',
            'name' => 'Rector General',
            'type' => 'admin',
        ]);

        $directorFinanzas = Employee::create([
            'user_id' => 'FIN-1',
            'name' => 'Jefa de Finanzas',
            'type' => 'admin',
        ]);

        $jefeSistemas = Employee::create([
            'user_id' => 'SIS-1',
            'name' => 'Jefe de Sistemas',
            'type' => 'admin',
        ]);

        $rectorArea = Area::create([
            'identificador' => 'ORG-01',
            'code' => 'RECTOR',
            'name' => 'Rectoría',
            'descripcion' => 'Máxima autoridad institucional',
            'head_employee_id' => $rector->id,
            'level' => 1,
            'status' => 'active',
        ]);

        $finanzasArea = Area::create([
            'identificador' => 'AR-01',
            'code' => 'FIN',
            'name' => 'Administración de Finanzas',
            'descripcion' => 'Área administrativa',
            'parent_id' => $rectorArea->id,
            'head_employee_id' => $directorFinanzas->id,
            'level' => 2,
            'status' => 'active',
        ]);

        $sistemasArea = Area::create([
            'identificador' => 'AR-02',
            'code' => 'SIS',
            'name' => 'Sistemas',
            'descripcion' => 'Área tecnológica',
            'parent_id' => $finanzasArea->id,
            'head_employee_id' => $jefeSistemas->id,
            'level' => 3,
            'status' => 'active',
        ]);

        $service = new OrganizationHierarchyService;

        $this->assertSame($finanzasArea->id, $sistemasArea->parent_id);
        $this->assertSame($directorFinanzas->id, $finanzasArea->head_employee_id);
        $this->assertSame($jefeSistemas->id, $sistemasArea->head_employee_id);

        $this->assertSame([$rectorArea->id, $finanzasArea->id], $service->getAncestors($sistemasArea)->pluck('id')->all());
        $this->assertSame([$rectorArea->id, $finanzasArea->id, $sistemasArea->id], $service->getHierarchyPath($sistemasArea));
    }

    public function test_incidence_approval_route_is_generated_and_persisted(): void
    {
        $rector = Employee::create([
            'user_id' => 'RECTOR-APP',
            'name' => 'Rector',
            'type' => 'admin',
        ]);

        $jefaFinanzas = Employee::create([
            'user_id' => 'FIN-APP',
            'name' => 'Jefa Finanzas',
            'type' => 'admin',
        ]);

        $jefeSistemas = Employee::create([
            'user_id' => 'SIS-APP',
            'name' => 'Jefe Sistemas',
            'type' => 'admin',
        ]);

        $rectorArea = Area::create([
            'identificador' => 'ORG-APP',
            'code' => 'RECTOR',
            'name' => 'Rectoría',
            'descripcion' => 'Máxima autoridad',
            'head_employee_id' => $rector->id,
            'level' => 1,
            'status' => 'active',
        ]);

        $finanzasArea = Area::create([
            'identificador' => 'AR-APP-01',
            'code' => 'FIN',
            'name' => 'Administración de Finanzas',
            'descripcion' => 'Finanzas',
            'parent_id' => $rectorArea->id,
            'head_employee_id' => $jefaFinanzas->id,
            'level' => 2,
            'status' => 'active',
        ]);

        $sistemasArea = Area::create([
            'identificador' => 'AR-APP-02',
            'code' => 'SIS',
            'name' => 'Sistemas',
            'descripcion' => 'Tecnología',
            'parent_id' => $finanzasArea->id,
            'head_employee_id' => $jefeSistemas->id,
            'level' => 3,
            'status' => 'active',
        ]);

        $empleado = Employee::create([
            'user_id' => 'EMP-APP-01',
            'name' => 'Empleado de Sistemas',
            'type' => 'biometric',
            'area_id' => $sistemasArea->id,
        ]);

        $incidencia = Incidencia::create([
            'asunto' => 'Justificación de retraso',
            'tipo_justificacion' => 'Retraso',
            'fecha_justificacion' => '2026-09-20',
            'empleado_id' => $empleado->id,
            'area_id' => $sistemasArea->id,
            'numero_empleado' => 'EMP-APP-01',
            'motivo' => 'Necesito justificar el retraso por tráfico.',
            'estado' => 'pendiente',
        ]);

        $service = new IncidentApprovalService;
        $route = $service->generateRoute($incidencia);

        $this->assertCount(3, $route);
        $this->assertSame(
            [$jefeSistemas->id, $jefaFinanzas->id, $rector->id],
            $route->pluck('approver_employee_id')->all()
        );
        $this->assertDatabaseHas('incidencia_approvals', [
            'incidencia_id' => $incidencia->id,
            'sequence' => 1,
            'approver_employee_id' => $jefeSistemas->id,
            'status' => 'pending',
        ]);
    }

    public function test_employee_area_update_records_organization_history(): void
    {
        $user = User::factory()->create([
            'name' => 'Analista RH',
            'username' => 'rh.admin',
            'role' => 'admin',
        ]);

        $sistemasArea = Area::create([
            'identificador' => 'ORG-UPDATE-01',
            'code' => 'SIS',
            'name' => 'Sistemas',
            'descripcion' => 'Área tecnológica',
            'level' => 2,
            'status' => 'active',
        ]);

        $finanzasArea = Area::create([
            'identificador' => 'ORG-UPDATE-02',
            'code' => 'FIN',
            'name' => 'Finanzas',
            'descripcion' => 'Área financiera',
            'level' => 2,
            'status' => 'active',
        ]);

        $empleado = Employee::create([
            'user_id' => 'EMP-AREA-UPDATE',
            'name' => 'Empleado de prueba',
            'type' => 'biometric',
            'area_id' => $sistemasArea->id,
            'auth_user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->put(route('employees.update', $empleado), [
                'name' => 'Empleado de prueba',
                'area_id' => $finanzasArea->id,
            ])
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('organization_histories', [
            'employee_id' => $empleado->id,
            'from_area_id' => $sistemasArea->id,
            'to_area_id' => $finanzasArea->id,
            'event_type' => 'transfer',
        ]);
    }

    public function test_same_area_assignment_does_not_create_duplicate_history_entry(): void
    {
        $user = User::factory()->create([
            'name' => 'Analista RH',
            'username' => 'rh.admin',
            'role' => 'admin',
        ]);

        $area = Area::create([
            'identificador' => 'ORG-SAME-01',
            'code' => 'SIS',
            'name' => 'Sistemas',
            'descripcion' => 'Área tecnológica',
            'level' => 2,
            'status' => 'active',
        ]);

        $empleado = Employee::create([
            'user_id' => 'EMP-SAME-AREA',
            'name' => 'Empleado sin movimiento',
            'type' => 'biometric',
            'area_id' => $area->id,
            'auth_user_id' => $user->id,
        ]);

        $record = OrganizationHistoryService::recordAreaChange($empleado, $area, $area, $user, 'transfer', 'Sin cambio real');

        $this->assertNull($record);
        $this->assertDatabaseMissing('organization_histories', [
            'employee_id' => $empleado->id,
            'from_area_id' => $area->id,
            'to_area_id' => $area->id,
        ]);
    }

    public function test_attendance_observation_can_be_recorded_from_ui_route(): void
    {
        $user = User::factory()->create([
            'name' => 'Analista RH',
            'username' => 'rh.admin',
            'role' => 'admin',
        ]);

        $area = Area::create([
            'identificador' => 'ORG-OBS-UI',
            'code' => 'OBS',
            'name' => 'Observación',
            'descripcion' => 'Área de pruebas de observación',
            'level' => 2,
            'status' => 'active',
        ]);

        $empleado = Employee::create([
            'user_id' => 'EMP-OBS-UI',
            'name' => 'Empleado con observación',
            'type' => 'biometric',
            'area_id' => $area->id,
        ]);

        $device = Device::create([
            'name' => 'Checador de observación',
            'ip' => '192.168.0.21',
            'port' => 4370,
            'password' => 'secret',
            'serial_number' => 'DEVICE-OBS-UI',
            'device_name' => 'Device Obs UI',
            'status' => 'online',
            'description' => 'Dispositivo de pruebas',
        ]);

        $attendance = Attendance::create([
            'device_id' => $device->id,
            'employee_id' => $empleado->id,
            'user_id' => $empleado->user_id,
            'state' => 0,
            'type' => 0,
            'recorded_at' => now(),
            'attendance_type' => 'biometric',
            'source' => 'zkteco',
        ]);

        $this->actingAs($user)
            ->post(route('attendances.observations.store', $attendance), [
                'kind' => 'late',
                'message' => 'Llegó tarde por tráfico',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_observations', [
            'attendance_id' => $attendance->id,
            'employee_id' => $empleado->id,
            'kind' => 'late',
            'message' => 'Llegó tarde por tráfico',
            'source' => 'ui',
        ]);
    }

    public function test_organization_history_and_attendance_observations_are_persisted(): void
    {
        $user = User::factory()->create([
            'name' => 'Analista RH',
            'username' => 'rh.admin',
        ]);

        $sistemasArea = Area::create([
            'identificador' => 'ORG-HIST-01',
            'code' => 'SIS',
            'name' => 'Sistemas',
            'descripcion' => 'Área tecnológica',
            'level' => 2,
            'status' => 'active',
        ]);

        $finanzasArea = Area::create([
            'identificador' => 'ORG-HIST-02',
            'code' => 'FIN',
            'name' => 'Finanzas',
            'descripcion' => 'Área financiera',
            'level' => 2,
            'status' => 'active',
        ]);

        $empleado = Employee::create([
            'user_id' => 'EMP-HIST-01',
            'name' => 'Empleado de prueba',
            'type' => 'biometric',
            'area_id' => $sistemasArea->id,
        ]);

        $history = OrganizationHistoryService::recordAreaChange($empleado, $sistemasArea, $finanzasArea, $user, 'transfer', 'Cambio de área por reasignación');

        $this->assertSame($sistemasArea->id, $history->from_area_id);
        $this->assertSame($finanzasArea->id, $history->to_area_id);
        $this->assertDatabaseHas('organization_histories', [
            'employee_id' => $empleado->id,
            'event_type' => 'transfer',
            'from_area_id' => $sistemasArea->id,
            'to_area_id' => $finanzasArea->id,
        ]);

        $device = Device::create([
            'name' => 'Checador de prueba',
            'ip' => '192.168.0.10',
            'port' => 4370,
            'password' => 'secret',
            'serial_number' => 'DEVICE-TEST-01',
            'device_name' => 'Device Test',
            'status' => 'online',
            'description' => 'Dispositivo de pruebas',
        ]);

        $attendance = Attendance::create([
            'device_id' => $device->id,
            'employee_id' => $empleado->id,
            'user_id' => $user->id,
            'state' => 0,
            'type' => 0,
            'recorded_at' => now(),
            'attendance_type' => 'biometric',
            'source' => 'zkteco',
        ]);

        $observation = AttendanceObservationService::record($attendance, $empleado, $user, 'late', 'Llegó tarde por tráfico');

        $this->assertSame('late', $observation->kind);
        $this->assertDatabaseHas('attendance_observations', [
            'attendance_id' => $attendance->id,
            'employee_id' => $empleado->id,
            'kind' => 'late',
            'message' => 'Llegó tarde por tráfico',
        ]);
    }

    public function test_incidence_approval_status_creates_audit_log(): void
    {
        $approver = User::factory()->create([
            'name' => 'Aprobador',
            'username' => 'approver',
            'role' => Role::Admin,
        ]);

        $area = Area::create([
            'identificador' => 'AR-AUDIT',
            'code' => 'AUD',
            'name' => 'Auditoría',
            'descripcion' => 'Área para aprobación auditada',
            'head_employee_id' => null,
            'level' => 2,
            'status' => 'active',
        ]);

        $approverEmployee = Employee::create([
            'user_id' => 'EMP-APPROVER-01',
            'name' => 'Jefe de Auditoría',
            'type' => 'admin',
            'auth_user_id' => $approver->id,
            'area_id' => $area->id,
        ]);
        $area->update(['head_employee_id' => $approverEmployee->id]);

        $employee = Employee::create([
            'user_id' => 'EMP-AUDIT-01',
            'name' => 'Empleado auditado',
            'type' => 'biometric',
            'area_id' => $area->id,
        ]);

        $incidencia = Incidencia::create([
            'asunto' => 'Auditable',
            'tipo_justificacion' => 'Permiso',
            'fecha_justificacion' => '2026-09-20',
            'empleado_id' => $employee->id,
            'area_id' => $area->id,
            'numero_empleado' => 'EMP-AUDIT-01',
            'motivo' => 'Necesita aprobación.',
            'estado' => 'pendiente',
        ]);

        app(\App\Services\IncidentApprovalService::class)->generateRoute($incidencia);

        $this->actingAs($approver)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $approver->id,
            'auditable_type' => Incidencia::class,
            'auditable_id' => $incidencia->id,
            'action' => 'status_changed',
            'module' => 'incidencias',
        ]);
    }
}
