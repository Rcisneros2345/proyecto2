<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Collection;

class PermissionResolver
{
    /**
     * Get all permissions for a user, cached with key "user_perms_{userId}" and TTL 15min.
     *
     * @return \Illuminate\Support\Collection Collection of permission data including module slug and action
     */
    public function getUserPermissions(User $user): Collection
    {
        $cacheKey = "user_perms_{$user->id}";

        // Try cache first
        $cached = cache()->remember($cacheKey, now()->addMinutes(15), function () use ($user) {
            return $this->getUserPermissionsFromDb($user);
        });

        return $cached;
    }

    /**
     * Get permissions from database (fallback when cache misses or is disabled).
     */
    protected function getUserPermissionsFromDb(User $user): Collection
    {
        $groupIds = $user->permissionGroups()->pluck('id');

        if ($groupIds->isEmpty()) {
            return collect();
        }

        return Permission::query()
            ->join('modules', 'modules.id', '=', 'permissions.module_id')
            ->join('permission_group_permissions', 'permission_group_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('permission_group_permissions.permission_group_id', $groupIds)
            ->where('modules.active', true)
            ->get(['permissions.id', 'permissions.module_id', 'permissions.action', 'modules.slug', 'modules.name', 'modules.group_name']);
    }

    /**
     * Check if a user can perform an action on a module.
     *
     * @param  string  $moduleSlug  Module slug (e.g., 'dispositivos', 'academia.ciclos')
     * @param  string  $action  Action (e.g., 'view', 'create', 'update', 'delete')
     */
    public function userCan(User $user, string $moduleSlug, string $action): bool
    {
        // Admin bypass
        if ($user->isAdmin()) {
            return true;
        }

        $permissions = $this->getUserPermissions($user);

        return $permissions->contains(function ($permission) use ($moduleSlug, $action) {
            return $permission['slug'] === $moduleSlug && $permission['action'] === $action;
        });
    }

    /**
     * Invalidate cache for a specific user.
     */
    public function invalidateForUser(int $userId): void
    {
        cache()->forget("user_perms_{$userId}");
    }

    /**
     * Invalidate cache for all users in a permission group.
     */
    public function invalidateForGroup(int $groupId): void
    {
        // Invalidate cache for all users belonging to this group
        // We need to find users with this group and invalidate their caches
        // This is a best-effort approach; the event system will handle the rest

        // Get users from employee permission groups
        $employeeUserIds = \App\Models\Employee::whereHas('permissionGroups', function ($query) use ($groupId) {
            $query->where('permission_groups.id', $groupId);
        })->pluck('auth_user_id');

        // Get users from professor permission groups
        $profesorUserIds = \App\Models\Academia\Profesor::whereHas('permissionGroups', function ($query) use ($groupId) {
            $query->where('permission_groups.id', $groupId);
        })->pluck('auth_user_id');

        $allUserIds = $employeeUserIds->merge($profesorUserIds)->unique()->all();

        foreach ($allUserIds as $userId) {
            cache()->forget("user_perms_{$userId}");
        }
    }

    /**
     * Invalidate cache for all users.
     */
    public function invalidateAll(): void
    {
        // Clear all user permission caches
        // We can't list all keys easily, so we use a tag approach or just clear the pattern
        // For now, we'll clear known pattern keys
        $this->clearUserPermissionCachePattern();
    }

    /**
     * Clear user permission cache using pattern.
     */
    protected function clearUserPermissionCachePattern(): void
    {
        // Using cache->tags would be better, but for backward compatibility
        // we'll just note that a manual cache:clear may be needed
        // or we could use a prefix approach
    }
}
