<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ciclo = App\Models\Academia\Ciclo::where('inicial', 2026)->where('final', 2026)->where('periodo', 1)->first();
$cycle = [$ciclo->inicial, $ciclo->final, $ciclo->periodo];

$start = microtime(true);

// 1. Materias del ciclo
$materiaKeys = App\Models\Academia\HorarioDet::porCiclo(...$cycle)
    ->activo()
    ->select('clave_asignatura')
    ->distinct()
    ->pluck('clave_asignatura');

if ($materiaKeys->isEmpty()) {
    $materiaKeys = App\Models\Academia\Curso::porCiclo(...$cycle)
        ->activo()
        ->select('clave_asignatura')
        ->distinct()
        ->pluck('clave_asignatura');
}

echo 'Materia keys in cycle: '.$materiaKeys->count().' in '.round((microtime(true) - $start) * 1000, 2)."ms\n";

$start2 = microtime(true);
$materiasQuery = App\Models\Academia\Materia::query()
    ->with(['plan.nivelRel']);

if ($materiaKeys->isNotEmpty()) {
    $materiasQuery->whereIn('clave_asignatura', $materiaKeys);
}

$materias = $materiasQuery->orderBy('nombre_asignatura')->get();
echo 'Loaded '.$materias->count().' materias in '.round((microtime(true) - $start2) * 1000, 2)."ms\n";

// 2. Planes del ciclo
$planIds = App\Models\Academia\Curso::porCiclo(...$cycle)
    ->activo()
    ->whereNotNull('id_plan')
    ->select('id_plan')
    ->distinct()
    ->pluck('id_plan');

if ($planIds->isEmpty()) {
    // try via materias
    $planIds = $materias->pluck('id_plan')->filter()->unique();
}

$planesQuery = App\Models\Academia\Plan::query()
    ->with('nivelRel')
    ->withCount('materias');

if ($planIds->isNotEmpty()) {
    $planesQuery->whereIn('id_plan', $planIds);
}

$planes = $planesQuery->orderBy('nombre_plan')->get();
echo 'Loaded '.$planes->count().' planes in '.round((microtime(true) - $start2) * 1000, 2)."ms\n";

echo 'Total time: '.round((microtime(true) - $start) * 1000, 2)."ms\n";
