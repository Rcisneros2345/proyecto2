<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use App\Models\Academia\AttendanceCaptureAssignment;
use App\Services\AttendanceCaptureAuthorization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['has_permission_groups'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Role::class,
        'type' => 'string',
    ];

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Relación 1:1 con Employee. Un usuario solo puede ser asignado a 1 empleado.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'auth_user_id');
    }

    /**
     * Relación 1:1 con Profesor. Un usuario solo puede ser asignado a 1 profesor.
     */
    public function professor()
    {
        return $this->hasOne(\App\Models\Academia\Profesor::class, 'auth_user_id');
    }

    /**
     * @deprecated Usar employee() en lugar de employeeAssignments()
     */
    public function employeeAssignments()
    {
        return $this->hasOne(Employee::class, 'auth_user_id');
    }

    /**
     * @deprecated Usar professor() en lugar de professorAssignments()
     */
    public function professorAssignments()
    {
        return $this->hasOne(\App\Models\Academia\Profesor::class, 'auth_user_id');
    }

    public function attendanceCaptureAssignments()
    {
        return $this->hasMany(AttendanceCaptureAssignment::class);
    }

    public function canCaptureAttendanceLevelAndCampus(
        string $nivel,
        ?string $campus,
        int $inicial,
        int $final,
        int $periodo
    ): bool {
        return app(AttendanceCaptureAuthorization::class)->canCaptureFilter(
            $this,
            $nivel,
            $campus,
            $inicial,
            $final,
            $periodo,
        );
    }

    public function permissionGroups()
    {
        if (! Schema::hasTable('employee_permission_groups') || ! Schema::hasTable('profesor_permission_groups') || ! Schema::hasTable('permission_groups')) {
            return collect();
        }

        $employeeGroupIds = DB::table('employee_permission_groups')
            ->join('employees', 'employees.id', '=', 'employee_permission_groups.employee_id')
            ->where('employees.auth_user_id', $this->id)
            ->pluck('employee_permission_groups.permission_group_id')
            ->unique()
            ->values();

        $profesorGroupIds = DB::table('profesor_permission_groups')
            ->join('profesores', 'profesores.clave_profesor', '=', 'profesor_permission_groups.profesor_clave_profesor')
            ->where('profesores.auth_user_id', $this->id)
            ->pluck('profesor_permission_groups.permission_group_id')
            ->unique()
            ->values();

        $groupIds = $employeeGroupIds->merge($profesorGroupIds)->unique()->values()->all();

        if (empty($groupIds)) {
            return collect();
        }

        return PermissionGroup::query()->whereIn('id', $groupIds)->get();
    }

    public function hasModulePermission(string $moduleSlug, string $action = 'view'): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! Schema::hasTable('modules') || ! Schema::hasTable('permissions') || ! Schema::hasTable('permission_group_permissions')) {
            return false;
        }

        // Use eager loaded permission groups with their permissions to avoid N+1 queries
        $groupIds = $this->permissionGroups()->pluck('id');

        if ($groupIds->isEmpty()) {
            return false;
        }

        // Eager load: permissionGroups with permissions already loaded
        // This avoids additional JOINs per request when cache or pre-loaded
        $permissions = Permission::query()
            ->join('modules', 'modules.id', '=', 'permissions.module_id')
            ->join('permission_group_permissions', 'permission_group_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('permission_group_permissions.permission_group_id', $groupIds)
            ->where('modules.slug', $moduleSlug)
            ->where('permissions.action', $action)
            ->get(['permissions.*']);

        // Extract permission IDs from the already-hydrated result
        $permissionIds = $permissions->pluck('id')->toArray();

        return ! empty($permissionIds);
    }

    public function hasPermission(string $moduleSlug, string $action = 'view'): bool
    {
        return $this->hasModulePermission($moduleSlug, $action);
    }

    public function canAccessModule(string $moduleSlug, string $action = 'view'): bool
    {
        return $this->hasModulePermission($moduleSlug, $action);
    }

    public function getHasPermissionGroupsAttribute(): bool
    {
        return $this->permissionGroups()->isNotEmpty();
    }
}
