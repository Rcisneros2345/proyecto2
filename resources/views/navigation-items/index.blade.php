@extends('layouts.admin')

@section('title', 'Navegación del sistema')
@section('breadcrumb', 'Administración › Navegación')

@section('content')
<x-page-header title="Navegación del sistema" subtitle="Administra las entradas, rutas y permisos visibles en el menú." :hide-title="false">
    @slot('actions')
        <a href="{{ route('navigation-items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva entrada
        </a>
    @endslot
</x-page-header>

<div class="card data-table-shell">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Sección</th>
                        <th>Etiqueta</th>
                        <th>Ruta</th>
                        <th>Módulo</th>
                        <th>Permiso</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->section }}</td>
                            <td><i class="bi {{ $item->icon }} me-1"></i>{{ $item->label }}</td>
                            <td><code>{{ $item->route_name }}</code></td>
                            <td>{{ $item->module?->name ?? 'Sin módulo' }}</td>
                            <td><span class="badge bg-light text-dark">{{ $item->permission_action }}</span></td>
                            <td>{{ $item->sort_order }}</td>
                            <td>
                                <span class="badge badge--status {{ $item->active ? 'badge--active' : 'badge--inactive' }}">
                                    {{ $item->active ? 'Activa' : 'Inactiva' }}
                                </span>
                                @if ($item->admin_only)
                                    <span class="badge bg-warning-subtle text-warning">Admin</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('navigation-items.edit', $item) }}" class="btn btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('navigation-items.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar esta entrada?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">@include('partials.empty-state', ['icon' => 'bi-list', 'title' => 'No hay entradas de navegación', 'desc' => 'Crea la primera entrada para construir el menú desde la base de datos.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
