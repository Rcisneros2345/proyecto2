<?php

ob_start();
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cicloService = app(App\Services\CicloActualService::class);
$ciclo = $cicloService->resolve(request());
echo "Ciclo resuelto:\n";
print_r($ciclo->toArray());

echo "\nCiclos con datos en BD:\n";
foreach (App\Models\Academia\Ciclo::all() as $ci) {
    $gCount = App\Models\Academia\Grupo::porCiclo($ci->inicial, $ci->final, $ci->periodo)->count();
    $hCount = App\Models\Academia\HorarioDet::porCiclo($ci->inicial, $ci->final, $ci->periodo)->count();
    if ($gCount > 0 || $hCount > 0) {
        echo "Ciclo {$ci->label} (inicial={$ci->inicial}, final={$ci->final}, periodo={$ci->periodo}) Activo: {$ci->activo} | Grupos: {$gCount} | Horarios: {$hCount}\n";
    }
}

$dashboardService = app(App\Services\AcademiaDashboardService::class);
$ciclo1 = App\Models\Academia\Ciclo::where('inicial', 2026)->where('final', 2026)->where('periodo', 1)->first();
echo "\n--- Probando con Ciclo 2026-2026-1 ---\n";
print_r($ciclo1->toArray());
$summary1 = $dashboardService->build($ciclo1);
echo "\nKPIs ciclo 2026-2026-1:\n";
print_r($summary1['kpis']);

echo "\nAlumnos por grupo count: ".$summary1['alumnosPorGrupo']->count()."\n";
print_r($summary1['alumnosPorGrupo']->take(5)->toArray());

echo "\nCursos por sede:\n";
print_r($summary1['cursosPorSede']->toArray());

echo "\nProfesores por origen:\n";
print_r($summary1['profesoresPorOrigen']->toArray());

echo "\nHoras por origen:\n";
print_r($summary1['horasPorOrigen']->toArray());

echo "\nCursos por origen count: ".$summary1['cursosPorOrigen']->count()."\n";
print_r($summary1['cursosPorOrigen']->take(5)->toArray());

echo "\nNiveles:\n";
print_r($summary1['niveles']->toArray());

echo "\nTurnos:\n";
print_r($summary1['turnos']->toArray());

echo "\nAlumnos por grupo (count=".$summary['alumnosPorGrupo']->count()."):\n";
print_r($summary['alumnosPorGrupo']->take(5)->toArray());

echo "\nCursos por sede:\n";
print_r($summary['cursosPorSede']->toArray());

echo "\nProfesores por origen:\n";
print_r($summary['profesoresPorOrigen']->toArray());

echo "\nHoras por origen:\n";
print_r($summary['horasPorOrigen']->toArray());

echo "\nCursos por origen (count=".$summary['cursosPorOrigen']->count()."):\n";
print_r($summary['cursosPorOrigen']->take(5)->toArray());

echo "\nNiveles:\n";
print_r($summary['niveles']->toArray());

echo "\nTurnos:\n";
print_r($summary['turnos']->toArray());

// Materias analysis
echo "\nMaterias sample:\n";
$materias = App\Models\Academia\Materia::with('plan')->take(5)->get();
print_r($materias->toArray());

// Planes sample
echo "\nPlanes sample:\n";
$planes = App\Models\Academia\Plan::with('nivelRel')->withCount('materias')->take(5)->get();
print_r($planes->toArray());

$out = ob_get_clean();
file_put_contents(__DIR__.'/output.txt', $out);
echo "Written to output.txt\n";
