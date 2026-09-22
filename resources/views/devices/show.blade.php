@extends('layouts.admin')

@section('title', $device->name)
@section('breadcrumb', 'Operación › Dispositivos › ' . $device->name)

@php
    $statusColor = match($device->status) {
        'online' => 'green',
        'offline' => 'red',
        default => 'gray',
    };
    $statusIcon = match($device->status) {
        'online' => 'bi-wifi',
        'offline' => 'bi-wifi-off',
        default => 'bi-question-circle',
    };
    $statusLabel = \App\Models\Device::states()[$device->status] ?? $device->status;
    $syncBadgeMeta = [
        'completed' => ['color' => 'green',  'icon' => 'bi-check-circle'],
        'failed'    => ['color' => 'red',    'icon' => 'bi-x-circle'],
        'running'   => ['color' => 'amber',  'icon' => 'bi-arrow-repeat'],
        'queued'    => ['color' => 'gray',   'icon' => 'bi-clock'],
        'cancelled' => ['color' => 'gray',   'icon' => 'bi-slash-circle'],
    ];
@endphp

@section('content')
<x-page-header title="{{ $device->name }}" subtitle="{{ $device->ip }}:{{ $device->port }} · {{ $statusLabel }}{{ $device->serial_number ? ' · SN ' . $device->serial_number : '' }}" :hide-title="false">
    @slot('actions')
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
    @if (auth()->user()->canAccessModule('dispositivos', 'sync'))
            <button type="button" class="btn btn-primary sync-btn" data-operation="all"
                    data-url="{{ route('devices.sync-all', $device) }}">
                <i class="bi bi-arrow-repeat me-1"></i> Sincronizar todo
            </button>
            <button type="button" class="btn btn-outline-secondary sync-btn" data-operation="users"
                    data-url="{{ route('devices.sync-users', $device) }}">
                <i class="bi bi-people me-1"></i> Usuarios
            </button>
            <button type="button" class="btn btn-outline-secondary sync-btn" data-operation="attendances"
                    data-url="{{ route('devices.sync-attendances', $device) }}">
                <i class="bi bi-calendar-check me-1"></i> Asistencias
            </button>
            <button type="button" class="btn btn-outline-secondary sync-btn" data-operation="fingerprints"
                    data-url="{{ route('devices.sync-fingerprints', $device) }}">
                <i class="bi bi-fingerprint me-1"></i> Huellas
            </button>
    @endif
        </div>
    @endslot
</x-page-header>

