@extends('layouts.admin')

@section('title', 'Permisos del grupo')
@section('breadcrumb', 'Administración › Grupos de permisos › Permisos de ' . $permissionGroup->name)

@section('content')
<x-page-header title="Permisos del grupo: {{ $permissionGroup->name }}" subtitle="Define qué módulos y acciones puede realizar este grupo." :hide-title="false">
    @slot('actions')
        <a href="{{ route('permission-groups.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endslot
</x-page-header>

{{-- Búsqueda y estadísticas --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="search-modules" placeholder="Buscar módulos por nombre...">
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Total: <strong id="total-modules">{{ $modules->sum('count') }}</strong> módulos | 
                    <strong id="total-permissions">{{ $modules->sum(fn($g) => $g->sum(fn($m) => count($m->permissions))) }}</strong> permisos disponibles
                </small>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <small class="text-muted d-flex align-items-center">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <span id="assigned-badge">0 asignados</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Botones preset --}}
<div class="card shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted small me-2"><i class="bi bi-lightning me-1"></i>Acciones rápidas:</span>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="presetReadOnly()">
            <i class="bi bi-eye me-1"></i> Solo lectura
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="presetFullAccess()">
            <i class="bi bi-unlock me-1"></i> Acceso completo
        </button>
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="presetClearAll()">
            <i class="bi bi-x-circle me-1"></i> Limpiar todo
        </button>
        <span class="ms-auto text-muted small" id="selected-count">0 permisos seleccionados</span>
    </div>
</div>

