<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Academia\Profesor;
use App\Models\Area;
use App\Models\Employee;
use App\Models\Incidencia;
use App\Notifications\IncidenciaStatusNotification;
use App\Policies\IncidenciaPolicy;
use App\Services\AuditService;
use App\Services\IncidentApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class IncidenciaController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $request->query('estado');
        $q = trim((string) $request->query('q', ''));
        $user = $request->user();

        $incidencias = Incidencia::query()
            ->with(['empleado', 'profesor', 'area', 'puesto', 'director', 'responsableArea'])
            ->when(! $user->isAdmin(), function ($query) use ($user): void {
                $employeeIds = $user->employeeAssignments()->pluck('employees.id');
                $professorKeys = $user->professorAssignments()->pluck('profesores.clave_profesor');

                $query->where(function ($visibleQuery) use ($employeeIds, $professorKeys, $user): void {
                    $visibleQuery
                        ->whereIn('empleado_id', $employeeIds)
                        ->orWhereIn('profesor_clave', $professorKeys)
                        ->orWhereIn('responsable_area_id', $employeeIds)
                        ->orWhereIn('director_id', $employeeIds)
                        ->orWhere('created_by_user_id', $user->id);
                });
            })
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($innerQuery) use ($q) {
                    $innerQuery->where('asunto', 'like', "%{$q}%")
                        ->orWhere('tipo_justificacion', 'like', "%{$q}%")
                        ->orWhere('motivo', 'like', "%{$q}%")
                        ->orWhere('numero_empleado', 'like', "%{$q}%");
                });
            })
            ->latest('fecha_creacion')
            ->paginate(20)
            ->withQueryString();

        return view('incidencias.index', compact('incidencias', 'estado', 'q'));
    }

    public function create(): View
    {
        $user = request()->user();
        $empleados = Employee::query()
            ->when(! $user->isAdmin(), fn ($query) => $query->where('auth_user_id', $user->id))
            ->with(['area.empleadoResponsable', 'area.head', 'puesto'])
            ->orderByRaw('LOWER(name)')
            ->get(['id', 'name', 'user_id', 'area_id', 'puesto_id']);

        $profesores = Profesor::query()
            ->when(! $user->isAdmin(), fn ($query) => $query->where('auth_user_id', $user->id))
            ->with(['area.empleadoResponsable', 'area.head', 'puesto', 'director'])
            ->orderByRaw('LOWER(nombre_profesor)')
            ->get(['clave_profesor', 'nombre_profesor', 'paterno', 'materno', 'area_id', 'puesto_id']);

        return view('incidencias.create', compact('empleados', 'profesores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo_persona' => ['required', 'in:empleado,profesor'],
            'empleado_id' => ['nullable', 'required_if:tipo_persona,empleado', 'exists:employees,id'],
            'profesor_clave' => ['nullable', 'required_if:tipo_persona,profesor', 'exists:profesores,clave_profesor'],
            'asunto' => ['required', 'string', 'max:150'],
            'tipo_justificacion' => ['required', 'string', 'max:80'],
            'fecha_justificacion' => ['nullable', 'date', 'required_without:fecha_falta_programada'],
            'fecha_falta_programada' => ['nullable', 'date', 'required_without:fecha_justificacion'],
            'tipo_duracion' => ['nullable', 'in:horario,dia_completo'],
            'hora_inicio' => ['nullable', 'date_format:H:i', 'required_if:tipo_duracion,horario'],
            'hora_fin' => ['nullable', 'date_format:H:i', 'required_if:tipo_duracion,horario', 'after:hora_inicio'],
            'motivo' => ['required', 'string'],
            'comentarios' => ['nullable', 'string', 'max:5000'],
            'solicitud' => ['nullable', 'string', 'max:5000'],
        ], [
            'empleado_id.required_if' => 'Debe seleccionar un empleado cuando el tipo de persona es empleado.',
            'profesor_clave.required_if' => 'Debe seleccionar un profesor cuando el tipo de persona es profesor.',
        ]);

        $empleado = null;
        $profesor = null;

        if ($data['tipo_persona'] === 'empleado') {
            $empleado = Employee::query()->findOrFail($data['empleado_id']);
            $this->authorize('create', Incidencia::class);
            abort_unless(app(IncidenciaPolicy::class)->createForEmployee(auth()->user(), $empleado), 403);
            $data['numero_empleado'] = $empleado->numero_empleado ?? $empleado->user_id;
            $data['area_id'] = $empleado->area_id;
            $data['puesto_id'] = $empleado->puesto_id;
            $data['empleado_id'] = $empleado->id;
            $data['profesor_clave'] = null;
            $data['director_id'] = null;
        } else {
            $profesor = Profesor::query()->findOrFail($data['profesor_clave']);
            $this->authorize('create', Incidencia::class);
            abort_unless(app(IncidenciaPolicy::class)->createForProfessor(auth()->user(), $profesor), 403);
            $data['numero_empleado'] = $profesor->clave_profesor;
            $data['area_id'] = $profesor->area_id;
            $data['puesto_id'] = $profesor->puesto_id;
            $data['empleado_id'] = null;
            $data['director_id'] = $profesor->director_id;
        }

        $area = $data['area_id'] ? Area::query()->find($data['area_id']) : null;
        $fechaFaltaProgramada = $data['fecha_falta_programada'] ?? $data['fecha_justificacion'];
        $tipoDuracion = $data['tipo_duracion'] ?? 'dia_completo';

        $incidencia = Incidencia::create([
            'asunto' => $data['asunto'],
            'tipo_justificacion' => $data['tipo_justificacion'],
            'fecha_justificacion' => $fechaFaltaProgramada,
            'fecha_falta_programada' => $fechaFaltaProgramada,
            'tipo_duracion' => $tipoDuracion,
            'hora_inicio' => $tipoDuracion === 'horario' ? ($data['hora_inicio'] ?? null) : null,
            'hora_fin' => $tipoDuracion === 'horario' ? ($data['hora_fin'] ?? null) : null,
            'empleado_id' => $data['empleado_id'],
            'profesor_clave' => $data['profesor_clave'],
            'area_id' => $data['area_id'],
            'puesto_id' => $data['puesto_id'],
            'director_id' => $data['director_id'] ?? null,
            'numero_empleado' => $data['numero_empleado'] ?? null,
            'motivo' => $data['motivo'],
            'comentarios' => $data['comentarios'] ?? null,
            'solicitud' => $data['solicitud'] ?? null,
            'estado' => 'pendiente',
            'responsable_area_id' => $area?->empleado_responsable_id,
            'created_by_user_id' => auth()->id(),
        ]);

        app(IncidentApprovalService::class)->generateRoute($incidencia);
        $this->notifyPendingApprover($incidencia);

        return redirect()->route('incidencias.index')->with('success', 'Incidencia registrada correctamente.');
    }

    public function updateStatus(Request $request, Incidencia $incidencia): RedirectResponse
    {
        $this->authorize('approve', $incidencia);

        $data = $request->validate([
            'estado' => ['required', 'in:pendiente,aprobada,rechazada'],
        ]);

        $pendingApproval = $incidencia->approvals()
            ->where('status', 'pending')
            ->orderBy('sequence')
            ->first();

        $oldState = $incidencia->estado;

        if ($pendingApproval) {
            $pendingApproval->update([
                'status' => $data['estado'] === 'aprobada' ? 'approved' : 'rejected',
                'approved_at' => $data['estado'] === 'aprobada' ? now() : null,
                'rejected_at' => $data['estado'] === 'rechazada' ? now() : null,
                'approver_user_id' => auth()->id(),
                'approver_employee_id' => auth()->user()?->employee?->id,
            ]);
        }

        $incidencia->update([
            'estado' => $data['estado'],
            'autorizado_por_user_id' => auth()->id(),
            'autorizado_at' => now(),
        ]);

        app(AuditService::class)->log(
            auth()->user(),
            'status_changed',
            $incidencia,
            'incidencias',
            "Cambio de estado de incidencia: {$oldState} -> {$data['estado']}",
            ['estado' => $oldState],
            ['estado' => $data['estado']]
        );

        $incidencia->loadMissing('creador');
        if ($incidencia->creador) {
            $incidencia->creador->notify(new IncidenciaStatusNotification(
                $incidencia,
                $data['estado'],
                "Tu incidencia '{$incidencia->asunto}' fue {$data['estado']}.",
            ));
        }

        return Redirect::route('incidencias.index')->with('success', 'Estado de incidencia actualizado correctamente.');
    }

    public function markViewed(Incidencia $incidencia): RedirectResponse
    {
        $this->authorize('markViewed', $incidencia);

        if (! $incidencia->visto_at) {
            $incidencia->update([
                'visto_at' => now(),
                'visto_por_user_id' => auth()->id(),
            ]);

            app(AuditService::class)->log(
                auth()->user(),
                'viewed',
                $incidencia,
                'incidencias',
                'La incidencia fue marcada como vista.',
            );

            $this->notifyAuthorizedUser($incidencia, 'viewed', "La incidencia '{$incidencia->asunto}' fue vista.");
        }

        return Redirect::route('incidencias.index')->with('success', 'Incidencia marcada como vista.');
    }

    public function sign(Incidencia $incidencia): RedirectResponse
    {
        $this->authorize('sign', $incidencia);

        if (! $incidencia->firmado_at) {
            $incidencia->update([
                'firmado_at' => now(),
                'firmado_por_user_id' => auth()->id(),
            ]);

            app(AuditService::class)->log(
                auth()->user(),
                'signed',
                $incidencia,
                'incidencias',
                'La incidencia fue firmada por el solicitante.',
            );

            $this->notifyAuthorizedUser($incidencia, 'signed', "La incidencia '{$incidencia->asunto}' fue firmada.");
        }

        return Redirect::route('incidencias.index')->with('success', 'Incidencia firmada correctamente.');
    }

    private function notifyPendingApprover(Incidencia $incidencia): void
    {
        $pendingApproval = app(IncidentApprovalService::class)->getPendingApprover($incidencia);
        $approverUser = $pendingApproval?->approverEmployee?->authUser;

        if ($approverUser) {
            $approverUser->notify(new IncidenciaStatusNotification(
                $incidencia,
                'pending_approval',
                "Tienes una incidencia pendiente de revisión: '{$incidencia->asunto}'.",
            ));
        }
    }

    private function notifyAuthorizedUser(Incidencia $incidencia, string $event, string $message): void
    {
        $incidencia->loadMissing('autorizadoPor');
        $authorizedUser = $incidencia->autorizadoPor;

        if ($authorizedUser && $authorizedUser->id !== auth()->id()) {
            $authorizedUser->notify(new IncidenciaStatusNotification($incidencia, $event, $message));
        }
    }
}
