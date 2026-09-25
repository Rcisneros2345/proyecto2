<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Schema of materias table
$columns = Illuminate\Support\Facades\Schema::getColumnListing('materias');
echo "Columnas en 'materias':\n";
print_r($columns);

// Check NULL values across columns in materias
echo "\nConteo de NULLs o vacíos en 'materias':\n";
foreach ($columns as $col) {
    $nullCount = Illuminate\Support\Facades\DB::table('materias')->whereNull($col)->orWhere($col, '')->count();
    $total = Illuminate\Support\Facades\DB::table('materias')->count();
    echo "  $col: $nullCount vacíos de $total\n";
}

// Inspect sample records
$sample = App\Models\Academia\Materia::with('plan.nivelRel')->take(10)->get();
echo "\nMaterias sample con relaciones:\n";
foreach ($sample as $m) {
    echo "ID: {$m->id} | Clave: {$m->clave_asignatura} | Asignatura: {$m->nombre_asignatura} | Plan: ".($m->plan ? $m->plan->nombre_plan : 'SIN PLAN').' | Nivel/Carrera: '.($m->plan?->nivelRel?->descripcion ?? $m->plan?->nivel ?? 'SIN NIVEL')." | Semestre/Grado: {$m->semestre} | Horas T/P: {$m->horas_teoria}/{$m->horas_practica} | Activa: {$m->activa}\n";
}

// Now check planes table
$planColumns = Illuminate\Support\Facades\Schema::getColumnListing('planes');
echo "\nColumnas en 'planes':\n";
print_r($planColumns);

echo "\nConteo de NULLs o vacíos en 'planes':\n";
foreach ($planColumns as $col) {
    $nullCount = Illuminate\Support\Facades\DB::table('planes')->whereNull($col)->orWhere($col, '')->count();
    $total = Illuminate\Support\Facades\DB::table('planes')->count();
    echo "  $col: $nullCount vacíos de $total\n";
}

$samplePlanes = App\Models\Academia\Plan::with('nivelRel')->withCount('materias')->take(10)->get();
echo "\nPlanes sample con relaciones:\n";
foreach ($samplePlanes as $p) {
    echo "ID: {$p->id} | PlanID: {$p->id_plan} | Nombre: {$p->nombre_plan} | Nivel: ".($p->nivelRel?->descripcion ?? $p->nivel ?? 'SIN NIVEL').' | Modalidad: '.($p->modalidad ?: 'NULL').' | Semestres: '.($p->duracion_semestres ?: 'NULL')." | Materias: {$p->materias_count} | Activo: {$p->activo}\n";
}
