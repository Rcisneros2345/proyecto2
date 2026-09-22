<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_group_permissions');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_permission_groups');
    }

    public function profesores(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Academia\Profesor::class,
            'profesor_permission_groups',
            'permission_group_id',
            'profesor_clave_profesor',
            'id',
            'clave_profesor'
        );
    }

    public function getModulesWithPermissionsAttribute(): Collection
    {
        // Get all permissions for this group, include their modules
        $permissions = $this->permissions()->with('module')->get();

        // Group modules by their slug, collecting their actions
        $grouped = $permissions->reduce(
            function (Collection $acc, $permission) {
                $module = $permission->module;
                if (! $module) {
                    return $acc;
                }

                $slug = $module->slug;
                $action = $permission->action;

                if ($acc->has($slug)) {
                    $acc = $acc->put($slug, array_merge($acc[$slug] ?? [], [$action]));
                } else {
                    $acc = $acc->put($slug, [$action]);
                }

                return $acc;
            },
            new Collection
        );

        // Transform into the desired structure - query modules directly
        return $grouped->map(function (array $actions, string $slug): Collection {
            // Query the module from the modules table
            $module = Module::where('slug', $slug)->first();
            if (! $module) {
                return Collection::make(['name' => 'Unknown', 'group' => 'Unknown', 'icon' => '', 'actions' => $actions]);
            }

            return Collection::make([
                'name' => $module->name,
                'group' => $module->group_name,
                'icon' => $module->icon,
                'actions' => array_unique($actions),
            ]);
        });
    }
}