{{-- [UI ONLY] State zone: visual snapshot of the device's current operating state --}}
<div class="device-state-zone" role="region" aria-label="Estado actual del dispositivo {{ $device->name }}">
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-hdd-rack me-1" aria-hidden="true"></i>Equipo</span>
        <span class="ds-value">{{ $info['device_name'] ?? '-' }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-cpu me-1" aria-hidden="true"></i>Firmware</span>
        <span class="ds-value">{{ $info['vendor'] ?? '-' }} {{ $info['version'] ?? '' }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Conexión</span>
        <span class="ds-value mono">{{ $device->ip }}:{{ $device->port }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-circle-fill me-1" aria-hidden="true"></i>Estado</span>
        <span class="ds-value"><x-badge :color="$statusColor" dot icon="{{ $statusIcon }}" :label="$statusLabel" size="sm" /></span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-clock me-1" aria-hidden="true"></i>Hora del equipo</span>
        <span class="ds-value mono">{{ $info['time'] ?? '-' }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-people me-1" aria-hidden="true"></i>Empleados</span>
        <span class="ds-value mono">{{ $device->employees_count }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-calendar-check me-1" aria-hidden="true"></i>Registros</span>
        <span class="ds-value mono">{{ $device->attendances_count }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-fingerprint me-1" aria-hidden="true"></i>Huellas</span>
        <span class="ds-value mono">{{ $device->fingerprints_count }}</span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Última sync</span>
        <span class="ds-value">
            @if ($device->latestSync?->finished_at)
                {{ $device->latestSync->finished_at->format('d/m/Y H:i') }}
            @else
                <span class="text-tertiary-token">Nunca ejecutada</span>
            @endif
        </span>
    </div>
</div>

@if (!$info)
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
        No se pudo obtener información del dispositivo. Verifica la IP, puerto y que esté en la misma red.
    </div>
@endif

<div class="kpi-grid" aria-live="polite" aria-label="KPIs del dispositivo">
    <x-stat-card icon="bi-people" :value="$device->employees_count" label="Empleados" color="blue" data-stat="employees">
        <div class="kpi-spark">
            @include('partials.sparkline', ['points' => $spark['employees'], 'color' => 'var(--cat-blue)', 'width' => 84, 'height' => 28])
        </div>
    </x-stat-card>

    <x-stat-card icon="bi-calendar-check" :value="$device->attendances_count" label="Asistencias" color="teal" data-stat="attendances">
        <div class="kpi-spark">
            @include('partials.sparkline', ['points' => $spark['attendances'], 'color' => 'var(--primary)', 'width' => 84, 'height' => 28])
        </div>
    </x-stat-card>

    <x-stat-card icon="bi-fingerprint" :value="$device->fingerprints_count" label="Huellas" color="purple" data-stat="fingerprints">
        <div class="kpi-spark">
            @include('partials.sparkline', ['points' => $spark['fingerprints'], 'color' => 'var(--cat-purple)', 'width' => 84, 'height' => 28])
        </div>
    </x-stat-card>

    <x-stat-card icon="bi-wifi" :value="$statusLabel" label="Estado de conexión" color="green" data-stat="status" />
</div>

<div class="row g-3 align-items-stretch mb-4">
    {{-- Sync progress & latest operation --}}
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="small text-tertiary-token">Última operación</div>
                        <div class="fw-semibold">
                            @if ($device->latestSync)
                                Sincronización de {{ $device->latestSync->operation_label }}
                            @else
                                Sin sincronizaciones registradas
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="small text-tertiary-token">Estado actual</div>
                        @php $ls = $device->latestSync; @endphp
                        @if ($ls)
                            @php $meta = $syncBadgeMeta[$ls->status] ?? ['color' => 'gray', 'icon' => 'bi-clock']; @endphp
                            <x-badge :color="$meta['color']" dot icon="{{ $meta['icon'] }}" :label="ucfirst($ls->status)" size="sm" />
                        @else
                            <x-badge color="gray" dot icon="bi-clock" label="Nunca ejecutada" size="sm" />
                        @endif
                    </div>
                </div>

                <div class="alert alert-secondary d-flex justify-content-between align-items-center gap-3 py-2 px-3 d-none"
                     id="sync-status" data-url="{{ route('devices.sync-status', $device) }}" aria-live="polite">
                    <span>
                        <i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i>
                        <strong>Sincronización:</strong>
                        <span data-sync-label class="fw-semibold">{{ $ls?->status ?? 'Nunca ejecutada' }}</span>
                    </span>
                    <span class="small text-tertiary-token text-end" data-sync-detail></span>
                </div>

                <div id="sync-progress" class="d-none">
                    <div class="progress mt-3 mb-2" style="height: 10px;">
                        <div id="sync-progress-bar" class="progress-bar bg-success" role="progressbar"
                             aria-label="Progreso de sincronización" style="width: 0%;"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span id="sync-status-text">Esperando...</span>
                        <span id="sync-progress-text">0%</span>
                    </div>
                </div>

                <div class="small text-muted">
                    @if ($ls?->finished_at)
                        Completado el {{ $ls->finished_at->format('d/m/Y H:i') }} · Creados: {{ $ls->created_count }} · Actualizados: {{ $ls->updated_count }}
                        @if ($ls->error_message)
                            <span class="text-tertiary-token">· {{ $ls->error_message }}</span>
                        @endif
                    @else
                        La próxima sincronización mostrará aquí su avance en tiempo real.
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent activity timeline --}}
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold"><i class="bi bi-activity me-1" aria-hidden="true"></i> Actividad reciente</span>
            </div>
            <div class="card-body py-2" data-recent-syncs>
                @forelse ($recentSyncs as $sync)
                    @php $meta = $syncBadgeMeta[$sync->status] ?? ['color' => 'gray', 'icon' => 'bi-clock']; @endphp
                    <div class="d-flex align-items-center gap-3 py-2">
                        <x-badge :color="$meta['color']" icon="{{ $meta['icon'] }}" size="sm" />
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">Sincronización de {{ $sync->operation_label }}</div>
                            <div class="text-tertiary-token small">
                                @if ($sync->status === 'failed' && $sync->error_message)
                                    {{ $sync->error_message }}
                                @else
                                    Creados: {{ $sync->created_count }} · Actualizados: {{ $sync->updated_count }}
                                @endif
                            </div>
                        </div>
                        <span class="mono small text-tertiary-token">{{ $sync->finished_at?->format('H:i') ?? '—' }}</span>
                    </div>
                @empty
                    @include('partials.empty-state', [
                        'icon'  => 'bi-activity',
                        'title' => 'Sin actividad aún',
                        'desc'  => 'Las sincronizaciones que ejecutes aparecerán aquí.',
                    ])
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap py-2">
                <div class="btn-group btn-group-sm" role="tablist" aria-label="Selector de registros">
                    <button type="button" class="btn btn-secondary active" role="tab" aria-selected="true"
                            aria-controls="tab-empleados"
                            data-bs-toggle="tab" data-bs-target="#tab-empleados">
                        <i class="bi bi-people me-1" aria-hidden="true"></i> Empleados
                    </button>
                    @if (auth()->user()->isAdmin())
                    <button type="button" class="btn btn-outline-secondary" role="tab" aria-selected="false"
                            aria-controls="tab-asistencias"
                            data-bs-toggle="tab" data-bs-target="#tab-asistencias">
                        <i class="bi bi-calendar-check me-1" aria-hidden="true"></i> Asistencias
                    </button>
                    <button type="button" class="btn btn-outline-secondary" role="tab" aria-selected="false"
                            aria-controls="tab-huellas"
                            data-bs-toggle="tab" data-bs-target="#tab-huellas">
                        <i class="bi bi-fingerprint me-1" aria-hidden="true"></i> Huellas
                    </button>
                    <button type="button" class="btn btn-outline-secondary" role="tab" aria-selected="false"
                            aria-controls="tab-acciones-remotas"
                            data-bs-toggle="tab" data-bs-target="#tab-acciones-remotas">
                        <i class="bi bi-gear me-1" aria-hidden="true"></i> Acciones remotas
                    </button>
                    @endif
                </div>
                <input type="search" class="form-control form-control-sm w-auto"
                       data-table-search placeholder="Buscar…" aria-label="Buscar en la tabla activa">
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-empleados" role="tabpanel" data-panel="employees">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 table-cards" aria-label="Empleados del dispositivo">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>UID</th>
                                        <th>Nombre</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody data-employees-rows>
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td data-label="ID"><code>{{ $employee->user_id }}</code></td>
                                            <td data-label="UID"><span class="mono text-secondary-token">{{ $employee->pivot->device_uid }}</span></td>
                                            <td data-label="Nombre">
                                                <span class="avatar is-sm me-2" aria-hidden="true">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                                                <span class="fw-semibold">{{ $employee->name }}</span>
                                            </td>
                                            <td data-label="">
                                                <div class="table-row-actions justify-content-end">
                                                @if (auth()->user()->isAdmin())
                                                    <form action="{{ route('devices.sync-fingerprints', $device) }}" method="POST" class="d-inline" data-sync>
                                                        @csrf
                                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                                        <button class="btn btn-sm btn-ghost" title="Extraer huella de {{ $employee->name }}" aria-label="Extraer huella de {{ $employee->name }}"><i class="bi bi-fingerprint"></i></button>
                                                    </form>
                                                    <form action="{{ route('devices.employees.upload-fingerprints', [$device, $employee]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-ghost" title="Subir huella digital al checador" aria-label="Subir huella"><i class="bi bi-cloud-arrow-up"></i></button>
                                                    </form>
                                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-ghost" title="Editar {{ $employee->name }}" aria-label="Editar {{ $employee->name }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('devices.employees.remove', [$device, $employee]) }}" method="POST" class="d-inline"
                                                          data-confirm
                                                          data-confirm-danger
                                                          data-confirm-title="¿Quitar a {{ $employee->name }} del dispositivo?"
                                                          data-confirm-message="Se eliminará solo de este checador y perderá sus accesos en él. Esta acción no se puede deshacer.">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-icon-danger" title="Eliminar {{ $employee->name }}" aria-label="Eliminar {{ $employee->name }}"><i class="bi bi-person-x"></i></button>
                                                    </form>
                                                @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                @include('partials.empty-state', [
                                                    'icon'  => 'bi-people',
                                                    'title' => 'Sin empleados en el dispositivo',
                                                    'desc'  => 'Usa «Usuarios» para vaciar el checador o agrega uno manualmente.',
                                                    'cta'   => auth()->user()->isAdmin()
                                                        ? ['label' => 'Agregar empleado', 'url' => route('employees.create')]
                                                        : null,
                                                    'ctaLink' => true,
                                                ])
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-asistencias" role="tabpanel" data-panel="attendances">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 table-cards" aria-label="Asistencias del dispositivo">
                                <thead><tr><th>Fecha y hora</th><th>Empleado</th><th>ID</th><th>Marcado</th></tr></thead>
                                <tbody data-attendances-rows>
                                    @forelse ($attendances as $attendance)
                                        <tr>
                                            <td data-label="Fecha y hora"><span class="mono text-secondary-token">{{ $attendance->recorded_at->format('d/m/Y H:i:s') }}</span></td>
                                            <td data-label="Empleado">{{ $attendance->employee?->name ?? 'Sin asignar' }}</td>
                                            <td data-label="ID"><code>{{ $attendance->user_id }}</code></td>
                                            <td data-label="Marcado">
                                                <x-badge :color="$attendance->stateColorClass() === 'cat-green' ? 'green' : ($attendance->stateColorClass() === 'cat-red' ? 'red' : 'gray')" dot :label="$attendance->shortStateLabel()" size="sm" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                @include('partials.empty-state', [
                                                    'icon'  => 'bi-calendar-check',
                                                    'title' => 'Sin asistencias sincronizadas',
                                                    'desc'  => 'Los chequeos aparecerán aquí cuando traigas las asistencias del checador.',
                                                ])
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-huellas" role="tabpanel" data-panel="fingerprints">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 table-cards" aria-label="Huellas del dispositivo">
                                <thead><tr><th>ID usuario</th><th>Empleado</th><th>Dedo</th><th>Registrada</th></tr></thead>
                                <tbody data-fingerprints-rows>
                                    @forelse ($fingerprints as $fingerprint)
                                        <tr>
                                            <td data-label="ID usuario"><code>{{ $fingerprint->employee?->user_id ?? '—' }}</code></td>
                                            <td data-label="Empleado">{{ $fingerprint->employee?->name ?? 'Sin asignar' }}</td>
                                            <td data-label="Dedo"><span class="mono text-secondary-token">#{{ $fingerprint->finger }}</span></td>
                                            <td data-label="Registrada"><span class="mono text-secondary-token">{{ $fingerprint->created_at?->format('d/m/Y H:i') }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                @include('partials.empty-state', [
                                                    'icon'  => 'bi-fingerprint',
                                                    'title' => 'Sin huellas extraídas',
                                                    'desc'  => 'Usa «Huellas» o el botón de huella por empleado para extraerlas del checador.',
                                                ])
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin())
                    <div class="tab-pane fade" id="tab-acciones-remotas" role="tabpanel">
                        <div class="p-3">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <form action="{{ route('devices.set-time', $device) }}" method="POST">
                                        @csrf
                                        <label for="datetime" class="form-label small text-tertiary-token mb-1">Establecer fecha y hora en el equipo</label>
                                        <div class="d-flex gap-2">
                                            <input type="datetime-local" id="datetime" name="datetime" class="form-control" required>
                                            <button class="btn btn-primary text-nowrap"><i class="bi bi-clock-history me-1"></i> Aplicar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 d-flex flex-wrap gap-2 align-items-start">
                                    <form action="{{ route('devices.clear-attendance', $device) }}" method="POST"
                                          data-confirm
                                          data-confirm-danger
                                          data-confirm-type="ELIMINAR"
                                          data-confirm-title="¿Limpiar la bitácora de asistencias?"
                                          data-confirm-message="Se eliminarán todos los registros de asistencia presentes en el equipo ({{ $device->attendances_count }} chequeos). Esta acción no se puede deshacer. Escribe ELIMINAR para confirmar.">
                                        @csrf
                                        <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Limpiar bitácora</button>
                                    </form>
                                    <form action="{{ route('devices.restore', $device) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-outline-warning"><i class="bi bi-unlock me-1"></i> Habilitar dispositivo</button>
                                    </form>
                                    <a href="{{ route('devices.edit', $device) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-gear me-1"></i> Editar configuración
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold">Asistencias recientes</span>
                <a href="{{ route('attendances.index', ['device_id' => $device->id]) }}" class="btn btn-sm btn-ghost" aria-label="Ver todas las asistencias">
                    Ver todas <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>
            <div class="card-body py-2" data-recent-feed>
                @forelse ($attendances->take(6) as $attendance)
                    <div class="d-flex align-items-center gap-2 py-2">
                        <span class="avatar is-sm" aria-hidden="true">{{ strtoupper(substr($attendance->employee?->name ?? '?', 0, 1)) }}</span>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ $attendance->employee?->name ?? 'Sin asignar' }}</div>
                            <div class="text-tertiary-token mono">ID {{ $attendance->user_id }}</div>
                        </div>
                        <div class="text-end">
                            <div>
                                <x-badge :color="$attendance->stateColorClass() === 'cat-green' ? 'green' : ($attendance->stateColorClass() === 'cat-red' ? 'red' : 'gray')" dot :label="$attendance->shortStateLabel()" size="sm" />
                            </div>
                            <div class="mono text-tertiary-token small">{{ $attendance->recorded_at->format('d/m H:i:s') }}</div>
                        </div>
                    </div>
                @empty
                    @include('partials.empty-state', [
                        'icon'  => 'bi-calendar-check',
                        'title' => 'Sin asistencias recientes',
                        'desc'  => 'Aún no hay chequeos registrados para este checador.',
                    ])
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const statusBox = document.querySelector('#sync-status');
        if (!statusBox) return;

        const label = statusBox.querySelector('[data-sync-label]');
        const detail = statusBox.querySelector('[data-sync-detail]');
        const progressBox = document.querySelector('#sync-progress');
        const progressBar = document.querySelector('#sync-progress-bar');
        const progressStatusText = document.querySelector('#sync-status-text');
        const progressPercentText = document.querySelector('#sync-progress-text');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const searchInput = document.querySelector('[data-table-search]');
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');

        const labels = { queued: 'En cola', running: 'Procesando', completed: 'Completada', failed: 'Fallida', never: 'Nunca ejecutada' };
        const operations = { users: 'usuarios', fingerprints: 'huellas', attendances: 'asistencias', all: 'todo' };
        const stages = { usuarios: 'Descargando usuarios', asistencias: 'Descargando asistencias', huellas: 'Descargando huellas', Preparando: 'Preparando sincronización', Terminado: 'Sincronización terminada', Error: 'Sincronización detenida' };
        const buttonLabels = {
            all: 'Sincronizar todo',
            users: 'Usuarios',
            attendances: 'Asistencias',
            fingerprints: 'Huellas',
        };
        const syncStatusMeta = {
            completed: { color: 'green', icon: 'bi-check-circle' },
            failed: { color: 'red', icon: 'bi-x-circle' },
            running: { color: 'amber', icon: 'bi-arrow-repeat' },
            queued: { color: 'gray', icon: 'bi-clock' },
        };

        let previousStatus = null;
        let notifiedResult = null;
        let pollingInterval = null;
        let activePanel = 'employees';

        function restoreButtonText(button) {
            if (!button) return;
            const key = button.dataset.operation || 'all';
            button.disabled = false;
            button.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i> ${buttonLabels[key] || 'Sincronizar'}`;
        }

        function restoreAllSyncButtons() {
            document.querySelectorAll('.sync-btn').forEach(restoreButtonText);
        }

        const esc = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));

        function emptyStateHtml(icon, title, desc) {
            return `<div class="empty-state"><i class="bi ${icon}"></i><div class="es-title">${esc(title)}</div><div class="es-desc">${esc(desc)}</div></div>`;
        }

        /**
         * Replica exacta del render inicial Blade (ID, UID, nombre y las 4 acciones
         * por fila). Al terminar re-bindea los formularios con data-confirm porque
         * Confirm.confirmAll() solo bindea elementos existentes.
         */
        function renderEmployeesTable(employees) {
            const body = document.querySelector('[data-employees-rows]');
            if (!body) return;
            if (!employees.length) {
                body.innerHTML = `<tr><td colspan="4">${emptyStateHtml('bi-people', 'Sin empleados en el dispositivo', 'Usa «Usuarios» para vaciar el checador o agrega uno manualmente.')}</td></tr>`;
                return;
            }
            body.innerHTML = employees.map((employee) => `
                <tr>
                    <td data-label="ID"><code>${esc(employee.user_id)}</code></td>
                    <td data-label="UID"><span class="mono text-secondary-token">${esc(employee.uid)}</span></td>
                    <td data-label="Nombre"><span class="avatar is-sm me-2" aria-hidden="true">${esc(String(employee.name ?? '?').charAt(0).toUpperCase())}</span><span class="fw-semibold">${esc(employee.name)}</span></td>
                    <td data-label="">
                        <div class="table-row-actions justify-content-end">
                            <form action="${esc(employee.sync_fingerprint_url)}" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="${esc(csrfToken)}">
                                <input type="hidden" name="employee_id" value="${esc(employee.id)}">
                                <button class="btn btn-sm btn-ghost" title="Extraer huella de ${esc(employee.name)}" aria-label="Extraer huella de ${esc(employee.name)}"><i class="bi bi-fingerprint"></i></button>
                            </form>
                            <form action="${esc(employee.upload_url)}" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="${esc(csrfToken)}">
                                <button class="btn btn-sm btn-ghost" title="Subir huella digital al checador" aria-label="Subir huella"><i class="bi bi-cloud-arrow-up"></i></button>
                            </form>
                            <a href="${esc(employee.edit_url)}" class="btn btn-sm btn-ghost" title="Editar" aria-label="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="${esc(employee.destroy_url)}" method="POST" class="d-inline"
                                  data-confirm
                                  data-confirm-danger
                                  data-confirm-title="¿Quitar a ${esc(employee.name)} del dispositivo?"
                                  data-confirm-message="Se eliminará solo de este checador y perderá sus accesos en él. Esta acción no se puede deshacer.">
                                <input type="hidden" name="_token" value="${esc(csrfToken)}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-icon-danger" title="Eliminar" aria-label="Eliminar"><i class="bi bi-person-x"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>`).join('');
            window.dashConfirmAll?.();
        }

        function renderAttendancesTable(attendances) {
            const body = document.querySelector('[data-attendances-rows]');
            if (!body) return;
            if (!attendances.length) {
                body.innerHTML = `<tr><td colspan="4">${emptyStateHtml('bi-calendar-check', 'Sin asistencias sincronizadas', 'Los chequeos aparecerán aquí cuando traigas las asistencias del checador.')}</td></tr>`;
            } else {
                body.innerHTML = attendances.map((attendance) => `
                    <tr>
                        <td data-label="Fecha y hora"><span class="mono text-secondary-token">${esc(attendance.recorded_at)}</span></td>
                        <td data-label="Empleado">${esc(attendance.employee_name)}</td>
                        <td data-label="ID"><code>${esc(attendance.user_id)}</code></td>
                        <td data-label="Marcado"><span class="badge badge-with-dot ${esc(attendance.state_color)}">${esc(attendance.state_label)}</span></td>
                    </tr>`).join('');
            }
            renderRecentFeed((attendances || []).slice(0, 6));
        }

        function renderFingerprintsTable(fingerprints) {
            const body = document.querySelector('[data-fingerprints-rows]');
            if (!body) return;
            if (!fingerprints.length) {
                body.innerHTML = `<tr><td colspan="4">${emptyStateHtml('bi-fingerprint', 'Sin huellas extraídas', 'Usa «Huellas» o el botón de huella por empleado para extraerlas del checador.')}</td></tr>`;
                return;
            }
            body.innerHTML = fingerprints.map((fingerprint) => `
                <tr>
                    <td data-label="ID usuario"><code>${esc(fingerprint.user_id)}</code></td>
                    <td data-label="Empleado">${esc(fingerprint.employee_name)}</td>
                    <td data-label="Dedo"><span class="mono text-secondary-token">#${esc(fingerprint.finger)}</span></td>
                    <td data-label="Registrada"><span class="mono text-secondary-token">${esc(fingerprint.registered_at)}</span></td>
                </tr>`).join('');
        }

        function renderRecentFeed(items) {
            const feed = document.querySelector('[data-recent-feed]');
            if (!feed) return;
            if (!items.length) {
                feed.innerHTML = emptyStateHtml('bi-calendar-check', 'Sin asistencias recientes', 'Aún no hay chequeos registrados para este checador.');
                return;
            }
            feed.innerHTML = items.map((attendance) => `
                <div class="d-flex align-items-center gap-2 py-2">
                    <span class="avatar is-sm" aria-hidden="true">${esc(String(attendance.employee_name ?? '?').charAt(0).toUpperCase())}</span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">${esc(attendance.employee_name)}</div>
                        <div class="text-tertiary-token mono">ID ${esc(attendance.user_id)}</div>
                    </div>
                    <div class="text-end">
                        <div><span class="badge badge-with-dot ${esc(attendance.state_color)}">${esc(attendance.state_label)}</span></div>
                        <div class="mono text-tertiary-token small">${esc(attendance.recorded_at)}</div>
                    </div>
                </div>`).join('');
        }

        function renderRecentSyncs(syncs) {
            const container = document.querySelector('[data-recent-syncs]');
            if (!container) return;
            if (!syncs.length) {
                container.innerHTML = emptyStateHtml('bi-activity', 'Sin actividad aún', 'Las sincronizaciones que ejecutes aparecerán aquí.');
                return;
            }
            container.innerHTML = syncs.map((sync) => {
                const meta = syncStatusMeta[sync.status] || { color: 'gray', icon: 'bi-clock' };
                const subtitle = (sync.status === 'failed' && sync.error)
                    ? sync.error
                    : `Creados: ${sync.created} · Actualizados: ${sync.updated}`;
                return `
                <div class="d-flex align-items-center gap-3 py-2">
                    <span class="badge badge-with-dot cat-${esc(meta.color)}"><i class="bi ${esc(meta.icon)}"></i></span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Sincronización de ${esc(sync.operation_label)}</div>
                        <div class="text-tertiary-token small">${esc(subtitle)}</div>
                    </div>
                    <span class="mono small text-tertiary-token">${esc(String(sync.finished_at).split(' ').pop() || '—')}</span>
                </div>`;
            }).join('');
        }

        function renderStatCards(counts) {
            const map = { employees: counts.employees, attendances: counts.attendances, fingerprints: counts.fingerprints, status: counts.status_label };
            Object.entries(map).forEach(([key, value]) => {
                const card = document.querySelector(`[data-stat="${key}"]`);
                card?.querySelector('[data-stat-value]') && (card.querySelector('[data-stat-value]').textContent = value);
            });
        }

        /**
         * Refresca KPIs, tablas (empleados/asistencias/huellas), feed reciente y
         * timeline de sincronizaciones tras completar una corrida, sin recargar.
         */
        async function refreshDeviceData() {
            try {
                const response = await fetch('{{ route('devices.refresh-data', $device) }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!response.ok) return;
                const data = await response.json();
                renderStatCards(data.counts);
                renderEmployeesTable(data.employees);
                renderAttendancesTable(data.attendances);
                renderFingerprintsTable(data.fingerprints);
                renderRecentSyncs(data.recent_syncs);
                applySearch();
            } catch (e) {
                console.error('Error refreshing device data:', e);
            }
        }

        function updateProgressUI(data) {
            const running = data.status === 'queued' || data.status === 'running';
            const shouldShow = running || data.status === 'completed' || data.status === 'failed';
            const how = data.operation ? ` (${operations[data.operation] || data.operation})` : '';
            const stageTitle = stages[data.stage] || 'Procesando';
            const processed = Number(data.processed ?? 0);
            const total = Number(data.total ?? 0);

            // Cada nueva corrida debe poder notificar su resultado, si no el
            // flag one-shot silenciaría el toast y el refresh de las siguientes.
            if (running) {
                notifiedResult = null;
            }

            statusBox.classList.toggle('d-none', !shouldShow);
            if (progressBox) {
                progressBox.classList.toggle('d-none', !running);
            }

            label.textContent = (labels[data.status] || data.status) + how;
            detail.textContent = data.error || (running
                ? `${stageTitle} · ${processed} de ${total || '…'}`
                : '');
            statusBox.classList.toggle('alert-danger', data.status === 'failed');
            statusBox.classList.toggle('alert-success', data.status === 'completed');

            const percent = total ? Math.min(100, Math.round((processed / total) * 100)) : 0;
            const done = running ? percent : (data.status === 'completed' ? 100 : 0);

            if (progressBar) {
                progressBar.style.width = `${done}%`;
                progressBar.setAttribute('aria-valuenow', String(done));
                progressBar.classList.toggle('progress-bar-striped', running);
                progressBar.classList.toggle('progress-bar-animated', running);
            }

            if (progressPercentText) {
                progressPercentText.textContent = `${done}%`;
            }

            if (progressStatusText) {
                progressStatusText.textContent = running
                    ? `${stageTitle}${total ? ` · ${processed} de ${total}` : ' · descargando del checador…'}`
                    : (data.status === 'failed' ? (data.error || 'Sincronización detenida') : 'Esperando...');
            }

            if (data.status === 'completed' && notifiedResult !== 'completed') {
                window.dashToast?.({ type: 'success', title: 'Sincronización completada', message: `Creados: ${data.created} · Actualizados: ${data.updated}` });
                notifiedResult = 'completed';
                refreshDeviceData();
            }

            if (data.status === 'failed' && notifiedResult !== 'failed') {
                window.dashToast?.({ type: 'error', title: 'Sincronización fallida', message: data.error || 'Revisa la conexión con el checador.' });
                notifiedResult = 'failed';
            }

            previousStatus = data.status;
        }

        async function fetchSyncStatus() {
            try {
                const response = await fetch(statusBox.dataset.url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!response.ok) return null;
                return await response.json();
            } catch (e) {
                console.error('Polling error:', e);
                return null;
            }
        }

        function startPolling() {
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(async () => {
                const data = await fetchSyncStatus();
                if (!data) return;
                updateProgressUI(data);
                if (data.status !== 'queued' && data.status !== 'running') {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    restoreAllSyncButtons();
                }
            }, 3000);
        }

        /* ─── Pestañas (Bootstrap) y buscador client-side ─── */

        function applySearch() {
            if (!searchInput) return;
            const query = searchInput.value.trim().toLowerCase();
            document.querySelectorAll(`[data-panel="${activePanel}"] tbody tr`).forEach((row) => {
                if (row.querySelector('.empty-state')) return;
                row.classList.toggle('d-none', Boolean(query) && !row.textContent.toLowerCase().includes(query));
            });
        }

        tabButtons.forEach((button) => {
            button.addEventListener('shown.bs.tab', () => {
                // '#tab-empleados' -> panel 'employees'; la pestaña remota no mapea
                // a tabla y deja el buscador sin efecto hasta volver a una con datos.
                const target = String(button.dataset.bsTarget || '');
                activePanel = target.startsWith('#tab-') ? target.replace('#tab-', '') : '';
                // Look temático: sólido = activa, outline = inactiva.
                tabButtons.forEach((b) => {
                    const isActive = b === button;
                    b.classList.toggle('btn-secondary', isActive);
                    b.classList.toggle('btn-outline-secondary', !isActive);
                });
                applySearch();
            });
        });
        searchInput?.addEventListener('input', applySearch);

        /* ─── Botones de sincronización ─── */

        document.querySelectorAll('.sync-btn').forEach((button) => {
            button.addEventListener('click', async () => {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

                const url = button.dataset.url;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const data = await response.json();
                    updateProgressUI(data);

                    if (data.status === 'queued' || data.status === 'running') {
                        startPolling();
                    }
                } catch (e) {
                    console.error('Sync error:', e);
                    window.dashToast?.({ type: 'error', title: 'Error', message: 'Error al iniciar sincronización' });
                } finally {
                    // Si el polling tomó el control, el botón se restaura al llegar
                    // al estado terminal; si no hay polling activo, restaurar ya.
                    if (!pollingInterval) restoreButtonText(button);
                }
            });
        });

        startPolling();
    })();
</script>
@endpush
