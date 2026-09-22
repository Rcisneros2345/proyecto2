<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeFormRequest;
use App\Jobs\DeprovisionEmployeeJob;
use App\Jobs\SyncEmployeeToDeviceJob;
use App\Models\Academia\Sede;
use App\Models\Area;
use App\Models\Device;
use App\Models\DeviceSync;
use App\Models\Employee;
use App\Models\Fingerprint;
use App\Models\Puesto;
use App\Models\User;
use App\Services\OrganizationHistoryService;
use App\Services\SobranteService;
use App\Services\ZktecoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request, SobranteService $sobranteService): View
    {
        $query = Employee::query()
            ->with(['devices', 'sede', 'area', 'puesto', 'horariosLaborales'])
            ->withCount('fingerprints')
            ->orderByRaw('LOWER(name)')
            ->orderBy('id');

        if ($deviceId = $request->query('device_id')) {
            $query->whereHas('devices', fn ($q) => $q->where('devices.id', $deviceId));
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('user_id', $search)
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        // Filtro por estado: activos, bajas, todos
        $status = $request->query('status', 'todos');
        if ($status === 'activos') {
            $query->activos();
        } elseif ($status === 'bajas') {
            $query->bajas();
        }

        $employees = $query->paginate((int) $request->query('per_page', 25))->withQueryString();

        // Stats para tabs
        $sobrantesStats = $sobranteService->getStats();

        // Opciones de filtros (distinct, limitadas para performance)
        $cargos = Employee::whereNotNull('cargo')->where('cargo', '!=', '')->distinct()->pluck('cargo')->sort()->values();
        $departamentos = Employee::whereNotNull('departamento')->where('departamento', '!=', '')->distinct()->pluck('departamento')->sort()->values();
        $sedes = Sede::query()
            ->orderBy('descripcion')
            ->select(['id_campus', 'descripcion'])
            ->get();

        return view('employees.index', [
            'employees' => $employees,
            'devices' => Device::orderBy('name')->get(),
            'status' => $status,
            'sobrantesStats' => $sobrantesStats,
            'cargos' => $cargos,
            'departamentos' => $departamentos,
            'sedes' => $sedes,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = Employee::query()
            ->with(['devices', 'sede', 'area', 'puesto', 'horariosLaborales'])
            ->withCount('fingerprints')
            ->orderByRaw('LOWER(name)')
            ->orderBy('id');

        if ($deviceId = $request->query('device_id')) {
            $query->whereHas('devices', fn ($q) => $q->where('devices.id', $deviceId));
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('user_id', $search)
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        // Filtro por cargo
        if ($cargo = trim((string) $request->query('cargo', ''))) {
            $query->where('cargo', $cargo);
        }

        // Filtro por departamento
        if ($departamento = trim((string) $request->query('departamento', ''))) {
            $query->where('departamento', $departamento);
        }

        // Filtro por sede
        if ($idCampus = $request->query('id_campus')) {
            $query->where('id_campus', $idCampus);
        }

        // Filtro por sin huellas
        if ($request->boolean('sin_huella')) {
            $query->whereDoesntHave('fingerprints');
        }

        // Filtro por sin enrolar (sin devices)
        if ($request->boolean('sin_device')) {
            $query->whereDoesntHave('devices');
        }

        // Filtro por estado
        $status = $request->query('status', 'todos');
        if ($status === 'activos') {
            $query->activos();
        } elseif ($status === 'bajas') {
            $query->bajas();
        }

        $employees = $query->paginate((int) $request->query('per_page', 25));

        $data = $employees->getCollection()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'user_id' => $employee->user_id,
                'name' => $employee->name,
                'numero_empleado' => $employee->numero_empleado,
                'clave_profesor' => $employee->clave_profesor,
                'sexo' => $employee->sexo,
                'sexo_label' => $employee->sexo_label,
                'fecha_nacimiento' => $employee->fecha_nacimiento?->format('d/m/Y'),
                'nacionalidad' => $employee->nacionalidad,
                'estado_civil' => $employee->estado_civil,
                'telefono' => $employee->telefono,
                'celular' => $employee->celular,
                'email' => $employee->email,
                'cargo' => $employee->cargo,
                'departamento' => $employee->departamento,
                'puesto_label' => $employee->puesto?->descripcion ?? $employee->cargo,
                'area_label' => $employee->area?->descripcion ?? $employee->departamento,
                'contrato' => $employee->contrato,
                'nivel' => $employee->nivel,
                'id_campus' => $employee->id_campus,
                'fecha_ingreso' => $employee->fecha_ingreso?->format('d/m/Y'),
                'sede_label' => $employee->sede->descripcion ?? $employee->id_campus ?? null,
                'horarios' => $employee->horariosLaborales->map(fn ($horario) => [
                    'dia' => $horario->diaNombre(),
                    'entrada' => $horario->hora_entrada?->format('H:i'),
                    'salida' => $horario->hora_salida?->format('H:i'),
                ])->values(),
                'status_actual' => $employee->status_actual,
                'is_baja' => $employee->status_actual === 'B',
                'fingerprints_count' => $employee->fingerprints_count ?? $employee->fingerprints->count() ?? 0,
                'has_card' => $employee->devices->contains(fn ($d) => filled($d->pivot->card_number)),
                'last_sync' => $employee->syncs->first() ?? null,
                'edit_url' => route('employees.edit', $employee),
                'sync_url' => route('employees.sync-devices', $employee),
                'destroy_url' => route('employees.destroy', $employee),
                'devices' => $employee->devices->map(function ($device) {
                    return [
                        'id' => $device->id,
                        'name' => $device->name,
                        'pivot' => [
                            'device_uid' => $device->pivot->device_uid,
                            'role' => $device->pivot->role,
                            'card_number' => $device->pivot->card_number,
                            'active' => $device->pivot->active,
                            'fingerprint_count' => $device->pivot->fingerprint_count,
                        ],
                    ];
                }),
            ];
        });

        return response()->json([
            'employees' => $data,
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
                'from' => $employees->firstItem(),
                'to' => $employees->lastItem(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'devices' => Device::orderBy('name')->get(),
            'areas' => Area::query()->orderBy('identificador')->get(['id', 'identificador', 'descripcion']),
            'puestos' => Puesto::query()->with('area')->orderBy('identificador')->get(['id', 'identificador', 'descripcion', 'area_id']),
        ]);
    }

    public function edit(Employee $employee): View
    {
        $employee->load([
            'devices',
            'fingerprints' => fn ($query) => $query->with('device:id,name')->orderBy('finger'),
            'syncs' => fn ($query) => $query->with('device:id,name')->latest()->limit(8),
        ]);
        $availableDevices = Device::query()
            ->whereNotIn('id', $employee->devices->pluck('id'))
            ->orderBy('name')
            ->get();
        $syncDevices = Device::query()->orderBy('name')->get();
        $totalDevices = $syncDevices->count();

        $availableFingerprints = Fingerprint::query()
            ->where('employee_id', '!=', $employee->id)
            ->with(['employee:id,user_id,name', 'device:id,name'])
            ->get()
            ->sortBy(fn (Fingerprint $fingerprint): string => sprintf(
                '%s|%s|%02d|%s',
                mb_strtolower($fingerprint->employee?->name ?? ''),
                mb_strtolower($fingerprint->device?->name ?? ''),
                $fingerprint->finger,
                $fingerprint->template_hash
            ))
            ->unique(fn (Fingerprint $fingerprint): string => implode('|', [
                $fingerprint->employee_id,
                $fingerprint->device_id ?? 'legacy',
                $fingerprint->finger,
                $fingerprint->template_hash,
            ]))
            ->values();

        return view('employees.edit', compact('employee', 'availableFingerprints', 'availableDevices', 'syncDevices', 'totalDevices'))
            ->with([
                'areas' => Area::query()->orderBy('identificador')->get(['id', 'identificador', 'descripcion']),
                'puestos' => Puesto::query()->with('area')->orderBy('identificador')->get(['id', 'identificador', 'descripcion', 'area_id']),
                'users' => User::query()
                    ->where(function ($query) use ($employee): void {
                        $query->whereDoesntHave('employee')
                            ->orWhere('id', $employee->auth_user_id);
                    })
                    ->whereDoesntHave('professor')
                    ->orderBy('name')
                    ->get(['id', 'name', 'username', 'email']),
            ]);
    }

    public function fingerprints(Request $request): View
    {
        $employees = Employee::query()
            ->with(['devices', 'fingerprints'])
            ->withCount('fingerprints')
            ->when($request->query('device_id'), fn ($query, $deviceId) => $query->whereHas(
                'devices',
                fn ($q) => $q->where('devices.id', $deviceId)
            ))
            ->when(trim((string) $request->query('q', '')), function ($query, $search): void {
                $search = trim((string) $search);
                $query->where(fn ($employeeQuery) => $employeeQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('user_id', $search)
                    ->orWhere('user_id', 'like', "%{$search}%"));
            })
            ->orderByRaw('LOWER(name)')
            ->orderBy('id')
            ->paginate((int) $request->query('per_page', 25))
            ->withQueryString();

        return view('fingerprints.index', [
            'employees' => $employees,
            'devices' => Device::orderBy('name')->get(),
        ]);
    }

    /**
     * El nombre pertenece al catálogo; rol y contraseña pertenecen a cada
     * enrolamiento, por lo que se propagan a TODOS los checadores donde la
     * persona está dada de alta.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:24'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'puesto_id' => ['nullable', 'exists:puestos,id'],
            'auth_user_id' => ['nullable', 'exists:users,id'],
            'password' => ['nullable', 'digits_between:1,8'],
            'card_number' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'role' => ['nullable', 'in:0,13,14'],
        ]);

        $authUserId = $data['auth_user_id'] ?? null;

        // Si se asigna un usuario, validar que no esté asignado a un profesor
        if ($authUserId !== null && $authUserId !== '') {
            $user = User::findOrFail($authUserId);
            $assignedProfessor = $user->professor()->first();

            if ($assignedProfessor && $assignedProfessor->clave_profesor !== ($employee->clave_profesor ?? null)) {
                return Redirect::back()->withInput()
                    ->with('error', "El usuario '{$user->name}' ya está asignado como profesor (clave: {$assignedProfessor->clave_profesor}). Un usuario solo puede ser empleado OU profesor, no ambos.");
            }

            // Actualizar el tipo de usuario a 'employee'
            $user->update(['type' => 'employee']);
        } else {
            // Si se quita la asignación, limpiar el tipo solo si no hay otro rol
            if ($employee->auth_user_id) {
                $oldUser = User::find($employee->auth_user_id);
                if ($oldUser && ! $oldUser->professor()->exists()) {
                    $oldUser->update(['type' => null]);
                }
            }
        }

        $previousAreaId = $employee->area_id;
        $newAreaId = $data['area_id'] ?? null;

        $employee->update([
            'name' => $data['name'],
            'area_id' => $newAreaId,
            'puesto_id' => $data['puesto_id'] ?? null,
            'auth_user_id' => $authUserId,
        ]);

        if ($previousAreaId !== $newAreaId && $employee->wasChanged('area_id')) {
            $actor = auth()->user();
            $fromArea = $previousAreaId ? Area::find($previousAreaId) : null;
            $toArea = $newAreaId ? Area::find($newAreaId) : null;

            if ($actor && $fromArea && $toArea) {
                OrganizationHistoryService::recordAreaChange(
                    $employee,
                    $fromArea,
                    $toArea,
                    $actor,
                    'transfer',
                    'Cambio de área por reasignación del empleado.'
                );
            }
        }

        // Credenciales de enrolamiento (Card B): el PIN/rol viven en el pivot
        // device_employee, no en el catálogo. Vacío = conservar (placeholder
        // "Conservar actual"); solo se aplican cuando el formulario los envía.
        // El cast 'encrypted' del pivot cifra el PIN en reposo al guardar.
        $pivotUpdate = [];
        if ($request->has('role') && $data['role'] !== null && $data['role'] !== '') {
            $pivotUpdate['role'] = (int) $data['role'];
        }
        if (filled($data['password'] ?? null)) {
            $pivotUpdate['password'] = $data['password'];
        }
        if ($pivotUpdate !== []) {
            $enrolledIds = $employee->devices()->pluck('devices.id');
            foreach ($enrolledIds as $enrolledId) {
                $employee->devices()->updateExistingPivot($enrolledId, $pivotUpdate);
            }
        }

        $enrollmentsCount = $employee->devices()->count();
        $message = $enrollmentsCount > 0
            ? "Empleado actualizado en el catálogo. Usa 'Sincronizar credenciales' para propagar password/rol/tarjeta a los {$enrollmentsCount} checador(es)."
            : 'Empleado actualizado en el catálogo (sin enrolamientos activos).';

        return Redirect::route('employees.index')->with('success', $message);
    }

    public function syncToDevices(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'device_ids' => ['required', 'array', 'min:1'],
            'device_ids.*' => ['integer', 'exists:devices,id'],
        ]);

        $created = 0;
        foreach (array_unique(array_map('intval', $data['device_ids'])) as $deviceId) {
            $device = Device::findOrFail($deviceId);
            $alreadyQueued = DeviceSync::query()
                ->where('employee_id', $employee->id)
                ->where('device_id', $device->id)
                ->where('operation', 'sync_full')
                ->whereIn('status', ['queued', 'running'])
                ->exists();

            if ($alreadyQueued) {
                continue;
            }

            $fingerprintCount = $employee->fingerprints()->select('finger')->distinct()->count('finger');
            $sync = DeviceSync::create([
                'device_id' => $device->id,
                'employee_id' => $employee->id,
                'status' => 'queued',
                'operation' => 'sync_full',
                'stage' => 'Preparando',
                'total' => max(1, $fingerprintCount + 1),
            ]);
            SyncEmployeeToDeviceJob::dispatch($employee, $device, $sync);
            $created++;
        }

        return Redirect::back()->with(
            $created ? 'success' : 'info',
            $created ? "Se enviaron {$created} tarea(s) a la cola." : 'Los dispositivos seleccionados ya tienen una sincronización pendiente.'
        );
    }

    public function enrollOnDevice(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
        ]);

        $device = Device::findOrFail($data['device_id']);
        $sourceEnrollment = $employee->devices()->first();
        $cardNumber = $sourceEnrollment?->pivot->card_number;
        if ($cardNumber !== null && DB::table('device_employee')
            ->where('device_id', $device->id)
            ->where('card_number', $cardNumber)
            ->where('employee_id', '!=', $employee->id)
            ->exists()) {
            return Redirect::back()->with('error', 'La tarjeta '.$cardNumber.' ya está asignada en '.$device->name.'. Asigna otra tarjeta después de enrolar.');
        }

        try {
            $ok = (new ZktecoService($device))->enrollEmployee(
                $employee,
                $sourceEnrollment?->pivot->password,
                $cardNumber,
                (int) ($sourceEnrollment?->pivot->role ?? 0)
            );
        } catch (ZktecoConnectionException $exception) {
            return Redirect::back()->with('error', 'No se pudo conectar con '.$device->name.' ('.$exception->deviceIp.':'.$device->port.'). Verifica que esté encendido, en la misma red y que la IP sea correcta.');
        }

        return Redirect::back()->with(
            $ok ? 'success' : 'error',
            $ok
                ? 'Empleado y huellas actualizados en '.$device->name.'.'
                : 'No se pudo actualizar el empleado en '.$device->name.'.'
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = (new EmployeeFormRequest)->validate();

        $device = Device::findOrFail($data['device_id']);
        $service = new ZktecoService($device);

        $employee = Employee::query()->firstOrCreate(
            ['user_id' => $data['user_id']],
            ['name' => $data['name'], 'area_id' => $data['area_id'] ?? null, 'puesto_id' => $data['puesto_id'] ?? null]
        );

        $employee->update([
            'name' => $data['name'],
            'area_id' => $data['area_id'] ?? null,
            'puesto_id' => $data['puesto_id'] ?? null,
        ]);

        // setUser() sincroniza al terminar y el nuevo flujo crea el catálogo
        // central + la fila pivote automáticamente.
        $ok = $service->setUser([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'password' => $data['password'] ?: $device->password,
            'role' => (int) $data['role'],
            'card_number' => $data['card_number'] ?: null,
        ]);

        return Redirect::to('/employees')
            ->with($ok ? 'success' : 'error', $ok ? 'Empleado agregado al catálogo y enrolado en el dispositivo.' : 'No se pudo agregar el empleado al dispositivo.');
    }

    /**
     * Da de baja a la persona en TODOS los checadores donde está enrolada.
     * Marca el empleado como 'Baja' (status_actual=B) y dispara jobs asíncronos
     * para desprovisionar de cada checador. El catálogo se elimina solo cuando
     * todos los jobs completan exitosamente (o manualmente si hay fallos).
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        // Marcar como baja lógica inmediatamente
        $employee->update(['status_actual' => 'B']);

        $enrollments = $employee->devices()->get();
        $created = 0;

        foreach ($enrollments as $device) {
            // Evitar duplicar si ya hay un job en cola para este dispositivo
            $alreadyQueued = DeviceSync::query()
                ->where('employee_id', $employee->id)
                ->where('device_id', $device->id)
                ->where('operation', 'deprovision')
                ->whereIn('status', ['queued', 'running'])
                ->exists();

            if ($alreadyQueued) {
                continue;
            }

            $sync = DeviceSync::create([
                'device_id' => $device->id,
                'employee_id' => $employee->id,
                'status' => 'queued',
                'operation' => 'deprovision',
                'stage' => 'Pendiente',
                'total' => 1,
            ]);

            DeprovisionEmployeeJob::dispatch($employee, $device, $sync);
            $created++;
        }

        $message = $created > 0
            ? "Empleado marcado como Baja. {$created} job(s) de desprovisionamiento encolados."
            : 'Empleado marcado como Baja (sin enrolamientos activos).';

        return Redirect::route('employees.index')->with('success', $message);
    }

    /**
     * Muestra la vista de sobrantes: device_employee sin employee válido
     * o con employee dado de baja (status_actual='B').
     */
    public function sobrantes(Request $request, SobranteService $sobranteService): View
    {
        $includeIgnored = $request->boolean('ignored', false);
        $deviceId = $request->input('device_id') ? (int) $request->input('device_id') : null;
        $type = $request->input('type');
        $search = $request->input('q');

        $stats = $sobranteService->getStats($deviceId);

        $sobrantes = $sobranteService->query(
            $includeIgnored,
            $deviceId,
            $type,
            $search,
        )->paginate(25)->withQueryString();

        return view('employees.sobrantes', [
            'sobrantes' => $sobrantes,
            'stats' => $stats,
            'includeIgnored' => $includeIgnored,
            'deviceId' => $deviceId,
            'type' => $type,
            'search' => $search,
            'devices' => Device::orderBy('name')->get(),
        ]);
    }

    /**
     * AJAX data endpoint para DataTables de sobrantes.
     */
    public function sobrantesData(Request $request, SobranteService $sobranteService): JsonResponse
    {
        $includeIgnored = $request->boolean('ignored', false);
        $deviceId = $request->input('device_id') ? (int) $request->input('device_id') : null;
        $type = $request->input('type');
        $search = $request->input('q');

        $sobrantes = $sobranteService->query(
            $includeIgnored,
            $deviceId,
            $type,
            $search,
        )->get();

        return response()->json([
            'data' => $sobrantes,
            'stats' => $sobranteService->getStats($deviceId),
        ]);
    }

    public function enrollmentDiff(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'device_ids' => ['required', 'array', 'min:1'],
            'device_ids.*' => ['integer', 'exists:devices,id'],
        ]);

        $deviceIds = array_values(array_unique(array_map('intval', $validated['device_ids'])));

        // Precarga en 2 queries (sin N+1): devices destino + enrolamientos actuales del empleado.
        $devices = Device::whereIn('id', $deviceIds)->get()->keyBy('id');
        $enrolled = $employee->devices()->whereIn('devices.id', $deviceIds)->get()->keyBy('id');

        $syncService = app(\App\Services\EmployeeDeviceSyncService::class);
        $diff = [];

        foreach ($deviceIds as $deviceId) {
            $device = $devices->get($deviceId);
            if (! $device) {
                continue;
            }

            // BelongsToMany::first() devuelve el modelo Employee con ->pivot (ver docs/checklist-backend.md).
            $enrollment = $enrolled->get($deviceId);
            $pivot = $enrollment?->pivot;

            $package = $syncService->packageFor($employee, $device);

            $currentRole = $pivot?->role;
            $currentCardNumber = $pivot?->card_number;
            $currentFingerprintCount = $pivot?->fingerprint_count;
            $currentPassword = $pivot?->password;
            $currentActive = $pivot?->active;

            // Determine action: create si no hay enrolamiento, noop si nada cambia, update en otro caso.
            if (! $enrollment) {
                $action = 'create';
            } elseif (
                $package->role === $currentRole
                && $package->cardNumber === $currentCardNumber
                && $package->fingerprintCount() === (int) $currentFingerprintCount
                && $package->password === $currentPassword
            ) {
                $action = 'noop';
            } else {
                $action = 'update';
            }

            // changes: solo "Cambia"/"Igual" + valores no sensibles; el PIN nunca expone su valor real.
            $changes = [
                [
                    'field' => 'role',
                    'status' => $package->role === $currentRole ? 'Igual' : 'Cambia',
                    'from' => $currentRole,
                    'to' => $package->role,
                ],
                [
                    'field' => 'card_number',
                    'status' => $package->cardNumber === $currentCardNumber ? 'Igual' : 'Cambia',
                    'from' => $currentCardNumber,
                    'to' => $package->cardNumber,
                ],
                [
                    'field' => 'pin',
                    'status' => $package->password === $currentPassword ? 'Igual' : 'Cambia',
                    'from' => null,
                    'to' => null,
                ],
                [
                    'field' => 'fingerprint_count',
                    'status' => $package->fingerprintCount() === (int) $currentFingerprintCount ? 'Igual' : 'Cambia',
                    'from' => $currentFingerprintCount !== null ? (int) $currentFingerprintCount : null,
                    'to' => $package->fingerprintCount(),
                ],
                [
                    'field' => 'active',
                    'status' => 'Igual',
                    'from' => $currentActive !== null ? ($currentActive ? 'Activo' : 'Inactivo') : null,
                    'to' => $currentActive !== null ? ($currentActive ? 'Activo' : 'Inactivo') : null,
                ],
            ];

            // warnings: card_duplicate — wherePivot solo vale sobre la relación, no en whereHas (docs/checklist-backend.md).
            $warnings = [];
            if ($package->cardNumber) {
                $duplicate = $device->employees()
                    ->wherePivot('card_number', $package->cardNumber)
                    ->where('employees.id', '!=', $employee->id)
                    ->exists();
                if ($duplicate) {
                    $warnings[] = 'card_duplicate';
                }
            }

            $diff[] = [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'action' => $action,
                'changes' => $changes,
                'warnings' => $warnings,
            ];
        }

        return response()->json([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'diff' => $diff,
        ]);
    }

    /**
     * Estado de sincronización más reciente por dispositivo para el empleado.
     * Solo lectura: alimenta el panel de progreso (polling 3s) sin tocar jobs.
     */
    public function syncProgress(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'device_ids' => ['sometimes', 'array'],
            'device_ids.*' => ['integer', 'exists:devices,id'],
        ]);

        $query = DeviceSync::query()
            ->where('employee_id', $employee->id)
            ->with('device:id,name');

        if (! empty($validated['device_ids'])) {
            $query->whereIn('device_id', array_map('intval', $validated['device_ids']));
        }

        $syncs = $query->orderByDesc('id')->limit(40)->get()
            ->unique('device_id')->values()
            ->map(fn (DeviceSync $sync): array => [
                'device_id' => $sync->device_id,
                'device_name' => $sync->device?->name ?? 'Dispositivo eliminado',
                'status' => $sync->status,
                'stage' => $sync->stage,
                'processed' => $sync->processed,
                'total' => $sync->total,
                'error_message' => $sync->error_message,
                'is_terminal' => ! in_array($sync->status, ['queued', 'running'], true),
                'updated_at' => $sync->updated_at?->format('d/m H:i'),
            ]);

        return response()->json([
            'employee_id' => $employee->id,
            'syncs' => $syncs,
        ]);
    }

    /**
     * Actualiza el código de tarjeta RFID del empleado en UN checador.
     *
     * La tarjeta vive en el pivot `device_employee`, no en el catálogo:
     * debe ser única por dispositivo. No toca PIN/rol/nombre.
     */
    public function updateCard(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'card_number' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
        ]);

        $deviceId = (int) $data['device_id'];
        $cardNumber = filled($data['card_number'] ?? null) ? $data['card_number'] : null;

        if (! $employee->devices()->where('devices.id', $deviceId)->exists()) {
            return Redirect::back()->with('error', 'El empleado no está enrolado en ese checador.');
        }

        if ($cardNumber !== null && DB::table('device_employee')
            ->where('device_id', $deviceId)
            ->where('card_number', $cardNumber)
            ->where('employee_id', '!=', $employee->id)
            ->exists()) {
            return Redirect::back()->with('error', 'La tarjeta '.$cardNumber.' ya está asignada a otro empleado en ese checador.');
        }

        $employee->devices()->updateExistingPivot($deviceId, ['card_number' => $cardNumber]);

        return Redirect::back()->with('success', 'Tarjeta actualizada en el catálogo. Sincroniza el checador para aplicarla en el equipo.');
    }

    /**
     * Marca un sobrante como ignorado (revisado por admin).
     */
    public function sobrantesIgnore(int $deviceId, int $deviceUid, SobranteService $sobranteService): RedirectResponse
    {
        $success = $sobranteService->ignore($deviceId, $deviceUid);

        $message = $success
            ? 'Sobrante marcado como ignorado.'
            : 'No se encontró el sobrante.';

        return Redirect::route('employees.sobrantes')
            ->with($success ? 'success' : 'error', $message);
    }

    /**
     * Revoca el estado ignorado de un sobrante.
     */
    public function sobrantesUnignore(int $deviceId, int $deviceUid, SobranteService $sobranteService): RedirectResponse
    {
        $success = $sobranteService->unignore($deviceId, $deviceUid);

        $message = $success
            ? 'Sobrante restaurado.'
            : 'No se encontró el sobrante.';

        return Redirect::route('employees.sobrantes')
            ->with($success ? 'success' : 'error', $message);
    }

    /**
     * Elimina un sobrante: hardware + pivot, JAMÁS Employee.
     *
     * Tipo A: removeUserFromDevice + delete pivot (no hay Employee)
     * Tipo B: removeUserFromDevice + delete pivot (Employee se conserva)
     */
    public function sobrantesRemove(
        int $deviceId,
        int $deviceUid,
        string $type,
        SobranteService $sobranteService,
    ): RedirectResponse {
        $result = $sobranteService->remove($deviceId, $deviceUid, $type);

        return Redirect::route('employees.sobrantes')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
