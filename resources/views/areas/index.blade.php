@extends('layouts.admin')

@section('title', 'Áreas')
@section('breadcrumb', 'Operación › Áreas')

@section('content')
<x-page-header title="Áreas" subtitle="Administración y organización por áreas responsables." :hide-title="false">
    @slot('actions')
        @if (auth()->user()->isAdmin())
            <a href="{{ route('areas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nueva área
            </a>
        @endif
    @endslot
</x-page-header>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Identificador</th>
                        <th>Descripción</th>
                        <th>Empleado responsable</th>
                        <th>Puestos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $area)
                        <tr>
                            <td><span class="fw-semibold">{{ $area->identificador }}</span></td>
                            <td>{{ $area->descripcion ?? '—' }}</td>
                            <td>{{ $area->empleadoResponsable?->name ?? '—' }}</td>
                            <td>{{ $area->puestos->count() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('areas.show', $area) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('areas.edit', $area) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('areas.destroy', $area) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar esta área?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay áreas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
