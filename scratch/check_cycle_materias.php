<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ciclo = App\Models\Academia\Ciclo::where('inicial', 2026)->where('final', 2026)->where('periodo', 1)->first();
$cycle = [$ciclo->inicial, $ciclo->final, $ciclo->periodo];

echo "Ciclo: {$ciclo->label}\n";

// Materias in this cycle via Cursos
$cursos = App\Models\Academia\Curso::porCiclo(...$cycle)->activo()->get();
echo 'Cursos count: '.$cursos->count()."\n";
echo 'Unique clave_asignatura in cursos: '.$cursos->pluck('clave_asignatura')->unique()->count()."\n";
echo 'Unique id_plan in cursos: '.$cursos->pluck('id_plan')->unique()->count()."\n";

// Sample curso fields
print_r($cursos->first()?->toArray());

// HorarioDet in this cycle
$horarios = App\Models\Academia\HorarioDet::porCiclo(...$cycle)->activo()->get();
echo "\nHorarioDet count: ".$horarios->count()."\n";
echo 'Unique clave_asignatura in horarios: '.$horarios->pluck('clave_asignatura')->unique()->count()."\n";
print_r($horarios->first()?->toArray());

// Materias linked with plans and carreras in this cycle vs global catalog
$materiasCiclo = App\Models\Academia\Materia::query()
    ->whereIn('clave_asignatura', $horarios->pluck('clave_asignatura')->unique())
    ->with('plan.nivelRel')
    ->get();
echo "\nMaterias encontradas en materias table para este ciclo: ".$materiasCiclo->count()."\n";
foreach ($materiasCiclo->take(5) as $m) {
    echo "Clave: {$m->clave_asignatura} | Asignatura: {$m->nombre_asignatura} | Plan: {$m->plan?->nombre_plan} | Carrera/Nivel: ".($m->plan?->nivelRel?->descripcion ?? $m->plan?->nivel ?? 'Sin datos').' | Semestre: '.($m->semestre ?: 'N/D').' | Horas: '.($m->horas_totales ?: 0).' | Activa: '.($m->activa ? 'Activa' : 'Inactiva')."\n";
}

// Planes in this cycle
$planesCiclo = App\Models\Academia\Plan::query()
    ->whereIn('id_plan', $cursos->pluck('id_plan')->filter()->unique())
    ->with('nivelRel')
    ->withCount('materias')
    ->get();
echo "\nPlanes en este ciclo: ".$planesCiclo->count()."\n";
foreach ($planesCiclo->take(5) as $p) {
    echo "ID: {$p->id_plan} | Nombre: {$p->nombre_plan} | Nivel: ".($p->nivelRel?->descripcion ?? $p->nivel ?? 'Sin datos')." | Materias: {$p->materias_count} | Activo: ".($p->activo ? 'Activo' : 'Inactivo')."\n";
}

// Grupos in this cycle
$grupos = App\Models\Academia\Grupo::porCiclo(...$cycle)->activo()->with(['plan.nivelRel'])->take(5)->get();
echo "\nGrupos sample:\n";
foreach ($grupos as $g) {
    echo "Grupo: {$g->codigo_grupo} | Plan: ".($g->id_plan ?: 'N/D')." | Grado: {$g->grado} | Turno: {$g->turno} | Activo: {$g->activo}\n";
}
