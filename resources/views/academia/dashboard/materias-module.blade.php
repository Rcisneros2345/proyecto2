@extends('layouts.admin')

@section('title', 'Materias - Academia')
@section('breadcrumb', 'Academia › Materias')

@section('content')
<x-page-header title="Catálogo de Materias" subtitle="Materias y asignaturas vinculadas a los planes de estudio y ciclos académicos." :hide-title="false">
    @slot('actions')
        <a href="{{ route('academia.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
        </a>
    @endslot
</x-page-header>

<div class="card shadow-sm border mb-4">
    <div class="card-body p-4 text-center">
        <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary" style="width: 56px; height: 56px;">
                <i class="bi bi-journal-bookmark fs-3"></i>
            </span>
        </div>
        <h5 class="fw-bold mb-2">Materias del Ciclo {{ $ciclo->label }}</h5>
        <p class="text-secondary mb-3">
            El catálogo completo de materias y su programación operativa se gestiona directamente desde el 
            <strong>Dashboard de Académica</strong> y la asignación en cursos y horarios.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('academia.dashboard') }}#seccion-materias" class="btn btn-primary btn-sm">
                <i class="bi bi-table me-1"></i> Ver Tabla de Materias en Dashboard
            </a>
            <a href="{{ route('academia.cursos.index', ['ciclo_principal' => $ciclo->label]) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-book me-1"></i> Ver Cursos Asignados
            </a>
        </div>
    </div>
</div>
@endsection
