<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Academia\AlumnoGrupo;
use App\Models\Academia\Ciclo;
use App\Models\Academia\Curso;
use App\Models\Academia\Grupo;
use App\Models\Academia\HorarioDet;
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

        $kpis = [
            'alumnos' => (clone $enrollments)->distinct()->count('numero_alumno'),
            'grupos' => (clone $groups)->count(),
            'profesores' => (clone $schedules)->distinct()->count('clave_profesor'),
            'horarios' => (clone $schedules)->distinct()->count('codigo_grupo'),
            'cursos' => (clone $courses)->count(),
            'planes' => (clone $courses)->distinct()->count('id_plan'),
            'materias' => (clone $courses)->distinct()->count('clave_asignatura'),
            'niveles' => (clone $groups)->distinct()->count('nivel'),
            'turnos' => (clone $groups)->distinct()->count('turno'),
        ];

        return [
            'kpis' => $kpis,
            'alumnosPorGrupo' => $this->studentsByGroup($ciclo),
            'cursosPorSede' => $this->coursesByCampus($ciclo),
            'profesoresPorOrigen' => $this->professorsByOrigin($ciclo),
            'horasPorOrigen' => $this->hoursByOrigin($ciclo),
            'cursosPorOrigen' => $this->coursesByOrigin($ciclo),
            'niveles' => $this->groupsByColumn($ciclo, 'nivel'),
            'turnos' => $this->groupsByColumn($ciclo, 'turno'),
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
        return AlumnoGrupo::query()
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
            ->select('grupos.grado', 'grupos.tipo_grupo', 'grupos.id_campus')
            ->selectRaw('COUNT(DISTINCT alumnos_grupos.numero_alumno) AS alumnos')
            ->groupBy('grupos.grado', 'grupos.tipo_grupo', 'grupos.id_campus')
            ->orderBy('grupos.grado')
            ->orderBy('grupos.tipo_grupo')
            ->get();
    }

    private function coursesByCampus(Ciclo $ciclo): Collection
    {
        return Curso::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->select('id_campus')
            ->selectRaw('COUNT(*) AS cursos')
            ->selectRaw('COUNT(DISTINCT id_plan) AS planes')
            ->selectRaw('COUNT(DISTINCT clave_asignatura) AS materias')
            ->groupBy('id_campus')
            ->orderBy('id_campus')
            ->get();
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
        return Curso::query()
            ->leftJoin('profesores', 'profesores.clave_profesor', '=', 'cursos.clave_profesor')
            ->porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->select('cursos.clave_curso', 'cursos.nombre_curso', 'cursos.id_campus')
            ->selectRaw("COALESCE(profesores.origen_horario, 'SIN_DEFINIR') AS origen")
            ->selectRaw('COALESCE(cursos.sesiones, 0) AS sesiones')
            ->orderBy('cursos.clave_curso')
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
