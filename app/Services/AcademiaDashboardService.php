<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Academia\AlumnoGrupo;
use App\Models\Academia\Ciclo;
use App\Models\Academia\Curso;
use App\Models\Academia\Grupo;
use App\Models\Academia\HorarioDet;
use App\Models\Academia\Materia;
use App\Models\Academia\Sede;
use Illuminate\Support\Collection;

class AcademiaDashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function build(Ciclo $ciclo): array
    {
        $cycle = [$ciclo->inicial, $ciclo->final, $ciclo->periodo];
        $groups = Grupo::porCiclo(...$cycle)->activo();
        $enrollments = AlumnoGrupo::query()
            ->where('inicial', $ciclo->inicial)
            ->where('final', $ciclo->final)
            ->where('periodo', $ciclo->periodo);
        $courses = Curso::porCiclo(...$cycle)->activo();
        $schedules = HorarioDet::porCiclo(...$cycle)->activo();

        $materiaKeys = (clone $schedules)->distinct()->pluck('clave_asignatura');
        $cursoMateriaKeys = (clone $courses)->distinct()->pluck('clave_asignatura');
        $allMateriaKeys = $materiaKeys->merge($cursoMateriaKeys)->unique()->filter();

        $planIds = (clone $courses)->whereNotNull('id_plan')->distinct()->pluck('id_plan');
        if ($allMateriaKeys->isNotEmpty()) {
            $planIdsFromMaterias = Materia::whereIn('clave_asignatura', $allMateriaKeys)
                ->whereNotNull('id_plan')
                ->distinct()
                ->pluck('id_plan');
            $planIds = $planIds->merge($planIdsFromMaterias)->unique()->filter();
        }

        $turnosCollection = $this->turnosNormalizados($ciclo);
        $sedesMap = Sede::query()->pluck('descripcion', 'id_campus')->toArray();

        $kpis = [
            'alumnos' => (clone $enrollments)->distinct()->count('numero_alumno'),
            'grupos' => (clone $groups)->count(),
            'profesores' => (clone $schedules)->distinct()->count('clave_profesor'),
            'horarios' => (clone $schedules)->distinct()->count('codigo_grupo'),
            'cursos' => (clone $courses)->count(),
            'planes' => $planIds->count() ?: (clone $courses)->distinct()->count('id_plan'),
            'materias' => $allMateriaKeys->count() ?: (clone $courses)->distinct()->count('clave_asignatura'),
            'niveles' => (clone $groups)->distinct()->count('nivel'),
            'turnos' => $turnosCollection->count(),
        ];

        return [
            'kpis' => $kpis,
            'sedesMap' => $sedesMap,
            'alumnosPorGrupo' => $this->studentsByGroup($ciclo),
            'cursosPorSede' => $this->coursesByCampus($ciclo),
            'profesoresPorOrigen' => $this->professorsByOrigin($ciclo),
            'horasPorOrigen' => $this->hoursByOrigin($ciclo),
            'cursosPorOrigen' => $this->coursesByOrigin($ciclo),
            'niveles' => $this->groupsByColumn($ciclo, 'nivel'),
            'turnos' => $turnosCollection,
            'dataQuality' => [
                'hoursCaptured' => (clone $schedules)->where(function ($query): void {
                    $query->where('horas_semanales', '>', 0)
                        ->orWhere('horas_teoria_practica', '>', 0);
                })->exists(),
            ],
        ];
    }

    private function studentsByGroup(Ciclo $ciclo): Collection
    {
        $sedesMap = Sede::query()->pluck('descripcion', 'id_campus')->toArray();

        $rows = AlumnoGrupo::query()
            ->join('grupos', function ($join) use ($ciclo): void {
                $join->on('grupos.codigo_grupo', '=', 'alumnos_grupos.codigo_grupo')
                    ->where('grupos.inicial', $ciclo->inicial)
                    ->where('grupos.final', $ciclo->final)
                    ->where('grupos.periodo', $ciclo->periodo)
                    ->where('grupos.activo', true);
            })
            ->where('alumnos_grupos.inicial', $ciclo->inicial)
            ->where('alumnos_grupos.final', $ciclo->final)
            ->where('alumnos_grupos.periodo', $ciclo->periodo)
            ->select('grupos.codigo_grupo', 'grupos.grado', 'grupos.tipo_grupo', 'grupos.id_campus')
            ->selectRaw('COUNT(DISTINCT alumnos_grupos.numero_alumno) AS alumnos')
            ->groupBy('grupos.codigo_grupo', 'grupos.grado', 'grupos.tipo_grupo', 'grupos.id_campus')
            ->orderBy('grupos.grado')
            ->get();

        $aggregated = [];
        foreach ($rows as $r) {
            $partes = explode('-', strtoupper(trim((string) $r->codigo_grupo)));
            $mod = $partes[2] ?? ($r->tipo_grupo ?: 'TR');
            $sedeId = (int) ($partes[1] ?? ($r->id_campus ?: 1));
            $is3C = isset($partes[4]) && $partes[4] === '3C';

            $modName = match ($mod) {
                'I' => 'INTENSIVO',
                'B' => 'BIS',
                'D' => 'DESPRESURIZADO',
                'M' => 'MIXTO',
                default => 'TRADICIONAL',
            };

            $sedeName = $sedesMap[$sedeId] ?? ($sedeId ? "Sede {$sedeId}" : 'Campus Principal');

            $key = "{$r->grado}-{$mod}-{$sedeId}-".($is3C ? '3C' : 'STD');
            if (! isset($aggregated[$key])) {
                $aggregated[$key] = (object) [
                    'grado' => $r->grado,
                    'tipo_grupo' => $mod,
                    'modalidad_corta' => $mod,
                    'modalidad' => $modName,
                    'id_campus' => $sedeId,
                    'sede' => $sedeName,
                    'es_tercer_ciclo' => $is3C,
                    'alumnos' => 0,
                    'grupos_count' => 0,
                ];
            }
            $aggregated[$key]->alumnos += (int) $r->alumnos;
            $aggregated[$key]->grupos_count += 1;
        }

        return collect(array_values($aggregated))->sortBy([
            ['grado', 'asc'],
            ['modalidad', 'asc'],
            ['id_campus', 'asc'],
        ])->values();
    }

    private function coursesByCampus(Ciclo $ciclo): Collection
    {
        $sedesMap = Sede::query()->pluck('descripcion', 'id_campus')->toArray();

        return Curso::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->select('id_campus')
            ->selectRaw('COUNT(*) AS cursos')
            ->selectRaw('COUNT(DISTINCT id_plan) AS planes')
            ->selectRaw('COUNT(DISTINCT clave_asignatura) AS materias')
            ->groupBy('id_campus')
            ->orderBy('id_campus')
            ->get()
            ->map(function ($c) use ($sedesMap) {
                $c->sede_nombre = $sedesMap[$c->id_campus] ?? ($c->id_campus ? "Sede {$c->id_campus}" : 'Campus Principal');

                return $c;
            });
    }

    private function professorsByOrigin(Ciclo $ciclo): Collection
    {
        return HorarioDet::query()
            ->join('profesores', 'profesores.clave_profesor', '=', 'horarios_det.clave_profesor')
            ->porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->selectRaw("COALESCE(profesores.origen_horario, 'SIN_DEFINIR') AS origen")
            ->selectRaw('COUNT(DISTINCT profesores.clave_profesor) AS profesores')
            ->groupBy('profesores.origen_horario')
            ->get();
    }

    private function hoursByOrigin(Ciclo $ciclo): Collection
    {
        return HorarioDet::query()
            ->join('profesores', 'profesores.clave_profesor', '=', 'horarios_det.clave_profesor')
            ->porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->selectRaw("COALESCE(profesores.origen_horario, 'SIN_DEFINIR') AS origen")
            ->selectRaw('COUNT(*) AS clases')
            ->selectRaw('SUM(COALESCE(NULLIF(horarios_det.horas_semanales, 0), horarios_det.horas_teoria_practica, 0)) AS horas')
            ->groupBy('profesores.origen_horario')
            ->get();
    }

    private function coursesByOrigin(Ciclo $ciclo): Collection
    {
        $sedesMap = Sede::query()->pluck('descripcion', 'id_campus')->toArray();

        return Curso::query()
            ->leftJoin('profesores', 'profesores.clave_profesor', '=', 'cursos.clave_profesor')
            ->porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->select('cursos.clave_curso', 'cursos.nombre_curso', 'cursos.id_campus')
            ->selectRaw("COALESCE(profesores.origen_horario, 'SIN_DEFINIR') AS origen")
            ->selectRaw('COALESCE(cursos.sesiones, 0) AS sesiones')
            ->orderBy('cursos.clave_curso')
            ->get()
            ->map(function ($c) use ($sedesMap) {
                $c->sede_nombre = $sedesMap[$c->id_campus] ?? ($c->id_campus ? "Sede {$c->id_campus}" : 'Campus Principal');

                return $c;
            });
    }

    private function turnosNormalizados(Ciclo $ciclo): Collection
    {
        return Grupo::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->whereNotNull('turno')
            ->selectRaw("CASE 
                WHEN UPPER(turno) LIKE 'M%' THEN 'MATUTINO' 
                WHEN UPPER(turno) LIKE 'V%' THEN 'VESPERTINO' 
                ELSE 'OTRO' 
            END AS turno")
            ->selectRaw('COUNT(*) AS grupos')
            ->groupBy('turno')
            ->orderBy('turno')
            ->get();
    }

    private function groupsByColumn(Ciclo $ciclo, string $column): Collection
    {
        return Grupo::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->select($column)
            ->selectRaw('COUNT(*) AS grupos')
            ->groupBy($column)
            ->orderBy($column)
            ->get();
    }
}
