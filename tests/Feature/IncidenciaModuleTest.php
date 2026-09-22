<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Area;
use App\Models\Employee;
use App\Models\Incidencia;
use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\PermissionGroupPermission;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidenciaModuleTest extends TestCase
{
    use RefreshDatabase;

    private function grantIncidenciaPermission(User $user, string $action): void
    {
        $module = Module::firstOrCreate(
            ['slug' => 'incidencias'],
            ['name' => 'Incidencias', 'description' => 'Incidencias', 'active' => true]
        );
        $permission = Permission::firstOrCreate(
            ['module_id' => $module->id, 'slug' => "incidencias.{$action}"],
            ['name' => "Incidencias {$action}", 'action' => $action]
        );
        $group = PermissionGroup::create(['name' => "Grupo {$action}"]);
        PermissionGroupPermission::create([
            'permission_group_id' => $group->id,
            'permission_id' => $permission->id,
        ]);

        $employee = $user->employeeAssignments()->firstOrCreate([
            'user_id' => "AUTH-{$user->id}",
        ], [
            'name' => $user->name,
            'type' => 'biometric',
        ]);
        $employee->permissionGroups()->attach($group->id);
    }

    public function test_employee_can_create_an_incidence_and_automatic_authorization_is_assigned(): void
    {
        $responsable = Employee::create([
            'user_id' => '1001',
            'name' => 'Responsable del área',
            'type' => 'admin',
        ]);

        $area = Area::create([
            'identificador' => 'AR-INC',
            'descripcion' => 'Área de pruebas',
            'empleado_responsable_id' => $responsable->id,
        ]);

        $puesto = Puesto::create([
            'identificador' => 'P-INC',
            'descripcion' => 'Analista',
            'area_id' => $area->id,
        ]);

        $employee = Employee::create([
            'user_id' => '2001',
            'name' => 'Empleado de prueba',
            'type' => 'biometric',
            'area_id' => $area->id,
            'puesto_id' => $puesto->id,
        ]);

        $user = User::factory()->create([
            'name' => 'Usuario prueba',
            'email' => 'prueba@example.com',
            'role' => Role::Admin,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('incidencias.store'), [
            'tipo_persona' => 'empleado',
            'empleado_id' => $employee->id,
            'asunto' => 'Justificación de ausencia',
            'tipo_justificacion' => 'Permiso',
            'fecha_justificacion' => '2026-09-10',
            'motivo' => 'Necesito justificar la ausencia por cita médica.',
        ]);

        $response->assertRedirect(route('incidencias.index'));
        $this->assertDatabaseHas('incidencias', [
            'empleado_id' => $employee->id,
            'area_id' => $area->id,
            'puesto_id' => $puesto->id,
            'responsable_area_id' => $responsable->id,
            'estado' => 'pendiente',
        ]);

        $incidencia = Incidencia::first();
        $this->assertSame('Empleado de prueba', $incidencia->empleado->name);
    }

    public function test_authorized_user_can_change_incidence_status(): void
    {
        $empleado = Employee::create([
            'user_id' => '3001',
            'name' => 'Empleado autorizado',
            'type' => 'biometric',
        ]);

        $incidencia = Incidencia::create([
            'asunto' => 'Justificación',
            'tipo_justificacion' => 'Permiso',
            'fecha_justificacion' => '2026-09-10',
            'empleado_id' => $empleado->id,
            'numero_empleado' => '3001',
            'motivo' => 'Motivo de prueba',
            'estado' => 'pendiente',
        ]);

        $user = User::factory()->create([
            'name' => 'Usuario aprobador',
            'email' => 'aprobador@example.com',
            'role' => Role::Admin,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('incidencias.estado', $incidencia), [
            'estado' => 'aprobada',
        ]);

        $response->assertRedirect(route('incidencias.index'));
        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'aprobada',
            'autorizado_por_user_id' => $user->id,
        ]);
    }

    public function test_employee_cannot_create_an_incidence_for_another_employee(): void
    {
        $user = User::factory()->create(['role' => Role::Operator]);
        $this->grantIncidenciaPermission($user, 'view');
        $this->grantIncidenciaPermission($user, 'create');

        $ownEmployee = $user->employeeAssignments()->first();
        $otherEmployee = Employee::create([
            'user_id' => 'OTHER-1',
            'name' => 'Otra persona',
            'type' => 'biometric',
        ]);

        $this->actingAs($user)
            ->post(route('incidencias.store'), [
                'tipo_persona' => 'empleado',
                'empleado_id' => $otherEmployee->id,
                'asunto' => 'Intento no autorizado',
                'tipo_justificacion' => 'Permiso',
                'fecha_justificacion' => '2026-09-10',
                'motivo' => 'No debe registrarse para otra persona.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('incidencias', ['empleado_id' => $otherEmployee->id]);
        $this->assertNotNull($ownEmployee);
    }

    public function test_area_responsible_with_approve_permission_can_approve_area_incidence(): void
    {
        $responsibleUser = User::factory()->create(['role' => Role::Operator]);
        $this->grantIncidenciaPermission($responsibleUser, 'approve');

        $responsible = $responsibleUser->employeeAssignments()->first();
        $area = Area::create([
            'identificador' => 'AR-AUTH',
            'descripcion' => 'Área autorizadora',
            'empleado_responsable_id' => $responsible->id,
        ]);
        $employee = Employee::create([
            'user_id' => 'EMP-AUTH',
            'name' => 'Empleado del área',
            'type' => 'biometric',
            'area_id' => $area->id,
        ]);
        $incidencia = Incidencia::create([
            'asunto' => 'Permiso',
            'tipo_justificacion' => 'Personal',
            'fecha_justificacion' => '2026-09-10',
            'empleado_id' => $employee->id,
            'area_id' => $area->id,
            'responsable_area_id' => $responsible->id,
            'estado' => 'pendiente',
            'motivo' => 'Motivo válido',
        ]);

        $this->actingAs($responsibleUser)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'aprobada',
            'autorizado_por_user_id' => $responsibleUser->id,
        ]);
    }

    public function test_incidence_stores_scheduled_absence_schedule_comments_and_request(): void
    {
        $user = User::factory()->create(['role' => Role::Admin]);
        $employee = Employee::create([
            'user_id' => '4001',
            'name' => 'Empleado con horario',
            'type' => 'biometric',
        ]);

        $this->actingAs($user)
            ->post(route('incidencias.store'), [
                'tipo_persona' => 'empleado',
                'empleado_id' => $employee->id,
                'asunto' => 'Permiso por horario',
                'tipo_justificacion' => 'Permiso personal',
                'fecha_falta_programada' => '2026-09-22',
                'tipo_duracion' => 'horario',
                'hora_inicio' => '10:00',
                'hora_fin' => '12:30',
                'motivo' => 'Cita programada',
                'comentarios' => 'Regresa después de la cita.',
                'solicitud' => 'Solicito autorización para ausentarme.',
            ])
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('incidencias', [
            'empleado_id' => $employee->id,
            'fecha_falta_programada' => '2026-09-22',
            'tipo_duracion' => 'horario',
            'hora_inicio' => '10:00:00',
            'hora_fin' => '12:30:00',
            'comentarios' => 'Regresa después de la cita.',
            'solicitud' => 'Solicito autorización para ausentarme.',
        ]);
    }

    public function test_approved_incidence_notifies_its_creator(): void
    {
        $creator = User::factory()->create(['role' => Role::Admin]);
        $approver = User::factory()->create(['role' => Role::Admin]);
        $employee = Employee::create([
            'user_id' => '5001',
            'name' => 'Empleado notificado',
            'type' => 'biometric',
        ]);
        $incidencia = Incidencia::create([
            'asunto' => 'Notificación de aprobación',
            'tipo_justificacion' => 'Permiso',
            'fecha_justificacion' => '2026-09-23',
            'fecha_falta_programada' => '2026-09-23',
            'tipo_duracion' => 'dia_completo',
            'empleado_id' => $employee->id,
            'numero_empleado' => '5001',
            'motivo' => 'Motivo de prueba',
            'estado' => 'pendiente',
            'created_by_user_id' => $creator->id,
        ]);

        $this->actingAs($approver)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $creator->id,
            'type' => \App\Notifications\IncidenciaStatusNotification::class,
        ]);
    }

    public function test_creator_can_mark_approved_incidence_viewed_and_signed(): void
    {
        $creator = User::factory()->create(['role' => Role::Admin]);
        $approver = User::factory()->create(['role' => Role::Admin]);
        $incidencia = Incidencia::create([
            'asunto' => 'Firma posterior a aprobación',
            'tipo_justificacion' => 'Permiso',
            'fecha_justificacion' => '2026-09-24',
            'fecha_falta_programada' => '2026-09-24',
            'tipo_duracion' => 'dia_completo',
            'motivo' => 'Prueba de transición',
            'estado' => 'pendiente',
            'created_by_user_id' => $creator->id,
        ]);

        $this->actingAs($approver)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertRedirect(route('incidencias.index'));

        $this->actingAs($creator)
            ->post(route('incidencias.vista', $incidencia))
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'visto_por_user_id' => $creator->id,
        ]);

        $this->actingAs($creator)
            ->post(route('incidencias.firmar', $incidencia))
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'firmado_por_user_id' => $creator->id,
        ]);
    }

    public function test_persisted_approval_route_has_priority_over_legacy_area_responsible_field(): void
    {
        $legacyResponsibleUser = User::factory()->create(['role' => Role::Operator]);
        $this->grantIncidenciaPermission($legacyResponsibleUser, 'approve');
        $legacyResponsible = $legacyResponsibleUser->employeeAssignments()->first();

        $routeApproverUser = User::factory()->create(['role' => Role::Operator]);
        $this->grantIncidenciaPermission($routeApproverUser, 'approve');
        $routeApprover = $routeApproverUser->employeeAssignments()->first();

        $routeArea = Area::create([
            'identificador' => 'AR-ROUTE',
            'descripcion' => 'Área con ruta de aprobación',
            'empleado_responsable_id' => $legacyResponsible->id,
            'head_employee_id' => $routeApprover->id,
            'level' => 2,
            'status' => 'active',
        ]);

        $employee = Employee::create([
            'user_id' => 'EMP-ROUTE',
            'name' => 'Empleado de la ruta',
            'type' => 'biometric',
            'area_id' => $routeArea->id,
        ]);

        $incidencia = Incidencia::create([
            'asunto' => 'Ruta prioritaria',
            'tipo_justificacion' => 'Personal',
            'fecha_justificacion' => '2026-09-10',
            'empleado_id' => $employee->id,
            'area_id' => $routeArea->id,
            'responsable_area_id' => $legacyResponsible->id,
            'estado' => 'pendiente',
            'motivo' => 'El responsable legado no debe ser el aprobador real.',
        ]);

        $incidencia->refresh();
        app(\App\Services\IncidentApprovalService::class)->generateRoute($incidencia);

        $this->actingAs($legacyResponsibleUser)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertForbidden();

        $this->actingAs($routeApproverUser)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertRedirect(route('incidencias.index'));

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'aprobada',
            'autorizado_por_user_id' => $routeApproverUser->id,
        ]);
    }

    public function test_unrelated_user_cannot_approve_an_incidence_even_with_approve_permission(): void
    {
        $owner = User::factory()->create(['role' => Role::Operator]);
        $unrelated = User::factory()->create(['role' => Role::Operator]);
        $this->grantIncidenciaPermission($unrelated, 'approve');

        $employee = Employee::create([
            'user_id' => 'EMP-PRIVATE',
            'name' => 'Empleado privado',
            'type' => 'biometric',
        ]);
        $incidencia = Incidencia::create([
            'asunto' => 'Privada',
            'tipo_justificacion' => 'Personal',
            'fecha_justificacion' => '2026-09-10',
            'empleado_id' => $employee->id,
            'estado' => 'pendiente',
            'motivo' => 'Motivo privado',
        ]);

        $this->actingAs($unrelated)
            ->post(route('incidencias.estado', $incidencia), ['estado' => 'aprobada'])
            ->assertForbidden();

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'pendiente',
        ]);
        $this->assertNotSame($owner->id, $unrelated->id);
    }
}
