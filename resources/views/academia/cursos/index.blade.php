@extends('layouts.admin')

@section('title', 'Cursos')
@section('breadcrumb', 'Academia › Cursos')

@section('content')
<x-page-header title="Cursos" subtitle="Catálogo académico de cursos y su relación con materias, profesores y ciclo actual." :hide-title="false">
    @slot('actions')
        <a href="{{ route('academia.ciclos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-calendar me-1"></i> Cambiar ciclo
        </a>
        <a href="{{ route('academia.cursos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo curso
        </a>
    @endslot
</x-page-header>

<div class="card">
    <div class="card-body p-0">
        @if ($cursos->isEmpty())
            <div class="card-body text-center text-muted py-5">
                @include('partials.empty-state', [
                    'icon' => 'bi-book',
                    'title' => 'No hay cursos registrados para este ciclo',
                    'desc' => 'Crea el primer curso para empezar a asignar docentes, materias y alumnos.',
                    'cta' => ['label' => 'Crear curso', 'url' => route('academia.cursos.create')],
                    'ctaLink' => true,
                ])
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Descripción</th>
                            <th>Materia</th>
                            <th>Maestro(s)</th>
                            <th>Nivel</th>
                            <th>Turno</th>
                            <th>Sede</th>
                            <th class="text-center">Alumnos</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cursos as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->clave_curso }}</td>
                                <td>{{ $c->nombre_curso }}</td>
                                <td>{{ $c->materia?->nombre_asignatura ?? $c->materias->first()?->materia?->nombre_asignatura ?? 'Materia no asignada' }}</td>
                                <td class="small">
                                    @forelse ($c->docentes as $docente)
                                        <div>{{ $docente->nombre_completo ?: $docente->clave_profesor }}</div>
                                    @empty
                                        <span class="text-muted">Sin maestro asignado</span>
                                    @endforelse
                                </td>
                                <td>{{ $c->nivelRel?->descripcion ?? $c->plan?->nivelRel?->descripcion ?? $c->nivel ?? 'Nivel no asignado' }}</td>
                                <td>{{ $c->turno_nombre }}</td>
                                <td>{{ $c->sede?->descripcion ?? $c->id_campus }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $c->alumnos_count }}</span>
                                </td>
                                <td>
                                    <span class="badge badge--status {{ $c->activo ? 'badge--active' : 'badge--inactive' }}">
                                        {{ $c->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('academia.cursos.show', $c) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{ $cursos->withQueryString()->links() }}
@endsection
