<?php

namespace Tests\Feature\Permission;

use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PermissionGroupPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the modules and permissions
        Artisan::call('db:seed', [
            '--class' => \Database\Seeders\ModulePermissionSeeder::class,
        ]);

        // Ensure admin user exists with admin role
        if (! User::where('role', 'admin')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'admin',
            ]);
        }

        // Ensure admin group exists
        if (! PermissionGroup::where('name', 'Administrador')->exists()) {
            PermissionGroup::create([
                'name' => 'Administrador',
                'description' => 'Grupo con todos los permisos',
                'is_default' => true,
            ]);
        }
    }

    public function test_get_permission_group_permissions_returns_200(): void
    {
        // Login as admin
        $adminUser = User::where('role', 'admin')->first();

        $adminGroup = PermissionGroup::where('name', 'Administrador')->first();

        $this->assertNotNull($adminGroup, 'El grupo Administrador debe existir');

        $response = $this->actingAs($adminUser)->get(route('permission-groups.permissions', $adminGroup));

        $response->assertStatus(200);
    }

    public function test_post_permission_group_permissions_syncs_correctly(): void
    {
        // Login as admin
        $adminUser = User::where('role', 'admin')->first();

        $adminGroup = PermissionGroup::where('name', 'Administrador')->first();

        $this->assertNotNull($adminGroup, 'El grupo Administrador debe existir');

        // Get permissions for one module (dispositivos)
        $modulePermissions = Permission::whereHas('module', function ($query) {
            $query->where('slug', 'dispositivos');
        })->take(3)->pluck('id')->toArray();

        $this->assertNotEmpty($modulePermissions, 'Debe haber permisos para el módulo dispositivos');

        $response = $this->actingAs($adminUser)->post(
            route('permission-groups.permissions', $adminGroup),
            ['permission_ids' => $modulePermissions]
        );

        $response->assertRedirect();

        // Verify permissions were synced
        $updatedGroup = PermissionGroup::find($adminGroup->id);
        $syncedPermissions = $updatedGroup->permissions->pluck('id')->toArray();

        foreach ($modulePermissions as $permId) {
            $this->assertContains($permId, $syncedPermissions, 'El permiso debe haberse sincronizado al grupo');
        }
    }

    public function test_group_can_have_permissions_from_multiple_modules(): void
    {
        $employeeGroup = PermissionGroup::create([
            'name' => 'Empleados Test',
            'description' => 'Grupo de prueba para testing',
            'is_default' => false,
        ]);

        // Add permissions from different modules
        $dispositivosPerm = Permission::whereHas('module', function ($query) {
            $query->where('slug', 'dispositivos');
        })->first();

        $empleadosPerm = Permission::whereHas('module', function ($query) {
            $query->where('slug', 'empleados');
        })->first();

        $this->assertNotNull($dispositivosPerm, 'Debe existir un permiso para dispositivos');
        $this->assertNotNull($empleadosPerm, 'Debe existir un permiso para empleados');

        $employeeGroup->permissions()->sync([
            $dispositivosPerm->id,
            $empleadosPerm->id,
        ]);

        $synced = $employeeGroup->permissions->pluck('module.slug')->toArray();

        $this->assertContains('dispositivos', $synced, 'El grupo debe tener permisos de módulo dispositivos');
        $this->assertContains('empleados', $synced, 'El grupo debe tener permisos de módulo empleados');
    }
}
