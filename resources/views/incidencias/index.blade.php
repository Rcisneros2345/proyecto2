@extends('layouts.admin')

@section('title', 'Incidencias')
@section('breadcrumb', 'Operación › Incidencias')

@section('content')
<x-page-header title="Incidencias" subtitle="Seguimiento y aprobación de incidencias de personal y profesores." :hide-title="false">
    @slot('actions')
        @if (auth()->user()->canAccessModule('incidencias', 'create'))
            <a href="{{ route('incidencias.create') }}" class="btn btn-primary">Nueva incidencia</a>
        @endif
    @endslot
</x-page-header>

<div class="card mb-4">
    <div class="card-body">
        <x-filter-bar :action="route('incidencias.index')" :clear-url="route('incidencias.index')">
            <div class="col-md-4">
                <label for="q" class="form-label">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $q }}" class="form-control" placeholder="Asunto, tipo, motivo, número">
            </div>
            <div class="col-md-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ $estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada" {{ $estado === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ $estado === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
        </x-filter-bar>
    </div>
</div>

<div class="card data-table-shell">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Persona</th>
                        <th>Área</th>
                        <th>Puesto</th>
                        <th>Director / Responsable</th>
                        <th>Asunto</th>
                        <th>Creada</th>
                        <th>Falta programada</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidencias as $incidencia)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $incidencia->empleado ? 'Empleado' : 'Profesor' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    @if ($incidencia->empleado)
                                        {{ $incidencia->empleado->name }}
                                    @else
                                        {{ trim("{$incidencia->profesor?->paterno} {$incidencia->profesor?->materno} {$incidencia->profesor?->nombre_profesor}") }}
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $incidencia->numero_empleado ?? ($incidencia->empleado?->user_id ?? $incidencia->profesor?->clave_profesor ?? '—') }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $incidencia->area?->identificador ?? '—' }}</div>
                                <small class="text-muted">{{ $incidencia->area?->descripcion ?? 'Sin descripción' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $incidencia->puesto?->identificador ?? '—' }}</div>
                                <small class="text-muted">{{ $incidencia->puesto?->descripcion ?? 'Sin descripción' }}</small>
                            </td>
                            <td>
                                @php
                                    $directorNombre = $incidencia->director?->name ?? $incidencia->responsableArea?->name ?? '—';
                                @endphp
                                <div class="fw-semibold">{{ $directorNombre }}</div>
                                <small class="text-muted">
                                    {{ $incidencia->director?->user_id ?? $incidencia->responsableArea?->user_id ?? 'Sin responsable' }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $incidencia->asunto }}</div>
                                <small class="text-muted">{{ $incidencia->tipo_justificacion }}</small>
                            </td>
                            <td>{{ $incidencia->fecha_creacion?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>{{ ($incidencia->fecha_falta_programada ?? $incidencia->fecha_justificacion)?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                @if ($incidencia->tipo_duracion === 'horario')
                                    {{ substr((string) $incidencia->hora_inicio, 0, 5) }} - {{ substr((string) $incidencia->hora_fin, 0, 5) }}
                                @else
                                    Día completo
                                @endif
                            </td>
                            <td>
                                @php
                                $estadoBadge = match($incidencia->estado) {
                                    'aprobada' => 'badge--active',
                                    'rechazada' => 'badge--inactive',
                                    default => 'badge--inactive'
                                };
                                @endphp
                                <span class="badge badge--status {{ $estadoBadge }}">{{ $incidencia->estado }}</span>
                                @if ($incidencia->visto_at)
                                    <small class="d-block text-success mt-1"><i class="bi bi-eye me-1"></i>Vista</small>
                                @endif
                                @if ($incidencia->firmado_at)
                                    <small class="d-block text-primary"><i class="bi bi-pen me-1"></i>Firmada</small>
                                @endif
                            </td>
                            <td>
                                @if (auth()->user()->canAccessModule('incidencias', 'approve'))
                                    <div class="btn-group btn-group-sm" role="group">
                                        <form method="POST" action="{{ route('incidencias.estado', $incidencia) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="aprobada">
                                            <button type="submit" class="btn btn-outline-success" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form method="POST" action="{{ route('incidencias.estado', $incidencia) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="rechazada">
                                            <button type="submit" class="btn btn-outline-danger" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    </div>
                                @endif
                                @if (! $incidencia->visto_at && $incidencia->created_by_user_id === auth()->id())
                                    <form method="POST" action="{{ route('incidencias.vista', $incidencia) }}" class="mt-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar vista</button>
                                    </form>
                                @endif
                                @if ($incidencia->estado === 'aprobada' && ! $incidencia->firmado_at && $incidencia->created_by_user_id === auth()->id())
                                    <form method="POST" action="{{ route('incidencias.firmar', $incidencia) }}" class="mt-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Firmar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-5">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-exclamation-circle',
                                    'title' => 'No hay incidencias registradas',
                                    'desc' => 'Cuando se generen solicitudes o aprobaciones aparecerán aquí.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $incidencias->links() }}
</div>
@endsection
