@extends('layouts.admin')

@section('title', 'Puestos')
@section('breadcrumb', 'Catálogos RH › Puestos')

@section('content')
<x-page-header title="Puestos" subtitle="Catálogo de puestos y su vinculación con áreas." :hide-title="false">
    @slot('actions')
        @if (auth()->user()->isAdmin())
            <a href="{{ route('puestos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo puesto
            </a>
        @endif
    @endslot
</x-page-header>

<div class="card data-table-shell">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Identificador</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Empleados</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($puestos as $puesto)
                        <tr>
                            <td><span class="fw-semibold">{{ $puesto->identificador }}</span></td>
                            <td>{{ $puesto->descripcion ?? '—' }}</td>
                            <td>{{ $puesto->area?->identificador ?? '—' }}</td>
                            <td>{{ $puesto->empleados->count() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('puestos.show', $puesto) }}" class="btn btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('puestos.edit', $puesto) }}" class="btn btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('puestos.destroy', $puesto) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar este puesto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                @include('partials.empty-state', [
                                    'title' => 'No hay puestos registrados',
                                    'desc' => 'Cuando se creen puestos y se asignen a áreas, aparecerán aquí.',
                                    'cta' => auth()->user()->isAdmin() ? ['label' => 'Crear puesto', 'url' => route('puestos.create')] : null,
                                    'ctaLink' => true,
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
