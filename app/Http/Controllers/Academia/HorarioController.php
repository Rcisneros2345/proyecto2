<?php

declare(strict_types=1);

namespace App\Http\Controllers\Academia;

use App\Http\Controllers\Controller;
use App\Models\Academia\DocenteAsistencia;
use App\Models\Academia\HorarioDet;
use App\Models\Academia\Sede;
use App\Models\Academia\SesionBase;
use App\Services\AttendanceCaptureAuthorization;
use App\Services\CicloActualService;
use App\Services\HorarioResolver;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function __construct(
        protected CicloActualService $cicloService,
        protected HorarioResolver $horarioResolver,
        protected AttendanceCaptureAuthorization $captureAuthorization,
    ) {}

    public function clase(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);

        // Filtros
        $nivel = $request->get('nivel');
        $turno = $request->get('turno');
        $dia = $request->filled('dia')
            ? (int) $request->get('dia')
            : now()->dayOfWeekIso;
        $fecha = $request->filled('fecha')
            ? (string) $request->get('fecha')
            : now()->toDateString();
        $fechaCarbon = Carbon::parse($fecha);
        if ($fechaCarbon->dayOfWeekIso !== $dia) {
            $fecha = $fechaCarbon->startOfWeek(Carbon::MONDAY)
                ->addDays($dia - 1)
                ->toDateString();
        }
        $sede = $request->get('sede');
        $edificio = $request->get('edificio');

        $niveles = \App\Models\Academia\Nivel::activo()->get();
        $turnos = \App\Models\Academia\Turno::activo()->get();
        $sedes = Sede::activo()->orderBy('descripcion')->get();
        $edificios = collect();
        $scopeAllowed = true;

        if (! $request->user()->isAdmin()) {
            $assignments = $this->captureAuthorization->assignmentsForCycle(
                $request->user(),
                $ciclo->inicial,
                $ciclo->final,
                $ciclo->periodo,
            );
            $niveles = $niveles->filter(fn ($item) => $assignments->contains(fn ($assignment) => $assignment->nivel === null || $assignment->nivel === $item->nivel))->values();
            $sedes = $sedes->filter(fn ($item) => $assignments->contains(fn ($assignment) => $assignment->id_campus === null || (string) $assignment->id_campus === (string) $item->id_campus))->values();
            if ($sede === null && $sedes->count() === 1) {
                $sede = (string) $sedes->first()->id_campus;
            }
            $scopeAllowed = $nivel !== null && $this->captureAuthorization->canCaptureFilter(
                $request->user(),
                (string) $nivel,
                $sede !== null ? (string) $sede : null,
                $ciclo->inicial,
                $ciclo->final,
                $ciclo->periodo,
            );
        }

        if ($nivel && $turno && $scopeAllowed) {
            $edificios = HorarioDet::query()
                ->where('horarios_det.inicial', $ciclo->inicial)
                ->where('horarios_det.final', $ciclo->final)
                ->where('horarios_det.periodo', $ciclo->periodo)
                ->where('horarios_det.activo', true)
                ->whereNotNull('horarios_det.edificio')
                ->when($sede, function ($query) use ($sede): void {
                    $query->where(function ($scope) use ($sede): void {
                        $scope->where('horarios_det.id_campus', $sede)
                            ->orWhere(function ($fallback) use ($sede): void {
                                $fallback->whereNull('horarios_det.id_campus')
                                    ->where('g.id_campus', $sede);
                            });
                    });
                })
                ->join('grupos as g', function ($join) {
                    $join->on('horarios_det.codigo_grupo', '=', 'g.codigo_grupo')
                        ->on('horarios_det.inicial', '=', 'g.inicial')
                        ->on('horarios_det.final', '=', 'g.final')
                        ->on('horarios_det.periodo', '=', 'g.periodo');
                })
                ->where('g.nivel', $nivel)
                ->whereRaw('UPPER(g.turno) LIKE ?', [strtoupper(substr($turno, 0, 1)).'%'])
                ->distinct()
                ->orderBy('horarios_det.edificio')
                ->pluck('horarios_det.edificio');
        }

        $horarios = [];
        $stats = ['total_clases' => 0, 'capturadas' => 0, 'presentes' => 0, 'ausentes' => 0, 'retardos' => 0, 'justificados' => 0];

        if ($nivel && $turno && $scopeAllowed) {
            $horarios = $this->horarioResolver->getClaseAsistenciaGrid(
                $ciclo->inicial, $ciclo->final, $ciclo->periodo,
                $nivel, $turno, $dia, $fecha, $sede, $edificio
            );
            $horarios = $this->captureAuthorization->filterGrid(
                $request->user(),
                $horarios,
                $ciclo->inicial,
                $ciclo->final,
                $ciclo->periodo,
            );
            $stats = $this->horarioResolver->getClaseAsistenciaStats(
                $ciclo->inicial, $ciclo->final, $ciclo->periodo,
                $nivel, $turno, $dia, $fecha, $sede, $edificio
            );
            if (! $request->user()->isAdmin()) {
                $stats = $this->statsFromGrid($horarios);
            }
        }

        return view('academia.horarios.clase', [
            'ciclo' => $ciclo,
            'niveles' => $niveles,
            'turnos' => $turnos,
            'horarios' => $horarios,
            'stats' => $stats,
            'sedes' => $sedes,
            'edificios' => $edificios,
            'filtros' => compact('nivel', 'turno', 'dia', 'fecha', 'sede', 'edificio'),
            'diasSemana' => [
                1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
                4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
            ],
        ]);
    }

    public function guardarAsistencia(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inicial' => ['required', 'integer'],
            'final' => ['required', 'integer'],
            'periodo' => ['required', 'integer'],
            'horario_id' => ['required', 'integer'],
            'codigo_grupo' => ['required', 'string', 'max:50'],
            'clave_profesor' => ['required', 'string', 'max:50'],
            'clave_asignatura' => ['required', 'string', 'max:20'],
            'dia' => ['required', 'integer', 'between:1,7'],
            'sesion' => ['required', 'integer'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:PRESENTE,AUSENTE,RETARDO,JUSTIFICADO'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        $horario = HorarioDet::query()->findOrFail($data['horario_id'] ?? 0);
        abort_unless($this->captureAuthorization->canCaptureSchedule($request->user(), $horario), 403);

        DocenteAsistencia::updateOrCreate(
            collect($data)->only([
                'inicial', 'final', 'periodo', 'codigo_grupo', 'clave_profesor',
                'clave_asignatura', 'dia', 'sesion', 'fecha',
            ])->all(),
            ['estado' => $data['estado'], 'observaciones' => $data['observaciones'] ?? null],
        );

        return back()->with('success', 'Asistencia docente guardada correctamente.');
    }

    private function statsFromGrid(array $horarios): array
    {
        return [
            'total_clases' => count($horarios),
            'capturadas' => count(array_filter($horarios, fn (array $row): bool => filled($row['ASISTENCIA_ESTADO'] ?? null))),
            'presentes' => count(array_filter($horarios, fn (array $row): bool => ($row['ASISTENCIA_ESTADO'] ?? null) === 'PRESENTE')),
            'ausentes' => count(array_filter($horarios, fn (array $row): bool => ($row['ASISTENCIA_ESTADO'] ?? null) === 'AUSENTE')),
            'retardos' => count(array_filter($horarios, fn (array $row): bool => ($row['ASISTENCIA_ESTADO'] ?? null) === 'RETARDO')),
            'justificados' => count(array_filter($horarios, fn (array $row): bool => ($row['ASISTENCIA_ESTADO'] ?? null) === 'JUSTIFICADO')),
        ];
    }

    public function profesor(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);

        $profesores = \App\Models\Academia\Profesor::activo()
            ->whereHas('horarios', fn ($q) => $q->where('inicial', $ciclo->inicial)
                ->where('final', $ciclo->final)
                ->where('periodo', $ciclo->periodo))
            ->get();

        return view('academia.horarios.profesor', [
            'ciclo' => $ciclo,
            'profesores' => $profesores,
        ]);
    }

    public function aula(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);

        $conflictos = $this->horarioResolver->detectarConflictosAula(
            $ciclo->inicial, $ciclo->final, $ciclo->periodo
        );

        return view('academia.horarios.aula', [
            'ciclo' => $ciclo,
            'conflictos' => $conflictos,
        ]);
    }

    public function base(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);

        $sesiones = SesionBase::with(['nivelRel', 'turnoRel'])
            ->activo()
            ->orderBy('nivel')
            ->orderBy('turno')
            ->orderBy('sesion')
            ->get();

        return view('academia.horarios.base', [
            'ciclo' => $ciclo,
            'sesiones' => $sesiones,
        ]);
    }

    public function persona(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);

        $buscar = $request->get('buscar');
        $tipo = $request->get('tipo', 'profesor'); // profesor, alumno

        $resultados = [];

        if ($buscar) {
            if ($tipo === 'profesor') {
                $profesor = \App\Models\Academia\Profesor::where('clave_profesor', $buscar)
                    ->orWhere('nombre_profesor', 'like', "%{$buscar}%")
                    ->first();

                if ($profesor) {
                    $resultados = $this->horarioResolver->getHorarioProfesor(
                        $profesor->clave_profesor,
                        $ciclo->inicial, $ciclo->final, $ciclo->periodo
                    );
                }
            }
        }

        return view('academia.horarios.persona', [
            'ciclo' => $ciclo,
            'resultados' => $resultados,
            'buscar' => $buscar,
            'tipo' => $tipo,
        ]);
    }
}
