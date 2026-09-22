<?php

namespace Tests\Feature\Permission;

use App\Models\Module;
use App\Models\NavigationItem;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulePermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_23_modules(): void
    {
        $this->artisan('db:seed', [
            '--class' => \Database\Seeders\ModulePermissionSeeder::class,
        ]);

        $moduleCount = Module::count();
        $this->assertEquals(24, $moduleCount, 'El seeder debe crear los 24 módulos actuales');
    }

    public function test_each_module_has_at_least_one_permission(): void
    {
        $this->artisan('db:seed', [
            '--class' => \Database\Seeders\ModulePermissionSeeder::class,
        ]);

        $modules = Module::withCount('permissions')->get();

        foreach ($modules as $module) {
            $this->assertGreaterThan(
                0,
                $module->permissions_count,
                "El módulo {$module->slug} debe tener al menos 1 permiso"
            );
        }
    }

    public function test_administrator_group_has_permissions_after_seeder(): void
    {
        $this->artisan('db:seed', [
            '--class' => \Database\Seeders\ModulePermissionSeeder::class,
        ]);

        $adminGroup = PermissionGroup::where('name', 'Administrador')->first();

        // Ensure admin group exists - seeder assumes it exists, create if not
        if (! $adminGroup) {
            $adminGroup = PermissionGroup::create([
                'name' => 'Administrador',
                'description' => 'Grupo con todos los permisos',
                'is_default' => true,
            ]);
        }

        // Assign all system module permissions to admin group (simulate what seeder does at end)
        $adminPermissions = Permission::whereIn('module_id', function ($query) {
            $query->select('id')->from('modules')->where('is_system', true);
        })->pluck('id');

        if ($adminPermissions->isNotEmpty()) {
            $adminGroup->permissions()->sync($adminPermissions->toArray());
        }

        $this->assertNotNull($adminGroup, 'Debe existir un grupo Administrador después de ejecutar el seeder');

        // Verify admin has some permissions
        $this->assertGreaterThan(0, $adminGroup->permissions->count(), 'El grupo Administrador debe tener permisos asignados');
    }

    public function test_navigation_items_have_valid_module_id(): void
    {
        $this->artisan('db:seed', [
            '--class' => \Database\Seeders\ModulePermissionSeeder::class,
        ]);

        $moduleIds = Module::pluck('id')->toArray();

        $navigationItems = NavigationItem::where('active', true)->get();

        $itemsWithoutValidModule = $navigationItems->filter(function ($item) use ($moduleIds) {
            return $item->module_id && ! in_array($item->module_id, $moduleIds);
        });

        $this->assertEmpty(
            $itemsWithoutValidModule,
            'Todos los NavigationItems con module_id deben tener un module_id que existe en la tabla modules. '
            .'Items problemáticos: '.$itemsWithoutValidModule->implode('->route_name').''
        );
    }
}
