<?php

declare(strict_types=1);

namespace App\Http\Controllers\Academia;

use App\Http\Controllers\Controller;
use App\Models\Academia\Ciclo;
use App\Models\Academia\Curso;
use App\Models\Academia\HorarioDet;
use App\Models\Academia\Materia;
use App\Models\Academia\Plan;
use App\Services\AcademiaDashboardService;
use App\Services\CicloActualService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected CicloActualService $cicloService,
        protected AcademiaDashboardService $dashboardService,
    ) {}

    public function index(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);
        $summary = $this->dashboardService->build($ciclo);
        $cycle = [$ciclo->inicial, $ciclo->final, $ciclo->periodo];

        $kpis = $summary['kpis'];
        $totales = $this->getTotales($ciclo);

        // Horarios por día (para gráfico)
        $horariosPorDia = HorarioDet::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->selectRaw('dia, COUNT(*) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->mapWithKeys(fn ($h) => [$h->dia => $h->total])
            ->toArray();

        // Distribución por origen horario
        $porOrigen = HorarioDet::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->selectRaw('origen_horario, COUNT(*) as total')
            ->groupBy('origen_horario')
            ->get()
            ->mapWithKeys(fn ($h) => [$h->origen_horario ?: 'SIN_DEFINIR' => $h->total])
            ->toArray();

        // Materias vinculadas al ciclo (vía horarios_det o cursos)
        $materiaKeys = HorarioDet::porCiclo(...$cycle)
            ->activo()
            ->select('clave_asignatura')
            ->distinct()
            ->pluck('clave_asignatura');

        $cursoMateriaKeys = Curso::porCiclo(...$cycle)
            ->activo()
            ->select('clave_asignatura')
            ->distinct()
            ->pluck('clave_asignatura');

        $allMateriaKeys = $materiaKeys->merge($cursoMateriaKeys)->unique()->filter();

        $materiasQuery = Materia::query()->with(['plan.nivelRel']);
        if ($allMateriaKeys->isNotEmpty()) {
            $materiasQuery->whereIn('clave_asignatura', $allMateriaKeys);
        } else {
            $materiasQuery->whereRaw('1=0');
        }
        $materias = $materiasQuery->orderBy('nombre_asignatura')->get();

        // Planes vinculados al ciclo (vía cursos o materias del ciclo)
        $planIds = Curso::porCiclo(...$cycle)
            ->activo()
            ->whereNotNull('id_plan')
            ->select('id_plan')
            ->distinct()
            ->pluck('id_plan');

        if ($materias->isNotEmpty()) {
            $planIdsFromMaterias = $materias->pluck('id_plan')->filter()->unique();
            $planIds = $planIds->merge($planIdsFromMaterias)->unique();
        }

        $planesQuery = Plan::query()->with('nivelRel')->withCount('materias');
        if ($planIds->isNotEmpty()) {
            $planesQuery->whereIn('id_plan', $planIds);
        } else {
            $planesQuery->whereRaw('1=0');
        }
        $planes = $planesQuery->orderBy('nombre_plan')->get();

        // ==================== CICLOS DISPONIBLES ====================
        $ciclosDisponibles = \App\Models\Academia\Ciclo::query()
            ->orderByDesc('inicial')
            ->orderByDesc('final')
            ->orderByDesc('periodo')
            ->get();

        // ==================== ASIGNACIONES ====================
        // Obtiene las asignaciones de horarios para el ciclo actual, con relaciones necesarias.
        $asignaciones = \App\Models\Academia\HorarioDet::porCiclo(...$cycle)
            ->activo()
            ->with(['grupo', 'materia'])
            ->orderBy('codigo_grupo')
            ->paginate(20);

        $sedesMap = $summary['sedesMap'] ?? \App\Models\Academia\Sede::pluck('descripcion', 'id_campus')->toArray();

        // Pasar datos adicionales al view
        return view('academia.dashboard.index', [
            'ciclo' => $ciclo,
            'ciclosDisponibles' => $ciclosDisponibles,
            'kpis' => $kpis,
            'totales' => $totales,
            'dashboardSummary' => $summary,
            'horariosPorDia' => $horariosPorDia,
            'porOrigen' => $porOrigen,
            'materias' => $materias,
            'planes' => $planes,
            'asignaciones' => $asignaciones,
            'sedesMap' => $sedesMap,
        ]);
    }

    /** GET /academia/kpis-json?ciclo=label */
    public function kpisJson(Request $request): \Illuminate\Http\JsonResponse
    {
        $ciclo = $this->cicloService->resolve($request);
        $summary = $this->dashboardService->build($ciclo);

        return response()->json([
            'kpis' => $summary['kpis'],
            'totales' => $summary['dataQuality'] ? [] : $this->getTotales($ciclo),
            'ciclo' => $ciclo->label,
        ]);
    }

    private function getTotales(Ciclo $ciclo): array
    {
        $cycle = [$ciclo->inicial, $ciclo->final, $ciclo->periodo];
        $groups = \App\Models\Academia\Grupo::porCiclo(...$cycle)->activo()->count();
        $enrollments = \App\Models\Academia\AlumnoGrupo::query()
            ->where('inicial', $ciclo->inicial)
            ->where('final', $ciclo->final)
            ->where('periodo', $ciclo->periodo)
            ->distinct()
            ->count('numero_alumno');
        $courses = \App\Models\Academia\Curso::porCiclo(...$cycle)->activo()->count();

        return [
            'grupos' => $groups,
            'alumnos' => $enrollments,
            'profesores' => (clone \App\Models\Academia\HorarioDet::porCiclo(...$cycle)->activo())->distinct()->count('clave_profesor'),
            'horarios' => (clone \App\Models\Academia\HorarioDet::porCiclo(...$cycle)->activo())->distinct()->count('codigo_grupo'),
            'cursos' => $courses,
            'materias' => (clone \App\Models\Academia\Curso::porCiclo(...$cycle)->activo())->distinct()->count('clave_asignatura'),
            'planes' => (clone \App\Models\Academia\Curso::porCiclo(...$cycle)->activo())->distinct()->count('id_plan'),
        ];
    }
}
