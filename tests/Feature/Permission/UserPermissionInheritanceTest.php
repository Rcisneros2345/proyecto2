<?php

namespace Tests\Feature\Permission;

use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionInheritanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed modules and permissions
        $this->artisan('db:seed', [
            '--class' => \Database\Seeders\ModulePermissionSeeder::class,
        ]);
    }

    public function test_usuario_con_un_grupo_tiene_sus_permisos(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $module = Module::where('slug', 'dispositivos')->firstOrFail();

        $viewPermission = Permission::whereHas('module', fn ($query) => $query->where('slug', 'dispositivos'))
            ->where('action', 'view')
            ->firstOrFail();

        $group = PermissionGroup::create([
            'name' => 'Operadores Test',
            'description' => 'Grupo de prueba',
            'is_default' => false,
        ]);

        // Attach permission to group
        $group->permissions()->attach($viewPermission->id);

        // Associate group to user via employee_permission_groups table
        // First, create or find an employee for this user
        $employee = $user->employeeAssignments()->first();

        if (! $employee) {
            $employee = new \App\Models\Employee([
                'user_id' => 'E-TEST-001',
                'name' => 'Empleado Test',
                'type' => 'biometric',
                'auth_user_id' => $user->id,
            ]);
            $employee->save();
        }

        // Attach group to employee
        $employee->permissionGroups()->sync([$group->id]);

        $this->actingAs($user);

        $this->assertTrue($user->hasModulePermission('dispositivos', 'view'), 'Usuario con un grupo debe tener el permiso');
        $this->assertTrue($user->hasPermission('dispositivos', 'view'), 'Usuario con un grupo debe tener el permiso (hasPermission)');
    }

    public function test_usuario_con_2_o_mas_grupos_acumula_permisos_or_logico(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $viewDevicePerm = Permission::whereHas('module', fn ($query) => $query->where('slug', 'dispositivos'))
            ->where('action', 'view')
            ->firstOrFail();

        $viewEmployeePerm = Permission::whereHas('module', fn ($query) => $query->where('slug', 'empleados'))
            ->where('action', 'view')
            ->firstOrFail();

        $group1 = PermissionGroup::create([
            'name' => 'Grupo A',
            'description' => 'Grupo A de prueba',
            'is_default' => false,
        ]);

        $group2 = PermissionGroup::create([
            'name' => 'Grupo B',
            'description' => 'Grupo B de prueba',
            'is_default' => false,
        ]);

        // Create employee if not exists
        $employee = $user->employeeAssignments()->first();

        if (! $employee) {
            $employee = new \App\Models\Employee([
                'user_id' => 'E-TEST-002',
                'name' => 'Empleado Test 2',
                'type' => 'biometric',
                'auth_user_id' => $user->id,
            ]);
            $employee->save();
        }

        // Attach both groups to employee
        $employee->permissionGroups()->sync([$group1->id, $group2->id]);

        // Attach permissions to groups
        $group1->permissions()->attach($viewDevicePerm->id);
        $group2->permissions()->attach($viewEmployeePerm->id);

        $this->actingAs($user);

        // User should have permissions from both groups (OR logic)
        $this->assertTrue($user->hasModulePermission('dispositivos', 'view'), 'Usuario debe tener permiso del grupo A');
        $this->assertTrue($user->hasModulePermission('empleados', 'view'), 'Usuario debe tener permiso del grupo B (acumulación OR)');
    }

    public function test_usuario_sin_grupos_no_tiene_permisos_salvo_admin(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        // User without any permission groups

        $this->actingAs($user);

        $this->assertFalse($user->hasModulePermission('dispositivos', 'view'), 'Usuario sin grupos no debe tener permisos');
        $this->assertFalse($user->hasPermission('dispositivos', 'view'), 'Usuario sin grupos no debe tener permisos');

        // But admin should always have permissions
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $this->assertTrue($admin->hasModulePermission('dispositivos', 'view'), 'Admin siempre debe tener permisos');
        $this->assertTrue($admin->hasPermission('dispositivos', 'view'), 'Admin siempre debe tener permisos (hasPermission)');
    }
}
