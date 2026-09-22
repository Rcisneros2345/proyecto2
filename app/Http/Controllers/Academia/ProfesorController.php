<?php

declare(strict_types=1);

namespace App\Http\Controllers\Academia;

use App\Http\Controllers\Controller;
use App\Models\Academia\Ciclo;
use App\Models\Academia\HorarioDet;
use App\Models\Academia\Profesor;
use App\Models\User;
use App\Services\CicloActualService;
use App\Services\PersonaContratosResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfesorController extends Controller
{
    public function __construct(
        protected CicloActualService $cicloService,
        protected PersonaContratosResolver $contratosResolver
    ) {}

    public function index(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);

        // Toggle: "todos" muestra catálogo completo, default muestra solo con horarios en ciclo
        $soloCiclo = $request->boolean('solo_ciclo', true);

        $query = Profesor::query();

        if ($soloCiclo) {
            $query->conHorariosEnCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('clave_profesor', 'like', "%{$buscar}%")
                    ->orWhere('nombre_profesor', 'like', "%{$buscar}%")
                    ->orWhere('paterno', 'like', "%{$buscar}%")
                    ->orWhere('materno', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_actual', $request->get('status'));
        }

        if ($request->filled('origen')) {
            $query->where('origen_horario', $request->get('origen'));
        }

        if ($request->filled('departamento')) {
            $query->where('departamento', $request->get('departamento'));
        }

        $profesores = $query->with(['contratoRel', 'sede'])
            ->orderBy('paterno')
            ->orderBy('materno')
            ->orderBy('nombre_profesor')
            ->paginate((int) $request->query('per_page', 25));

        return view('academia.profesores.index', [
            'ciclo' => $ciclo,
            'profesores' => $profesores,
            'soloCiclo' => $soloCiclo,
            'statusOptions' => ['A' => 'Activo', 'B' => 'Baja'],
            'origenOptions' => ['HD' => 'Hora Docente (PTC)', 'CA' => 'Carga Asignada (PA)'],
        ]);
    }

    public function show(Request $request, Profesor $profesor): View
    {
        $ciclo = $this->cicloService->resolve($request);

        $profesor->load(['contratoRel', 'sede']);

        // Contratos unificados
        $contratos = $this->contratosResolver->getContratosCompletos(
            $profesor->clave_profesor,
            $ciclo
        );

        // Horarios del ciclo
        $horarios = HorarioDet::with(['grupo', 'materia', 'sede'])
            ->where('clave_profesor', $profesor->clave_profesor)
            ->where('inicial', $ciclo->inicial)
            ->where('final', $ciclo->final)
            ->where('periodo', $ciclo->periodo)
            ->where('activo', true)
            ->orderBy('dia')
            ->orderBy('sesion')
            ->get()
            ->groupBy('dia');

        // Estadísticas
        $stats = [
            'total_clases' => HorarioDet::where('clave_profesor', $profesor->clave_profesor)
                ->where('inicial', $ciclo->inicial)
                ->where('final', $ciclo->final)
                ->where('periodo', $ciclo->periodo)
                ->where('activo', true)
                ->count(),
            'ptc' => HorarioDet::where('clave_profesor', $profesor->clave_profesor)
                ->where('inicial', $ciclo->inicial)
                ->where('final', $ciclo->final)
                ->where('periodo', $ciclo->periodo)
                ->where('origen_horario', 'HD')
                ->where('activo', true)
                ->count(),
            'pa' => HorarioDet::where('clave_profesor', $profesor->clave_profesor)
                ->where('inicial', $ciclo->inicial)
                ->where('final', $ciclo->final)
                ->where('periodo', $ciclo->periodo)
                ->where('origen_horario', 'CA')
                ->where('activo', true)
                ->count(),
        ];

        return view('academia.profesores.show', [
            'ciclo' => $ciclo,
            'profesor' => $profesor,
            'contratos' => $contratos,
            'horarios' => $horarios,
            'stats' => $stats,
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function assignUser(Request $request, Profesor $profesor)
    {
        $data = $request->validate([
            'auth_user_id' => ['nullable', 'exists:users,id'],
        ]);

        // Si se asigna un usuario, validar que no esté asignado a un empleado
        if ($data['auth_user_id'] !== null && $data['auth_user_id'] !== '') {
            $user = User::findOrFail($data['auth_user_id']);
            $assignedEmployee = $user->employee()->first();

            if ($assignedEmployee) {
                return redirect()->route('academia.profesores.show', $profesor)
                    ->with('error', "El usuario '{$user->name}' ya está asignado como empleado. Un usuario solo puede ser empleado OU profesor, no ambos.");
            }

            // Actualizar el tipo de usuario a 'professor'
            $user->update(['type' => 'professor']);
        } else {
            // Si se quita la asignación, limpiar el tipo solo si no hay otro rol
            if ($profesor->auth_user_id) {
                $oldUser = User::find($profesor->auth_user_id);
                if ($oldUser && ! $oldUser->employee()->exists()) {
                    $oldUser->update(['type' => null]);
                }
            }
        }

        $profesor->update(['auth_user_id' => $data['auth_user_id'] ?? null]);

        return redirect()->route('academia.profesores.show', $profesor)
            ->with('success', 'Usuario de acceso del profesor actualizado.');
    }

    public function horario(Request $request, Profesor $profesor): View
    {
        $ciclo = $this->cicloService->resolve($request);

        $horarios = HorarioDet::with(['grupo', 'materia', 'sede'])
            ->where('clave_profesor', $profesor->clave_profesor)
            ->where('inicial', $ciclo->inicial)
            ->where('final', $ciclo->final)
            ->where('periodo', $ciclo->periodo)
            ->where('activo', true)
            ->orderBy('dia')
            ->orderBy('sesion')
            ->get();

        $grid = [];
        $dias = range(1, 7);
        $sesiones = range(1, 12); // máximo 12 sesiones

        foreach ($dias as $dia) {
            foreach ($sesiones as $sesion) {
                $key = "$dia-$sesion";
                $grid[$key] = [
                    'dia' => $dia,
                    'sesion' => $sesion,
                    'clases' => [],
                ];
            }
        }

        foreach ($horarios as $h) {
            $key = "{$h->dia}-{$h->sesion}";
            if (isset($grid[$key])) {
                $grid[$key]['clases'][] = [
                    'grupo' => $h->grupo?->codigo_grupo,
                    'materia' => $h->materia?->label,
                    'aula' => $h->ubicacion,
                    'tipo' => $h->tipoClase,
                ];
            }
        }

        return view('academia.profesores.horario', [
            'ciclo' => $ciclo,
            'profesor' => $profesor,
            'grid' => $grid,
            'dias' => $dias,
            'sesiones' => $sesiones,
        ]);
    }
}
