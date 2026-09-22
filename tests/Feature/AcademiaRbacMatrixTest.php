<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AcademiaRbacMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ModulePermissionSeeder::class]);
    }

    public function test_write_routes_declare_fine_grained_module_actions(): void
    {
        $expected = [
            'academia.ciclos.store' => 'academia.ciclos,create',
            'academia.ciclos.update' => 'academia.ciclos,update',
            'academia.ciclos.destroy' => 'academia.ciclos,delete',
            'academia.ciclos.activo' => 'academia.ciclos,activo',
            'academia.cursos.store' => 'academia.cursos,create',
            'academia.cursos.update' => 'academia.cursos,update',
            'academia.cursos.destroy' => 'academia.cursos,delete',
            'academia.cursos.materia.add' => 'academia.cursos,materia',
            'academia.planes.store' => 'academia.planes,create',
            'academia.planes.update' => 'academia.planes,update',
            'academia.planes.destroy' => 'academia.planes,delete',
        ];

        foreach ($expected as $name => $middleware) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, "Falta la ruta {$name}");
            $this->assertStringContainsString($middleware, implode('|', $route->gatherMiddleware()), "{$name} no declara {$middleware}");
        }
    }

    public function test_operator_group_and_admin_are_distinguished_on_cycle_create(): void
    {
        $operator = User::factory()->create(['role' => Role::Operator]);
        $this->assertForbiddenFor($operator, route('academia.ciclos.create'));

        $group = PermissionGroup::create(['name' => 'Ciclo capturista', 'description' => 'Prueba', 'is_default' => false]);
        $this->attachPermission($group, 'academia', 'view');
        $this->attachPermission($group, 'academia.ciclos', 'create');
        $groupUser = User::factory()->create(['role' => Role::Operator]);
        $employee = Employee::create(['user_id' => 'RBAC-CICLO', 'name' => 'RBAC Ciclo', 'type' => 'admin', 'auth_user_id' => $groupUser->id]);
        $employee->permissionGroups()->attach($group->id);

        $this->actingAs($groupUser)->get(route('academia.ciclos.create'))->assertOk();
        $this->actingAs(User::factory()->create(['role' => Role::Admin]))
            ->get(route('academia.ciclos.create'))
            ->assertOk();
    }

    private function attachPermission(PermissionGroup $group, string $moduleSlug, string $action): void
    {
        $permission = Permission::whereHas('module', fn ($query) => $query->where('slug', $moduleSlug))
            ->where('action', $action)
            ->firstOrFail();
        $group->permissions()->attach($permission->id);
    }

    private function assertForbiddenFor(User $user, string $url): void
    {
        $this->actingAs($user)->get($url)->assertForbidden();
    }
}