<form action="{{ route('permission-groups.savePermissions', $permissionGroup) }}" method="POST" id="permissions-form">
    @csrf

    @forelse ($modules as $groupName => $groupModules)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#group-{{ Str::slug($groupName) }}" role="button" style="cursor: pointer;">
                <h3 class="h6 mb-0">
                    <i class="bi bi-folder2-open me-2"></i>{{ $groupName }}
                    <span class="badge bg-light text-dark ms-2">{{ $groupModules->count() }} módulos</span>
                </h3>
                <i class="bi bi-chevron-down text-muted"></i>
            </div>
            <div id="group-{{ Str::slug($groupName) }}" class="collapse show">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Módulo</th>
                                <th style="width: 10%; text-align: center;">Todo</th>
                                <th style="width: 12%; text-align: center;">Ver</th>
                                <th style="width: 12%; text-align: center;">Crear</th>
                                <th style="width: 12%; text-align: center;">Editar</th>
                                <th style="width: 12%; text-align: center;">Eliminar</th>
                                <th style="width: 12%; text-align: center;">Extra</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupModules as $module)
                                @php
                                    $modulePermissionIds = $module->permissions->pluck('id')->all();
                                    $assignedCount = count(array_intersect($modulePermissionIds, $assigned));
                                    $allChecked = $assignedCount === count($modulePermissionIds) && count($modulePermissionIds) > 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($module->icon)
                                                <i class="bi bi-{{ $module->icon }} text-muted"></i>
                                            @endif
                                            <div>
                                                <span class="fw-medium">{{ $module->name }}</span>
                                                @if ($module->description)
                                                    <small class="text-muted d-block">{{ $module->description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Checkbox "Todo el módulo" --}}
                                    <td style="text-align: center;">
                                        <input type="checkbox" class="form-check-input module-toggle" data-module-id="{{ $module->id }}" {{ $allChecked ? 'checked' : '' }}>
                                    </td>
                                    {{-- Checkboxes individuales por acción --}}
                                    @php
                                        $standardActions = ['view', 'create', 'update', 'delete'];
                                        $extraPermissions = $module->permissions->filter(fn($p) => !in_array($p->action, $standardActions));
                                    @endphp
                                    @foreach ($standardActions as $action)
                                        @php
                                            $perm = $module->permissions->firstWhere('action', $action);
                                        @endphp
                                        <td style="text-align: center;">
                                            @if ($perm)
                                                <input type="checkbox" name="permission_ids[]" value="{{ $perm->id }}" class="form-check-input permission-checkbox" data-module-id="{{ $module->id }}" data-action="{{ $action }}" {{ in_array($perm->id, $assigned, true) ? 'checked' : '' }}>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    {{-- Acciones extra --}}
                                    <td style="text-align: center;">
                                        @if ($extraPermissions->isNotEmpty())
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    +{{ $extraPermissions->count() }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @foreach ($extraPermissions as $extraPerm)
                                                        <li>
                                                            <label class="dropdown-item d-flex align-items-center gap-2 mb-0">
                                                                <input type="checkbox" name="permission_ids[]" value="{{ $extraPerm->id }}" class="form-check-input permission-checkbox" data-module-id="{{ $module->id }}" data-action="{{ $extraPerm->action }}" {{ in_array($extraPerm->id, $assigned, true) ? 'checked' : '' }}>
                                                                <span>{{ $extraPerm->name ?? $extraPerm->action }}</span>
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center text-muted py-5">
                @include('partials.empty-state', [
                    'icon' => 'bi-shield-check',
                    'title' => 'No hay módulos registrados',
                    'desc' => 'Ejecuta el seeder de módulos para comenzar a configurar permisos.',
                ])
            </div>
        </div>
    @endforelse

    {{-- Botón guardar --}}
    @if ($modules->isNotEmpty())
        <div class="d-flex justify-content-end mt-3 mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-lg me-1"></i> Guardar cambios
            </button>
        </div>
    @endif
</form>
@endsection

@section('scripts')
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('permissions-form');
        if (!form) return;

        // --- Toggle "Todo el módulo" ---
        form.querySelectorAll('.module-toggle').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                const moduleId = this.dataset.moduleId;
                const checkboxes = form.querySelectorAll('.permission-checkbox[data-module-id="' + moduleId + '"]');
                checkboxes.forEach(function (cb) {
                    cb.checked = toggle.checked;
                });
                updateCount();
            });
        });

        // --- Cuando cambia un checkbox individual, actualizar el toggle del módulo ---
        form.querySelectorAll('.permission-checkbox').forEach(function (cb) {
            cb.addEventListener('change', function () {
                const moduleId = this.dataset.moduleId;
                const allCb = form.querySelectorAll('.permission-checkbox[data-module-id="' + moduleId + '"]');
                const allChecked = Array.from(allCb).every(function (c) { return c.checked; });
                const toggle = form.querySelector('.module-toggle[data-module-id="' + moduleId + '"]');
                if (toggle) toggle.checked = allChecked;
                updateCount();
            });
        });

        // --- Búsqueda de módulos ---
        const searchInput = document.getElementById('search-modules');
        if (searchInput) {
            searchInput.addEventListener('keyup', function () {
                const query = this.value.toLowerCase();
                const rows = form.querySelectorAll('tbody tr');
                let visibleCount = 0;
                
                rows.forEach(function (row) {
                    const moduleName = row.textContent.toLowerCase();
                    const matches = moduleName.includes(query) || query === '';
                    row.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });

                // Ocultar acordeones vacíos
                form.querySelectorAll('.collapse').forEach(function (section) {
                    const visibleRows = section.querySelectorAll('tbody tr[style=""]').length;
                    section.style.display = visibleRows > 0 ? '' : 'none';
                });
            });
        }

        // --- Contador de permisos seleccionados ---
        function updateCount() {
            const count = form.querySelectorAll('.permission-checkbox:checked').length;
            const el = document.getElementById('selected-count');
            if (el) el.textContent = count + ' permisos seleccionados';
            
            // Actualizar badge de asignados
            const badge = document.getElementById('assigned-badge');
            if (badge) badge.textContent = count + ' asignados';
        }
        updateCount();
    });

    // --- Presets ---
    function presetReadOnly() {
        document.querySelectorAll('.permission-checkbox').forEach(function (cb) {
            cb.checked = cb.dataset.action === 'view';
        });
        syncModuleToggles();
        updateCount();
    }

    function presetFullAccess() {
        document.querySelectorAll('.permission-checkbox').forEach(function (cb) {
            cb.checked = true;
        });
        syncModuleToggles();
        updateCount();
    }

    function presetClearAll() {
        document.querySelectorAll('.permission-checkbox, .module-toggle').forEach(function (cb) {
            cb.checked = false;
        });
        updateCount();
    }

    function syncModuleToggles() {
        document.querySelectorAll('.module-toggle').forEach(function (toggle) {
            const moduleId = toggle.dataset.moduleId;
            const cbs = document.querySelectorAll('.permission-checkbox[data-module-id="' + moduleId + '"]');
            toggle.checked = cbs.length > 0 && Array.from(cbs).every(function (c) { return c.checked; });
        });
    }

    function updateCount() {
        var count = document.querySelectorAll('.permission-checkbox:checked').length;
        var el = document.getElementById('selected-count');
        if (el) el.textContent = count + ' permisos seleccionados';
        
        var badge = document.getElementById('assigned-badge');
        if (badge) badge.textContent = count + ' asignados';
    }
</script>
@endsection
