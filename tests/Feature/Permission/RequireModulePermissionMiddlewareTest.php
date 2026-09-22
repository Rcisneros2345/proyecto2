<?php

namespace Tests\Feature\Permission;

use App\Models\Employee;
use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequireModulePermissionMiddlewareTest extends TestCase
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

    public function test_sin_permiso_devuelve_403(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $employee = Employee::create([
            'user_id' => 'E-NO-PERM',
            'name' => 'Empleado Sin Permiso',
            'type' => 'biometric',
            'auth_user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/devices', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // Should get 403 since operator doesn't have dispositivos.view permission
        $response->assertStatus(403);
    }

    public function test_con_permiso_devuelve_200(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $group = PermissionGroup::create([
            'name' => 'Con Permiso Group',
            'description' => 'Grupo con permiso',
            'is_default' => false,
        ]);

        $module = Module::where('slug', 'dispositivos')->firstOrFail();
        $perm = Permission::whereHas('module', fn ($query) => $query->where('slug', 'dispositivos'))
            ->where('action', 'view')
            ->firstOrFail();

        $group->permissions()->attach($perm->id);

        $employee = Employee::create([
            'user_id' => 'E-WITH-PERM',
            'name' => 'Empleado Con Permiso',
            'type' => 'biometric',
            'auth_user_id' => $user->id,
        ]);

        $employee->permissionGroups()->attach($group->id);

        $this->actingAs($user);

        $response = $this->get('/devices', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // Should get 200 since user now has the permission
        $response->assertStatus(200);
    }

    public function test_admin_bypass_siempre_pasa(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $employee = Employee::create([
            'user_id' => 'E-ADMIN-BYPASS',
            'name' => 'Empleado Admin Bypass',
            'type' => 'biometric',
            'auth_user_id' => $admin->id,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/devices', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // Admin should always pass (bypass)
        $response->assertStatus(200);
    }
}
