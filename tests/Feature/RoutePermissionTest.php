<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Employee;
use App\Models\FirebirdSync;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ModulePermissionSeeder::class]);

        // Crear grupo RH con permiso dispositivos.view
        // Refleja el grupo real creado en producción 2026-09-20
        $rhGroup = PermissionGroup::firstOrCreate(['name' => 'RH']);
        $dispositivosView = Permission::whereHas('module', fn ($q) => $q->where('slug', 'dispositivos'))
            ->where('action', 'view')
            ->first();
        if ($dispositivosView && ! $rhGroup->permissions->contains($dispositivosView->id)) {
            $rhGroup->permissions()->attach($dispositivosView->id);
        }
    }

    // ── employees.sobrantes ──────────────────────────────────────────

    public function test_sobrantes_with_dispositivos_view_permission_returns_200(): void
    {
        $user = $this->createUserWithPermission('dispositivos', 'view');

        $this->actingAs($user)->get(route('employees.sobrantes'))->assertOk();
    }

    public function test_sobrantes_without_permission_returns_403(): void
    {
        $user = $this->createOperatorWithoutPermissions();

        $this->actingAs($user)->get(route('employees.sobrantes'))->assertForbidden();
    }

    public function test_sobrantes_without_session_redirects_to_login(): void
    {
        $this->get(route('employees.sobrantes'))->assertRedirect();
    }

    // ── employees.sobrantes.data ─────────────────────────────────────

    public function test_sobrantes_data_with_dispositivos_view_permission_returns_200(): void
    {
        $user = $this->createUserWithPermission('dispositivos', 'view');

        $this->actingAs($user)->get(route('employees.sobrantes.data'))->assertOk();
    }

    public function test_sobrantes_data_without_permission_returns_403(): void
    {
        $user = $this->createOperatorWithoutPermissions();

        $this->actingAs($user)->get(route('employees.sobrantes.data'))->assertForbidden();
    }

    // ── firebird.index ───────────────────────────────────────────────

    public function test_firebird_index_with_firebird_view_permission_returns_200(): void
    {
        $user = $this->createUserWithPermission('firebird', 'view');

        $this->actingAs($user)->get(route('firebird.index'))->assertOk();
    }

    public function test_firebird_index_without_permission_returns_403(): void
    {
        $user = $this->createOperatorWithoutPermissions();

        $this->actingAs($user)->get(route('firebird.index'))->assertForbidden();
    }

    // ── firebird.sync ────────────────────────────────────────────────

    public function test_firebird_sync_with_firebird_view_permission_returns_200(): void
    {
        $user = $this->createUserWithPermission('firebird', 'view');
        $sync = $this->createFirebirdSync();

        $this->actingAs($user)->get(route('firebird.sync', $sync))->assertOk();
    }

    public function test_firebird_sync_without_permission_returns_403(): void
    {
        $user = $this->createOperatorWithoutPermissions();
        $sync = $this->createFirebirdSync();

        $this->actingAs($user)->get(route('firebird.sync', $sync))->assertForbidden();
    }

    // ── firebird.status ──────────────────────────────────────────────

    public function test_firebird_status_with_firebird_view_permission_returns_200(): void
    {
        $user = $this->createUserWithPermission('firebird', 'view');
        $sync = $this->createFirebirdSync();

        $this->actingAs($user)->get(route('firebird.status', $sync))->assertOk();
    }

    public function test_firebird_status_without_permission_returns_403(): void
    {
        $user = $this->createOperatorWithoutPermissions();
        $sync = $this->createFirebirdSync();

        $this->actingAs($user)->get(route('firebird.status', $sync))->assertForbidden();
    }

    // ── Admin bypass ─────────────────────────────────────────────────

    public function test_admin_bypasses_all_permission_checks(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $sync = $this->createFirebirdSync();

        $this->actingAs($admin)->get(route('employees.sobrantes'))->assertOk();
        $this->actingAs($admin)->get(route('employees.sobrantes.data'))->assertOk();
        $this->actingAs($admin)->get(route('firebird.index'))->assertOk();
        $this->actingAs($admin)->get(route('firebird.sync', $sync))->assertOk();
        $this->actingAs($admin)->get(route('firebird.status', $sync))->assertOk();
    }

    // ── Grupo RH real ─────────────────────────────────────────────────

    public function test_user_in_rh_group_can_access_sobrantes(): void
    {
        $rhGroup = PermissionGroup::where('name', 'RH')->first();
        $this->assertNotNull($rhGroup, 'Grupo RH debe existir (creado en seeder o DB)');

        $user = User::factory()->create(['role' => Role::Operator]);
        $employee = Employee::create([
            'user_id' => 'TEST-'.$user->id,
            'name' => 'Test RH Employee',
            'type' => 'admin',
            'auth_user_id' => $user->id,
        ]);
        $employee->permissionGroups()->attach($rhGroup->id);

        $this->actingAs($user)->get(route('employees.sobrantes'))->assertOk();
        $this->actingAs($user)->get(route('employees.sobrantes.data'))->assertOk();
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function createUserWithPermission(string $moduleSlug, string $action): User
    {
        $group = PermissionGroup::create([
            'name' => "Test {$moduleSlug} {$action}",
            'description' => 'Grupo de prueba',
            'is_default' => false,
        ]);

        $permission = Permission::whereHas('module', fn ($q) => $q->where('slug', $moduleSlug))
            ->where('action', $action)
            ->firstOrFail();

        $group->permissions()->attach($permission->id);

        $user = User::factory()->create(['role' => Role::Operator]);
        $employee = Employee::create([
            'user_id' => 'TEST-'.$user->id,
            'name' => 'Test Employee',
            'type' => 'admin',
            'auth_user_id' => $user->id,
        ]);
        $employee->permissionGroups()->attach($group->id);

        return $user;
    }

    private function createOperatorWithoutPermissions(): User
    {
        return User::factory()->create(['role' => Role::Operator]);
    }

    private function createFirebirdSync(): FirebirdSync
    {
        return FirebirdSync::create([
            'operation' => 'sync_catalogos',
            'ciclo' => 'TEST',
            'status' => 'pending',
            'stage' => 'init',
            'started_at' => now(),
            'processed' => 0,
            'total' => 0,
            'created_count' => 0,
            'updated_count' => 0,
            'deleted_count' => 0,
        ]);
    }
}
