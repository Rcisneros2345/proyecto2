<?php

namespace Tests\Feature\Permission;

use App\Enums\Role;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGroupAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_groups_update_effective_permissions_and_menu_immediately(): void
    {
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ModulePermissionSeeder::class]);

        $admin = User::factory()->create(['role' => Role::Admin]);
        $operator = User::factory()->create(['role' => Role::Operator]);
        Employee::create([
            'user_id' => 'RBAC-MULTI',
            'name' => 'Usuario Multi Grupo',
            'type' => 'admin',
            'auth_user_id' => $operator->id,
        ]);

        $devicesGroup = PermissionGroup::create([
            'name' => 'Grupo Dispositivos Test',
            'description' => 'Acceso a dispositivos',
            'is_default' => false,
        ]);
        $employeesGroup = PermissionGroup::create([
            'name' => 'Grupo Empleados Test',
            'description' => 'Acceso a empleados',
            'is_default' => false,
        ]);

        $devicesGroup->permissions()->attach(
            Permission::whereHas('module', fn ($query) => $query->where('slug', 'dispositivos'))
                ->where('action', 'view')->firstOrFail()->id
        );
        $employeesGroup->permissions()->attach(
            Permission::whereHas('module', fn ($query) => $query->where('slug', 'empleados'))
                ->where('action', 'view')->firstOrFail()->id
        );

        $this->assertFalse($operator->canAccessModule('dispositivos'));
        $this->assertFalse($operator->canAccessModule('empleados'));

        $this->actingAs($admin)
            ->put(route('preferencia.usuarios.update', $operator), [
                'name' => $operator->name,
                'username' => $operator->username,
                'email' => $operator->email,
                'role' => 'operator',
                'group_ids' => [$devicesGroup->id, $employeesGroup->id],
            ])
            ->assertRedirect(route('preferencia.usuarios.index'));

        $operator->refresh();
        $this->assertTrue($operator->canAccessModule('dispositivos'));
        $this->assertTrue($operator->canAccessModule('empleados'));
        $this->actingAs($operator)->get(route('employees.index'))->assertOk()->assertSee('Empleados');

        $this->actingAs($admin)
            ->put(route('preferencia.usuarios.update', $operator), [
                'name' => $operator->name,
                'username' => $operator->username,
                'email' => $operator->email,
                'role' => 'operator',
                'group_ids' => [$devicesGroup->id],
            ])
            ->assertRedirect(route('preferencia.usuarios.index'));

        $operator->refresh();
        $this->assertTrue($operator->canAccessModule('dispositivos'));
        $this->assertFalse($operator->canAccessModule('empleados'));
        $this->actingAs($operator)->get(route('employees.index'))->assertForbidden();
    }
}
