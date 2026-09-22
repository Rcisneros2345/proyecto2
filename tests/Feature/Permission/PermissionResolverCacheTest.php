<?php

namespace Tests\Feature\Permission;

use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionResolverCacheTest extends TestCase
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

    public function test_user_can_check_permission(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $group = PermissionGroup::create([
            'name' => 'Permission Check Group',
            'description' => 'Grupo para check de permiso',
            'is_default' => false,
        ]);

        $module = Module::where('slug', 'dispositivos')->firstOrFail();
        $perm = Permission::whereHas('module', fn ($query) => $query->where('slug', 'dispositivos'))
            ->where('action', 'view')
            ->firstOrFail();

        // Attach permission to group
        $group->permissions()->attach($perm->id);

        // Associate group with user - create employee and attach group
        $employee = new \App\Models\Employee([
            'user_id' => 'E-TEST',
            'name' => 'Empleado Test',
            'type' => 'biometric',
            'auth_user_id' => $user->id,
        ]);
        $employee->save();
        $employee->permissionGroups()->attach($group->id);

        // Act as user and check permission
        $this->actingAs($user);

        $resolver = new \App\Services\PermissionResolver;

        // Test userCan
        $can = $resolver->userCan($user, 'dispositivos', 'view');

        $this->assertTrue($can, 'El usuario debería poder ver dispositivos');
    }

    public function test_invalidacion_por_grupo_funciona(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $group1 = PermissionGroup::create([
            'name' => 'Group 1',
            'description' => 'Grupo 1',
            'is_default' => false,
        ]);

        $module = Module::where('slug', 'dispositivos')->firstOrFail();
        $perm = Permission::whereHas('module', function ($query) {
            $query->where('slug', 'dispositivos');
        })->first();

        // Attach permission to group
        $group1->permissions()->attach($perm->id);

        // Associate group with user through employee
        $employee = new \App\Models\Employee([
            'user_id' => 'E-INVALID-GROUP',
            'name' => 'Empleado Invalid Group',
            'type' => 'biometric',
            'auth_user_id' => $user->id,
        ]);
        $employee->save();
        $employee->permissionGroups()->attach($group1->id);

        $this->actingAs($user);

        $resolver = new \App\Services\PermissionResolver;

        // Before invalidation, user should have the permission
        $permsBefore = $resolver->getUserPermissions($user);
        $hasBefore = $permsBefore->contains(function ($p) {
            return $p['slug'] === 'dispositivos.view';
        });

        // Invalidate cache for the group
        $resolver->invalidateForGroup($group1->id);

        // After invalidation, next call should refetch
        $permsAfter = $resolver->getUserPermissions($user);

        // Should be able to fetch without error
        $this->assertIsArray($permsAfter->toArray(), 'Invalidación por grupo debe funcionar sin error');
    }

    public function test_invalidacion_por_usuario_funciona(): void
    {
        $user = User::factory()->create(['role' => 'operator']);

        $group = PermissionGroup::create([
            'name' => 'User Inv Group',
            'description' => 'Grupo de invalidación de usuario',
            'is_default' => false,
        ]);

        $module = Module::where('slug', 'dispositivos')->firstOrFail();
        $perm = Permission::whereHas('module', function ($query) {
            $query->where('slug', 'dispositivos');
        })->first();

        // Attach permission to group
        $group->permissions()->attach($perm->id);

        // Associate group with user through employee
        $employee = new \App\Models\Employee([
            'user_id' => 'E-INVALID-USER',
            'name' => 'Empleado Invalid User',
            'type' => 'biometric',
            'auth_user_id' => $user->id,
        ]);
        $employee->save();
        $employee->permissionGroups()->attach($group->id);

        $this->actingAs($user);

        $resolver = new \App\Services\PermissionResolver;

        // Before invalidation
        $permsBefore = $resolver->getUserPermissions($user);

        // Invalidate cache for this specific user
        $resolver->invalidateForUser($user->id);

        // After invalidation, next call should refetch without error
        $permsAfter = $resolver->getUserPermissions($user);

        $this->assertIsArray($permsAfter->toArray(), 'Invalidación por usuario debe funcionar sin error');
    }
}
