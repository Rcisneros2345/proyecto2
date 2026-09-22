<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Academia\Ciclo;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use App\Models\HorarioLaboral;
use App\Services\AttendanceObservationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attendance::with(['employee.area', 'employee.puesto', 'employee.sede', 'device']);

        $hasFilters = collect(['device_id', 'type', 'from', 'to'])
            ->map(fn ($key) => $request->query($key))
            ->contains(fn ($value) => $value !== null && $value !== '');

        if ($hasFilters) {
            $this->applyFilters($query, $request);
        }

        $rawAttendances = $query
            ->orderBy('recorded_at')
            ->get();

        $employeesByUserId = Employee::query()
            ->with(['area', 'puesto', 'sede'])
            ->whereIn('user_id', $rawAttendances->pluck('user_id')->filter()->unique())
            ->get()
            ->keyBy(fn (Employee $employee) => (string) $employee->user_id);

        $rawAttendances->each(function (Attendance $attendance) use ($employeesByUserId): void {
            if (! $attendance->relationLoaded('employee') || ! $attendance->employee) {
                $employee = $employeesByUserId->get((string) $attendance->user_id);
                if ($employee) {
                    $attendance->setRelation('employee', $employee);
                }
            }
        });

        // Precargar horarios laborales para los empleados en el rango
        $employeeIds = $rawAttendances->pluck('employee_id')->filter()->unique();
        $horariosMap = HorarioLaboral::whereIn('employee_id', $employeeIds)
            ->where('activo', true)
            ->get()
            ->groupBy('employee_id');

        $dailyRows = $rawAttendances
            ->groupBy(fn (Attendance $attendance) => ($attendance->employee?->id ?? 'user-'.$attendance->user_id).':'.$attendance->recorded_at->toDateString())
            ->map(function ($records) use ($horariosMap) {
                $first = $records->first();
                $punches = $records->groupBy(fn (Attendance $attendance) => $attendance->punchStatus())
                    ->map(fn ($items) => $items->sortBy('recorded_at')->first());

                // Obtener horario laboral del empleado para este día
                $diaSemana = $first->recorded_at->dayOfWeekIso;
                $employeeId = $first->employee_id;
                $horarios = $horariosMap->get($employeeId, collect());
                $horario = $horarios->firstWhere('dia_semana', $diaSemana);
                $horaEntradaBase = $horario?->hora_entrada?->format('H:i') ?? '08:00';
                $horaSalidaBase = $horario?->hora_salida?->format('H:i') ?? '17:00';
                $entrada = $punches->get(0);
                $salida = $punches->get(1);

                return (object) [
                    'date' => $first->recorded_at->toDateString(),
                    'id' => $first->id,
                    'employee' => $first->employee,
                    'user_id' => $first->user_id,
                    'device_names' => $records->map(fn ($item) => $item->device?->name)->filter()->unique()->values(),
                    'punches' => $punches,
                    'latest_observation' => $records->map(fn ($item) => $item->observations()->latest()->first())->filter()->sortByDesc('created_at')->first(),
                    'observation_attendance_id' => $records->sortByDesc('recorded_at')->first()?->id,
                    'llegada_resumen' => $this->buildPunchSummary($punches, [0 => 'Entrada', 4 => 'Extra entrada']),
                    'salida_resumen' => $this->buildPunchSummary($punches, [1 => 'Salida', 5 => 'Extra salida']),
                    'observacion_llegada' => $this->buildDeviationLabel($entrada?->recorded_at, $horaEntradaBase, 'llegó'),
                    'observacion_salida' => $this->buildDeviationLabel($salida?->recorded_at, $horaSalidaBase, 'salió'),
                    'horario_entrada_base' => $horaEntradaBase,
                    'horario_salida_base' => $horaSalidaBase,
                    'tiene_horario' => $horario !== null,
                ];
            })
            ->sortByDesc(fn ($row) => $row->date.' '.($row->employee?->name ?? $row->user_id));

        $page = LengthAwarePaginator::resolveCurrentPage('employee_page');
        $perPage = min(max((int) $request->query('per_page', 25), 10), 100);
        $attendances = new LengthAwarePaginator(
            $dailyRows->forPage($page, $perPage)->values(),
            $dailyRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'employee_page', 'query' => $request->query()],
        );

        // --- Asistencia por clase ---
        $classStatus = $request->has('class_status')
            ? (string) $request->query('class_status')
            : 'PRESENTE';
        $request->merge(['class_status' => $classStatus]);
        $classAttendances = DB::table('docentes_asistencias as da')
            ->leftJoin('profesores as p', 'p.clave_profesor', '=', 'da.clave_profesor')
            ->leftJoin('grupos as g', function ($join) {
                $join->on('g.codigo_grupo', '=', 'da.codigo_grupo')
                    ->on('g.inicial', '=', 'da.inicial')
                    ->on('g.final', '=', 'da.final')
                    ->on('g.periodo', '=', 'da.periodo');
            })
            ->leftJoin('materias as m', 'm.clave_asignatura', '=', 'da.clave_asignatura')
            ->leftJoin('sedes as s', 's.id_campus', '=', 'g.id_campus')
            ->leftJoin('horarios_det as h', function ($join) {
                $join->on('h.codigo_grupo', '=', 'da.codigo_grupo')
                    ->on('h.inicial', '=', 'da.inicial')
                    ->on('h.final', '=', 'da.final')
                    ->on('h.periodo', '=', 'da.periodo')
                    ->on('h.clave_profesor', '=', 'da.clave_profesor')
                    ->on('h.clave_asignatura', '=', 'da.clave_asignatura')
                    ->on('h.dia', '=', 'da.dia')
                    ->on('h.sesion', '=', 'da.sesion');
            })
            ->leftJoin('sesiones_base as sb', function ($join) {
                $join->on('sb.sesion', '=', 'da.sesion')
                    ->on('sb.nivel', '=', 'g.nivel')
                    ->on('sb.turno', '=', 'g.turno');
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('da.fecha', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('da.fecha', '<=', $request->query('to')))
            ->when($request->filled('class_from'), fn ($q) => $q->whereDate('da.fecha', '>=', $request->query('class_from')))
            ->when($request->filled('class_to'), fn ($q) => $q->whereDate('da.fecha', '<=', $request->query('class_to')))
            ->when($request->filled('class_status') && $request->query('class_status') !== 'SIN_ASIGNAR', fn ($q) => $q->where('da.estado', $request->query('class_status')))
            ->when($request->filled('class_profesor'), fn ($q) => $q->where('da.clave_profesor', $request->query('class_profesor')))
            ->when($request->filled('class_group'), fn ($q) => $q->where('da.codigo_grupo', $request->query('class_group')))
            ->when($request->filled('class_nivel'), fn ($q) => $q->where('g.nivel', $request->query('class_nivel')))
            ->when($request->filled('class_turno'), fn ($q) => $q->where('g.turno', $request->query('class_turno')))
            ->when($request->filled('class_cycle'), function ($q) use ($request): void {
                [$initial, $final, $period] = array_map('intval', explode('-', $request->query('class_cycle')));
                $q->where('da.inicial', $initial)->where('da.final', $final)->where('da.periodo', $period);
            })
            ->select([
                'da.*',
                'p.nombre_profesor', 'p.paterno as profesor_paterno', 'p.materno as profesor_materno',
                'g.grado', 'g.turno', 'g.nivel', 'g.id_campus',
                'g.carrera', 'h.edificio', 'h.aula', 'sb.hora_inicio', 'sb.hora_fin',
                'm.nombre_asignatura', 's.descripcion as sede_nombre',
                DB::raw('CASE WHEN h.id IS NOT NULL THEN 1 ELSE 0 END as has_horario'),
            ])
            ->orderByDesc('da.fecha')
            ->orderBy('da.sesion')
            ->paginate(25, ['*'], 'class_page')
            ->appends($request->query());

        $classWeekStart = $request->filled('class_from')
            ? Carbon::parse($request->query('class_from'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $classWeekEnd = $request->filled('class_to')
            ? Carbon::parse($request->query('class_to'))->endOfDay()
            : $classWeekStart->copy()->addDays(4)->endOfDay();
        $classWeekEnd = $classWeekEnd->min($classWeekStart->copy()->addDays(4)->endOfDay());
        $weekDates = collect(range(1, 5))->mapWithKeys(fn (int $day): array => [
            $day => $classWeekStart->copy()->addDays($day - 1),
        ]);
        $currentCycle = Ciclo::query()
            ->orderByDesc('inicial')
            ->orderByDesc('final')
            ->orderByDesc('periodo')
            ->first();
        if ($request->filled('class_cycle')) {
            [$initial, $final, $period] = array_map('intval', explode('-', $request->query('class_cycle')));
            $currentCycle = Ciclo::query()
                ->where('inicial', $initial)
                ->where('final', $final)
                ->where('periodo', $period)
                ->first() ?? $currentCycle;
        }
        if (! $request->filled('class_cycle') && $currentCycle && ! $this->cycleHasClassSchedule($currentCycle)) {
            $scheduledCycle = DB::table('horarios_det')
                ->where('activo', true)
                ->select(['inicial', 'final', 'periodo'])
                ->groupBy(['inicial', 'final', 'periodo'])
                ->orderByDesc('inicial')
                ->orderByDesc('final')
                ->orderByDesc('periodo')
                ->first();

            if ($scheduledCycle) {
                $currentCycle = Ciclo::query()
                    ->where('inicial', $scheduledCycle->inicial)
                    ->where('final', $scheduledCycle->final)
                    ->where('periodo', $scheduledCycle->periodo)
                    ->first() ?? $currentCycle;
            }
        }
        $classWeeklyGrid = $this->buildClassWeeklyGrid($currentCycle, $weekDates, $classWeekEnd, $request);
        $classWeeklyStats = $this->summarizeClassWeeklyGrid($classWeeklyGrid);

        return view('attendances.index', [
            'attendances' => $attendances,
            'devices' => Device::orderBy('name')->get(),
            'states' => Attendance::states(),
            'classAttendances' => $classAttendances,
            'classWeeklyGrid' => $classWeeklyGrid,
            'classWeekDates' => $weekDates,
            'classCurrentCycle' => $currentCycle,
            'classWeeklyStats' => $classWeeklyStats,
            'classStatus' => $classStatus,
            'classStatuses' => ['PRESENTE' => 'Presente', 'AUSENTE' => 'Ausente', 'RETARDO' => 'Retardo', 'JUSTIFICADO' => 'Justificado', 'SIN_ASIGNAR' => 'Sin asignar'],
            'classProfessors' => DB::table('horarios_det as h')
                ->leftJoin('profesores as p', 'p.clave_profesor', '=', 'h.clave_profesor')
                ->where('h.inicial', $currentCycle?->inicial)
                ->where('h.final', $currentCycle?->final)
                ->where('h.periodo', $currentCycle?->periodo)
                ->where('h.activo', true)
                ->whereNotNull('h.clave_profesor')
                ->select(['h.clave_profesor', 'p.nombre_profesor', 'p.paterno', 'p.materno'])
                ->distinct()
                ->orderBy('p.nombre_profesor')
                ->get(),
            'classGroups' => DB::table('horarios_det')
                ->where('inicial', $currentCycle?->inicial)
                ->where('final', $currentCycle?->final)
                ->where('periodo', $currentCycle?->periodo)
                ->where('activo', true)
                ->distinct()
                ->orderBy('codigo_grupo')
                ->pluck('codigo_grupo'),
            'classLevels' => DB::table('horarios_det as h')
                ->join('grupos as g', function ($join): void {
                    $join->on('g.codigo_grupo', '=', 'h.codigo_grupo')
                        ->on('g.inicial', '=', 'h.inicial')
                        ->on('g.final', '=', 'h.final')
                        ->on('g.periodo', '=', 'h.periodo');
                })
                ->leftJoin('niveles as n', 'n.nivel', '=', 'g.nivel')
                ->where('h.inicial', $currentCycle?->inicial)
                ->where('h.final', $currentCycle?->final)
                ->where('h.periodo', $currentCycle?->periodo)
                ->where('h.activo', true)
                ->distinct()
                ->orderBy('g.nivel')
                ->get(['g.nivel as value', 'n.descripcion as label']),
            'classTurns' => DB::table('horarios_det as h')
                ->join('grupos as g', function ($join): void {
                    $join->on('g.codigo_grupo', '=', 'h.codigo_grupo')
                        ->on('g.inicial', '=', 'h.inicial')
                        ->on('g.final', '=', 'h.final')
                        ->on('g.periodo', '=', 'h.periodo');
                })
                ->leftJoin('turnos as t', 't.turno', '=', 'g.turno')
                ->where('h.inicial', $currentCycle?->inicial)
                ->where('h.final', $currentCycle?->final)
                ->where('h.periodo', $currentCycle?->periodo)
                ->where('h.activo', true)
                ->distinct()
                ->orderBy('g.turno')
                ->get(['g.turno as value', 't.descripcion as label']),
            'classCycles' => Ciclo::query()->latest('inicial')->latest('final')->latest('periodo')->get(),
        ]);
    }

    private function buildClassWeeklyGrid(?Ciclo $cycle, $weekDates, Carbon $weekEnd, Request $request): array
    {
        if (! $cycle) {
            return [];
        }

        $weekStart = $weekDates->first();

        $schedule = DB::table('horarios_det as h')
            ->leftJoin('grupos as g', function ($join): void {
                $join->on('g.codigo_grupo', '=', 'h.codigo_grupo')
                    ->on('g.inicial', '=', 'h.inicial')
                    ->on('g.final', '=', 'h.final')
                    ->on('g.periodo', '=', 'h.periodo');
            })
            ->leftJoin('profesores as p', 'p.clave_profesor', '=', 'h.clave_profesor')
            ->leftJoin('materias as m', 'm.clave_asignatura', '=', 'h.clave_asignatura')
            ->leftJoin('sedes as s', 's.id_campus', '=', 'h.id_campus')
            ->leftJoin('sesiones_base as sb', function ($join): void {
                $join->on('sb.sesion', '=', 'h.sesion')
                    ->on('sb.nivel', '=', 'g.nivel')
                    ->on('sb.turno', '=', 'g.turno');
            })
            ->where('h.inicial', $cycle->inicial)
            ->where('h.final', $cycle->final)
            ->where('h.periodo', $cycle->periodo)
            ->whereBetween('h.dia', [1, 5])
            ->where('h.activo', true)
            ->when($request->filled('class_profesor'), fn ($q) => $q->where('h.clave_profesor', $request->query('class_profesor')))
            ->when($request->filled('class_group'), fn ($q) => $q->where('h.codigo_grupo', $request->query('class_group')))
            ->when($request->filled('class_nivel'), fn ($q) => $q->where('g.nivel', $request->query('class_nivel')))
            ->when($request->filled('class_turno'), fn ($q) => $q->where('g.turno', $request->query('class_turno')))
            ->orderBy('h.dia')
            ->orderBy('sb.orden')
            ->orderBy('h.sesion')
            ->get([
                'h.codigo_grupo', 'h.clave_profesor', 'h.clave_asignatura',
                'h.dia', 'h.sesion', 'h.edificio', 'h.aula', 'h.id_campus',
                'g.carrera', 'g.nivel', 'g.turno',
                'p.nombre_profesor', 'p.paterno as profesor_paterno', 'p.materno as profesor_materno',
                'm.nombre_asignatura', 's.descripcion as sede_nombre',
                'sb.hora_inicio', 'sb.hora_fin', 'sb.descripcion as sesion_descripcion',
            ]);

        $attendances = DB::table('docentes_asistencias')
            ->where('inicial', $cycle->inicial)
            ->where('final', $cycle->final)
            ->where('periodo', $cycle->periodo)
            ->whereBetween('fecha', [$weekStart->toDateString(), $weekEnd->copy()->addDays(2)->toDateString()])
            ->get()
            ->keyBy(fn (object $attendance): string => $this->classAttendanceKey($attendance));

        $rows = [];
        foreach ($schedule as $slot) {
            $rowKey = implode('|', [$slot->codigo_grupo, $slot->clave_profesor, $slot->clave_asignatura]);
            if (! isset($rows[$rowKey])) {
                $rows[$rowKey] = [
                    'codigo_grupo' => $slot->codigo_grupo,
                    'carrera' => $slot->carrera,
                    'materia' => $slot->nombre_asignatura ?: $slot->clave_asignatura,
                    'profesor' => trim(($slot->profesor_paterno ?? '').' '.($slot->profesor_materno ?? '').' '.($slot->nombre_profesor ?? '')) ?: $slot->clave_profesor,
                    'nivel' => $slot->nivel,
                    'turno' => $slot->turno,
                    'scheduled' => 0,
                    'captured' => 0,
                    'pending' => 0,
                    'days' => collect(range(1, 5))->mapWithKeys(fn (int $day): array => [$day => []])->all(),
                ];
            }

            $day = (int) $slot->dia;
            $date = $weekDates->get($day);
            $attendanceKey = implode('|', [
                $slot->codigo_grupo, $slot->clave_profesor, $slot->clave_asignatura,
                $slot->dia, $slot->sesion, $date->toDateString(),
            ]);
            $attendance = $attendances->get($attendanceKey);
            $statusFilter = $request->query('class_status');
            if ($statusFilter === 'SIN_ASIGNAR' && $attendance) {
                continue;
            }
            if ($statusFilter && $statusFilter !== 'SIN_ASIGNAR' && ($attendance?->estado !== $statusFilter)) {
                continue;
            }
            $rows[$rowKey]['scheduled']++;
            $rows[$rowKey][$attendance ? 'captured' : 'pending']++;
            $rows[$rowKey]['days'][$day][] = [
                'session' => $slot->sesion_descripcion ?: 'Sesión '.$slot->sesion,
                'time' => $slot->hora_inicio && $slot->hora_fin
                    ? Carbon::parse($slot->hora_inicio)->format('H:i').' - '.Carbon::parse($slot->hora_fin)->format('H:i')
                    : 'Horario no definido',
                'location' => trim(($slot->sede_nombre ?? '').' '.($slot->edificio ?? '').' '.($slot->aula ?? '')),
                'status' => $attendance?->estado,
                'observaciones' => $attendance?->observaciones,
            ];
        }

        return array_values(array_filter($rows, fn (array $row): bool => $row['scheduled'] > 0));
    }

    private function summarizeClassWeeklyGrid(array $grid): array
    {
        $stats = [
            'classes' => count($grid),
            'scheduled' => 0,
            'captured' => 0,
            'pending' => 0,
        ];

        foreach ($grid as $classRow) {
            $stats['scheduled'] += $classRow['scheduled'];
            $stats['captured'] += $classRow['captured'];
            $stats['pending'] += $classRow['pending'];
        }

        return $stats;
    }

    private function cycleHasClassSchedule(Ciclo $cycle): bool
    {
        return DB::table('horarios_det')
            ->where('inicial', $cycle->inicial)
            ->where('final', $cycle->final)
            ->where('periodo', $cycle->periodo)
            ->where('activo', true)
            ->exists();
    }

    private function classAttendanceKey(object $attendance): string
    {
        $effectiveDate = Carbon::parse($attendance->fecha)
            ->startOfWeek(Carbon::MONDAY)
            ->addDays(((int) $attendance->dia) - 1)
            ->toDateString();

        return implode('|', [
            $attendance->codigo_grupo,
            $attendance->clave_profesor,
            $attendance->clave_asignatura,
            $attendance->dia,
            $attendance->sesion,
            $effectiveDate,
        ]);
    }

    private function buildPunchSummary($punches, array $labels): array
    {
        $summary = [];
        foreach ($labels as $status => $label) {
            $punch = $punches->get($status);
            if ($punch) {
                $summary[] = [
                    'label' => $label,
                    'time' => $punch->recorded_at->format('H:i:s'),
                ];
            }
        }

        return $summary;
    }

    private function buildDeviationLabel(?\Carbon\Carbon $recordedAt, string $baseTime, string $verb): string
    {
        if (! $recordedAt) {
            return '—';
        }

        $base = \Carbon\Carbon::createFromFormat('H:i', $baseTime);
        $actual = \Carbon\Carbon::createFromTime(
            $recordedAt->hour,
            $recordedAt->minute,
            $recordedAt->second,
        );
        $minutes = $actual->diffInMinutes($base);

        if ($minutes === 0) {
            return 'A tiempo';
        }

        return $actual->greaterThan($base)
            ? "{$verb} tarde en {$minutes} min"
            : "{$verb} temprano en {$minutes} min";
    }

    public function storeObservation(Request $request, Attendance $attendance): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $employee = $attendance->employee ?? Employee::find($attendance->employee_id);
        $user = $request->user();

        if (! $employee || ! $user) {
            return back()->with('error', 'No se pudo registrar la observación en esta asistencia.');
        }

        AttendanceObservationService::record(
            $attendance,
            $employee,
            $user,
            $data['kind'],
            trim($data['message']),
            'ui'
        );

        return back()->with('success', 'Observación registrada correctamente.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Attendance::with(['employee', 'device'])->orderBy('recorded_at');
        $this->applyFilters($query, $request);

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Fecha y hora', 'Empleado', 'ID', 'Marcado', 'Tipo', 'Dispositivo']);
            $query->chunk(500, function ($attendances) use ($handle): void {
                foreach ($attendances as $attendance) {
                    fputcsv($handle, [
                        $attendance->recorded_at->format('Y-m-d H:i:s'),
                        $attendance->employee?->name ?? 'Sin asignar',
                        $attendance->user_id,
                        $attendance->stateLabel(),
                        $attendance->type,
                        $attendance->device?->name ?? '',
                    ]);
                }
            });
            fclose($handle);
        }, 'asistencias-'.now()->format('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportClassAttendances(Request $request): StreamedResponse
    {
        $rows = $this->buildClassExportRows($request);

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Fecha', 'Estado', 'Observaciones', 'Docente', 'Clave profesor',
                'Grupo', 'Materia', 'Día', 'Sesión', 'Horario', 'Ciclo', 'Nivel', 'Turno', 'Carrera',
            ]);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'asistencias-docentes-'.now()->format('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildClassExportRows(Request $request): array
    {
        $cycle = $this->resolveClassCycle($request);
        if (! $cycle) {
            return [];
        }

        $from = $request->filled('class_from')
            ? Carbon::parse($request->query('class_from'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $to = $request->filled('class_to')
            ? Carbon::parse($request->query('class_to'))->endOfDay()
            : $from->copy()->addDays(4)->endOfDay();
        $to = $to->min($from->copy()->addDays(4)->endOfDay());

        $schedule = DB::table('horarios_det as h')
            ->leftJoin('grupos as g', function ($join): void {
                $join->on('g.codigo_grupo', '=', 'h.codigo_grupo')
                    ->on('g.inicial', '=', 'h.inicial')
                    ->on('g.final', '=', 'h.final')
                    ->on('g.periodo', '=', 'h.periodo');
            })
            ->leftJoin('profesores as p', 'p.clave_profesor', '=', 'h.clave_profesor')
            ->leftJoin('materias as m', 'm.clave_asignatura', '=', 'h.clave_asignatura')
            ->leftJoin('sesiones_base as sb', function ($join): void {
                $join->on('sb.sesion', '=', 'h.sesion')
                    ->on('sb.nivel', '=', 'g.nivel')
                    ->on('sb.turno', '=', 'g.turno');
            })
            ->where('h.inicial', $cycle->inicial)
            ->where('h.final', $cycle->final)
            ->where('h.periodo', $cycle->periodo)
            ->whereBetween('h.dia', [1, 5])
            ->where('h.activo', true)
            ->when($request->filled('class_profesor'), fn ($q) => $q->where('h.clave_profesor', $request->query('class_profesor')))
            ->when($request->filled('class_group'), fn ($q) => $q->where('h.codigo_grupo', $request->query('class_group')))
            ->when($request->filled('class_nivel'), fn ($q) => $q->where('g.nivel', $request->query('class_nivel')))
            ->when($request->filled('class_turno'), fn ($q) => $q->where('g.turno', $request->query('class_turno')))
            ->get([
                'h.codigo_grupo', 'h.clave_profesor', 'h.clave_asignatura', 'h.dia', 'h.sesion',
                'g.nivel', 'g.turno', 'g.carrera',
                'p.nombre_profesor', 'p.paterno', 'p.materno',
                'm.nombre_asignatura', 'sb.hora_inicio', 'sb.hora_fin', 'sb.descripcion as sesion_descripcion',
            ]);

        $captured = DB::table('docentes_asistencias')
            ->where('inicial', $cycle->inicial)
            ->where('final', $cycle->final)
            ->where('periodo', $cycle->periodo)
            ->whereBetween('fecha', [$from->toDateString(), $to->copy()->addDays(2)->toDateString()])
            ->get()
            ->keyBy(fn (object $row): string => $this->classAttendanceKey($row));

        $rows = [];
        foreach ($schedule as $slot) {
            foreach (range(1, 5) as $day) {
                if ((int) $slot->dia !== $day) {
                    continue;
                }
                $date = $from->copy()->addDays($day - 1);
                $key = implode('|', [$slot->codigo_grupo, $slot->clave_profesor, $slot->clave_asignatura, $slot->dia, $slot->sesion, $date->toDateString()]);
                $attendance = $captured->get($key);
                $status = $attendance?->estado ?? 'SIN_ASISTENCIA';
                $requestedStatus = $request->query('class_status');
                if ($requestedStatus && $requestedStatus !== 'SIN_ASIGNAR' && $requestedStatus !== $status) {
                    continue;
                }
                if ($requestedStatus === 'SIN_ASIGNAR' && $attendance) {
                    continue;
                }
                $rows[] = [
                    $date->toDateString(),
                    $status === 'SIN_ASISTENCIA' ? 'Sin asistencia' : $status,
                    $attendance?->observaciones ?? '',
                    trim(($slot->paterno ?? '').' '.($slot->materno ?? '').' '.($slot->nombre_profesor ?? '')) ?: 'Sin docente',
                    $slot->clave_profesor,
                    $slot->codigo_grupo,
                    $slot->nombre_asignatura ?? $slot->clave_asignatura,
                    $day,
                    $slot->sesion_descripcion ?: 'Sesión '.$slot->sesion,
                    $slot->hora_inicio && $slot->hora_fin ? Carbon::parse($slot->hora_inicio)->format('H:i').' - '.Carbon::parse($slot->hora_fin)->format('H:i') : '',
                    "{$cycle->inicial}-{$cycle->final}-{$cycle->periodo}",
                    $slot->nivel ?? '',
                    $slot->turno ?? '',
                    $slot->carrera ?? '',
                ];
            }
        }

        return $rows;
    }

    private function resolveClassCycle(Request $request): ?Ciclo
    {
        if ($request->filled('class_cycle')) {
            [$initial, $final, $period] = array_map('intval', explode('-', $request->query('class_cycle')));

            return Ciclo::query()->where('inicial', $initial)->where('final', $final)->where('periodo', $period)->first();
        }

        return Ciclo::query()->orderByDesc('inicial')->orderByDesc('final')->orderByDesc('periodo')->first();
    }

    public function print(Request $request): View
    {
        $query = Attendance::with(['employee', 'device'])->orderBy('recorded_at');
        $this->applyFilters($query, $request);

        return view('attendances.print', ['attendances' => $query->get()]);
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        if ($deviceId = $request->query('device_id')) {
            $query->where('device_id', $deviceId);
        }
        if (($type = $request->query('type')) !== null && $type !== '') {
            // El parámetro "type" del formulario selecciona el MODO de
            // checado, que en este firmware vive en la columna "type"
            // (state viene constante en 1 — ver Attendance::punchStatus()).
            $query->where('type', (int) $type);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('recorded_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('recorded_at', '<=', $to);
        }
    }

    private function applyClassFilters($query, Request $request): void
    {
        if ($request->filled('class_from')) {
            $query->whereDate('da.fecha', '>=', $request->query('class_from'));
        }
        if ($request->filled('class_to')) {
            $query->whereDate('da.fecha', '<=', $request->query('class_to'));
        }
        if ($request->filled('class_status') && $request->query('class_status') !== 'SIN_ASIGNAR') {
            $query->where('da.estado', $request->query('class_status'));
        }
        if ($request->filled('class_profesor')) {
            $query->where('da.clave_profesor', $request->query('class_profesor'));
        }
        if ($request->filled('class_group')) {
            $query->where('da.codigo_grupo', $request->query('class_group'));
        }
        if ($request->filled('class_nivel')) {
            $query->where('g.nivel', $request->query('class_nivel'));
        }
        if ($request->filled('class_turno')) {
            $query->where('g.turno', $request->query('class_turno'));
        }
        if ($request->filled('class_cycle')) {
            [$initial, $final, $period] = array_map('intval', explode('-', $request->query('class_cycle')));
            $query->where('da.inicial', $initial)->where('da.final', $final)->where('da.periodo', $period);
        }
    }
}
