<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Academia\Profesor;
use App\Models\Area;
use App\Models\Employee;
use App\Models\PermissionGroup;
use App\Models\Puesto;
use App\Models\User;
use App\Services\PermissionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PreferenciaUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $type = $request->query('type');

        $users = User::query()
            ->with(['employee.permissionGroups', 'professor.permissionGroups'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when(in_array($type, ['employee', 'professor', 'unassigned'], true), function ($query) use ($type): void {
                if ($type === 'employee') {
                    $query->whereHas('employee');
                } elseif ($type === 'professor') {
                    $query->whereHas('professor');
                } else {
                    $query->whereDoesntHave('employee')->whereDoesntHave('professor');
                }
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('preferencia.index', compact('users', 'search', 'type'));
    }

    public function create()
    {
        $areas = Area::orderBy('identificador')->get();
        $puestos = Puesto::orderBy('identificador')->get();

        $empleadosDisponibles = Employee::whereNull('auth_user_id')
            ->with(['user' => fn ($q) => $q->select('id', 'name')])
            ->latest()
            ->get();

        $profesoresDisponibles = Profesor::whereNull('auth_user_id')
            ->latest()
            ->get();

        return view('preferencia.crear', compact('areas', 'puestos', 'empleadosDisponibles', 'profesoresDisponibles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'preference_type' => ['required', 'in:employee,professor'],
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            // Empleado
            'user_id' => [Rule::requiredIf(fn () => $request->input('preference_type') === 'employee' && ! $request->filled('empleado_existente_id')), 'nullable', 'string', 'max:9', 'unique:employees,user_id'],
            'employee_type' => ['nullable', 'in:biometric,admin'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'puesto_id' => ['nullable', 'exists:puestos,id'],
            'card_number' => ['nullable', 'string', 'max:20'],
            'device_pin' => ['nullable', 'string', 'max:8'],
            // Profesor
            'clave_profesor' => [Rule::requiredIf(fn () => $request->input('preference_type') === 'professor' && ! $request->filled('profesor_existente_id')), 'nullable', 'string', 'max:20', 'unique:profesores,clave_profesor'],
            'profesor_email' => ['nullable', 'email', 'max:150'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'profesor_area_id' => ['nullable', 'exists:areas,id'],
            'profesor_puesto_id' => ['nullable', 'exists:puestos,id'],
            'contrato' => ['nullable', 'string', 'max:50'],
            // Nuevo: empleado/profesor existente
            'empleado_existente_id' => ['nullable', 'exists:employees,id'],
            'profesor_existente_id' => ['nullable', 'exists:profesores,clave_profesor'],
        ]);

        return DB::transaction(function () use ($data) {
            $isEmployee = $data['preference_type'] === 'employee';
            $employee = ! empty($data['empleado_existente_id']) ? Employee::findOrFail($data['empleado_existente_id']) : null;
            $profesor = ! empty($data['profesor_existente_id']) ? Profesor::findOrFail($data['profesor_existente_id']) : null;
            $profileName = $employee?->name ?? $profesor?->nombre_completo ?? $data['name'];
            $reference = $employee?->numero_empleado ?: $employee?->user_id ?: $profesor?->clave_profesor;
            $username = $data['username'] ?? $this->makeUsername($profileName);
            $password = $data['password'] ?: 'UTE'.$reference;

            $user = User::create([
                'name' => $profileName,
                'username' => $username,
                'email' => $data['email'],
                'password' => Hash::make($password),
                'role' => Role::Operator,
                'type' => $data['preference_type'] === 'employee' ? 'employee' : 'professor',
            ]);

            if ($data['preference_type'] === 'employee') {
                // Si seleccionó empleado existente
                if (! empty($data['empleado_existente_id'])) {
                    $empleado = $employee;
                    // Validar que no tenga auth_user_id asignado
                    if (! is_null($empleado->auth_user_id)) {
                        throw new \Exception('Este empleado ya tiene un usuario asignado. Seleccione otro o cree uno nuevo.');
                    }
                    // Vincular el usuario existente al empleado
                    $empleado->auth_user_id = $user->id;
                    $empleado->save();
                } else {
                    // Crear nuevo empleado
                    Employee::create([
                        'auth_user_id' => $user->id,
                        'user_id' => $data['user_id'],
                        'name' => $data['name'],
                        'type' => $data['employee_type'] ?? 'biometric',
                        'area_id' => $data['area_id'] ?? null,
                        'puesto_id' => $data['puesto_id'] ?? null,
                        'card_number' => $data['card_number'] ?? null,
                        'password' => $data['device_pin'] ?? null,
                        'status_actual' => 'A',
                    ]);
                }
            } else {
                // Si seleccionó profesor existente
                if (! empty($data['profesor_existente_id'])) {
                    // Validar que no tenga auth_user_id asignado
                    if (! is_null($profesor->auth_user_id)) {
                        throw new \Exception('Este profesor ya tiene un usuario asignado. Seleccione otro o cree uno nuevo.');
                    }
                    // Vincular el usuario existente al profesor
                    $profesor->auth_user_id = $user->id;
                    $profesor->save();
                } else {
                    // Crear nuevo profesor
                    Profesor::create([
                        'auth_user_id' => $user->id,
                        'clave_profesor' => $data['clave_profesor'],
                        'nombre_profesor' => $data['name'],
                        'paterno' => '',
                        'materno' => '',
                        'email' => $data['profesor_email'] ?? $data['email'],
                        'departamento' => $data['departamento'] ?? null,
                        'area_id' => $data['profesor_area_id'] ?? null,
                        'puesto_id' => $data['profesor_puesto_id'] ?? null,
                        'contrato' => $data['contrato'] ?? null,
                        'status_actual' => 'A',
                        'origen_horario' => 'HD',
                    ]);
                }
            }

            $defaultGroup = PermissionGroup::where('name', $isEmployee ? 'Empleado' : 'Profesor')
                ->where('is_default', true)
                ->first();
            if ($defaultGroup) {
                $this->syncGroups($user, [$defaultGroup->id]);
            }

            return redirect()->route('preferencia.usuarios.index')
                ->with('success', 'Usuario creado correctamente. Usuario: '.$username.' | Contraseña inicial: '.$password);
        });
    }

    public function edit(User $user)
    {
        $user->load(['employee.permissionGroups', 'professor.permissionGroups']);
        $groups = \App\Models\PermissionGroup::query()->orderBy('name')->get();
        $assignedGroupIds = $user->employee?->permissionGroups->pluck('id')->all()
            ?? $user->professor?->permissionGroups->pluck('id')->all()
            ?? [];

        return view('preferencia.editar', compact('user', 'groups', 'assignedGroupIds'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([Role::Admin->value, Role::Operator->value])],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:permission_groups,id'],
        ]);

        DB::transaction(function () use ($data, $user): void {
            $attributes = [
                'name' => $data['name'],
                'username' => $data['username'] ?: null,
                'email' => $data['email'],
                'role' => $data['role'],
            ];

            if (filled($data['password'] ?? null)) {
                $attributes['password'] = Hash::make($data['password']);
            }

            $user->update($attributes);
            $this->syncGroups($user, array_map('intval', $data['group_ids'] ?? []));
            app(PermissionResolver::class)->invalidateForUser($user->id);
        });

        return redirect()->route('preferencia.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    private function syncGroups(User $user, array $groupIds): void
    {
        if ($user->employee) {
            $user->employee->permissionGroups()->sync($groupIds);
        }

        if ($user->professor) {
            $user->professor->permissionGroups()->sync($groupIds);
        }
    }

    private function makeUsername(string $name): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($name)) ?: []));
        $base = Str::lower(Str::ascii(substr($parts[count($parts) - 1] ?? 'u', 0, 1).($parts[0] ?? '')));
        $base = preg_replace('/[^a-z0-9]/', '', $base) ?: 'usuario';
        $username = $base;
        $suffix = 2;

        while (User::where('username', $username)->exists()) {
            $username = $base.$suffix++;
        }

        return $username;
    }
}
