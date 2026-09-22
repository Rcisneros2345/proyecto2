@extends('layouts.admin')

@section('title', 'Módulos del sistema')
@section('breadcrumb', 'Administración > Módulos')

@section('content')
<x-page-header title="Módulos del sistema" subtitle="Consulta los módulos disponibles y el número de permisos que controlan cada uno.">
    @slot('actions')
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary"><i class="bi bi-key me-1"></i> Ver permisos</a>
    @endslot
</x-page-header>

<div class="row g-4">
    @forelse($modules as $module)
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <span class="d-inline-grid place-items-center rounded-3 p-3 bg-primary-subtle text-primary" style="min-width:52px;min-height:52px">
                            <i class="bi {{ $module->icon ?: 'bi-grid' }} fs-4" aria-hidden="true"></i>
                        </span>
                        <div class="min-width-0">
                            <h2 class="h5 mb-1">{{ $module->name }}</h2>
                            <code class="small">{{ $module->slug }}</code>
                        </div>
                    </div>

                    <p class="text-muted small mb-3">{{ $module->description ?: 'Módulo operativo del sistema.' }}</p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <x-badge color="blue" :label="$module->group_name ?: 'Sin grupo'" />
                        <x-badge color="gray" :label="$module->permissions_count . ' permisos'" />
                    </div>

                    <div class="border-top pt-3 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0">Submenú</h3>
                            <span class="small text-muted">{{ $module->navigationItems->count() }} enlaces</span>
                        </div>
                        @if($module->navigationItems->isEmpty())
                            <p class="small text-muted mb-0">No hay entradas de navegación asociadas.</p>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($module->navigationItems as $item)
                                    <a href="{{ route($item->route_name) }}" class="list-group-item list-group-item-action px-0 d-flex align-items-center gap-2 border-0">
                                        <i class="bi {{ $item->icon ?: 'bi-chevron-right' }} text-primary" aria-hidden="true"></i>
                                        <span class="small">{{ $item->label }}</span>
                                        <i class="bi bi-arrow-up-right ms-auto text-muted small" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <x-badge :color="$module->active ? 'green' : 'gray'" dot :label="$module->active ? 'Activo' : 'Inactivo'" />
                        <form action="{{ route('modules.update', $module) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="active" value="{{ $module->active ? 0 : 1 }}">
                            <button class="btn btn-sm btn-outline-secondary">{{ $module->active ? 'Desactivar' : 'Activar' }}</button>
                        </form>
                    </div>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info">No hay módulos registrados.</div></div>
    @endforelse
</div>
@endsection
