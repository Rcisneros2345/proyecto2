@extends('layouts.admin')

@section('title', 'Dispositivos')
@section('breadcrumb', 'Operación › Dispositivos')

@section('content')
<x-page-header title="Dispositivos" subtitle="Supervisa la red biométrica y sus registros desde un solo lugar." :hide-title="false">
    @slot('actions')
        @if (auth()->user()->canAccessModule('dispositivos', 'sync'))
            <form action="{{ route('devices.deduplicate') }}" method="POST"
                  data-confirm
                  data-confirm-danger
                  data-confirm-type="ELIMINAR"
                  data-confirm-title="¿Eliminar registros duplicados?"
                  data-confirm-message="Se conservará el registro más antiguo de cada empleado y asistencia repetida. Las huellas y relaciones se conservarán cuando sea posible. Escribe ELIMINAR para confirmar.">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-funnel me-1"></i> Limpiar duplicados
                </button>
            </form>
        @endif
        @if (auth()->user()->canAccessModule('dispositivos', 'create'))
            <a href="{{ route('devices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Registrar dispositivo
            </a>
        @endif
    @endslot
</x-page-header>

{{-- KPI Summary — aria-live announces changes from polling --}}
<div class="kpi-grid" aria-live="polite" aria-label="Resumen operativo de dispositivos">
    <x-stat-card icon="bi-hdd-network" :value="$stats['devices']" label="Checadores registrados" color="teal">
        <div class="kpi-spark">
            @include('partials.sparkline', ['points' => $spark['devices'], 'color' => 'var(--primary)', 'width' => 84, 'height' => 28])
        </div>
    </x-stat-card>

    <x-stat-card icon="bi-wifi" :value="$stats['online']" label="En línea ahora" color="green">
        <div class="kpi-trend flat"><span class="text-tertiary-token" style="font-weight:500">{{ (int) round(($stats['online'] / max(1, $stats['devices'])) * 100) }}% de la red</span></div>
    </x-stat-card>

    <x-stat-card icon="bi-people" :value="$stats['employees']" label="Empleados sincronizados" color="purple">
        <div class="kpi-spark">
            @include('partials.sparkline', ['points' => $spark['employees'], 'color' => 'var(--cat-purple)', 'width' => 84, 'height' => 28])
        </div>
    </x-stat-card>

    <x-stat-card icon="bi-calendar-check" :value="$stats['attendances']" label="Checadas almacenadas" color="blue">
        <div class="kpi-spark">
            @include('partials.sparkline', ['points' => $spark['attendances'], 'color' => 'var(--cat-blue)', 'width' => 84, 'height' => 28])
        </div>
    </x-stat-card>
</div>

<div class="section-heading">
    <h2>Dispositivos de la red</h2>
    <span class="text-muted small">{{ $stats['fingerprints'] }} huellas protegidas</span>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-cards" aria-label="Listado de dispositivos biométricos">
                <thead>
                    <tr>
                        <th>Dispositivo</th>
                        <th>Conexión</th>
                        <th>Estado</th>
                        <th class="num-cell">Empleados</th>
                        <th class="num-cell">Registros</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($devices as $device)
                        @php
                            // Attention priority: offline > no employees > unknown > online
                            $rowClass = '';
                            if ($device->status === 'offline') {
                                $rowClass = 'device-row-offline';
                            } elseif ($device->employees_count === 0) {
                                $rowClass = 'device-row-no-employees';
                            }

                            // Badge mapping: icon + text, never just color
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
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td data-label="Dispositivo">
                                <a href="{{ route('devices.show', $device) }}" class="fw-semibold text-decoration-none">{{ $device->name }}</a>
                                @if ($device->device_name)
                                    <div class="small text-muted">{{ $device->device_name }}</div>
                                @endif
                            </td>
                            <td data-label="Conexión">
                                <code>{{ $device->ip }}</code>
                                <span class="text-tertiary-token mx-1">:</span>
                                <span class="mono text-secondary-token">{{ $device->port }}</span>
                            </td>
                            <td data-label="Estado">
                                <x-badge :color="$statusColor" dot icon="{{ $statusIcon }}" :label="$statusLabel" size="sm" />
                            </td>
                            <td data-label="Empleados" class="num-cell">
                                @if ($device->employees_count === 0)
                                    <span class="text-secondary-token" title="Sin empleados enrolados">
                                        <i class="bi bi-exclamation-triangle text-tertiary-token me-1" aria-hidden="true"></i>0
                                    </span>
                                @else
                                    <span class="mono">{{ $device->employees_count }}</span>
                                @endif
                            </td>
                            <td data-label="Registros" class="num-cell mono">{{ $device->attendances_count }}</td>
                            <td data-label="">
                                <div class="table-row-actions justify-content-end">
                                    <a href="{{ route('devices.show', $device) }}" class="btn btn-sm btn-ghost" title="Ver detalle de {{ $device->name }}" aria-label="Ver detalle de {{ $device->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (auth()->user()->canAccessModule('dispositivos', 'sync'))
                                        <form action="{{ route('devices.sync-attendances', $device) }}" method="POST" class="d-inline" data-sync>
                                            @csrf
                                            <button class="btn btn-sm btn-ghost" title="Sincronizar asistencias de {{ $device->name }}" aria-label="Sincronizar asistencias de {{ $device->name }}"><i class="bi bi-calendar-plus"></i></button>
                                        </form>
                                    @endif
                                    @if (auth()->user()->canAccessModule('dispositivos', 'update'))
                                        <a href="{{ route('devices.edit', $device) }}" class="btn btn-sm btn-ghost" title="Editar {{ $device->name }}" aria-label="Editar {{ $device->name }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if (auth()->user()->canAccessModule('dispositivos', 'delete'))
                                        <form action="{{ route('devices.destroy', $device) }}" method="POST" class="d-inline"
                                              data-confirm
                                              data-confirm-danger
                                              data-confirm-type="ELIMINAR"
                                              data-confirm-title="¿Eliminar dispositivo de la red?"
                                              data-confirm-message="Se eliminará «{{ $device->name }}» junto con sus empleados, huellas y registros asociados. Esta acción no se puede deshacer. Escribe ELIMINAR para confirmar.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-icon-danger" title="Eliminar {{ $device->name }}" aria-label="Eliminar {{ $device->name }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('partials.empty-state', [
                                    'icon'     => 'bi-hdd-network',
                                    'title'    => 'No hay checadores registrados aún',
                                    'desc'     => 'Los dispositivos aparecerán aquí cuando se registren en la red.',
                                    'cta'      => auth()->user()->canAccessModule('dispositivos', 'create')
                                        ? ['label' => 'Registrar primer dispositivo', 'url' => route('devices.create')]
                                        : null,
                                    'ctaLink'  => true,
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $devices->links() }}</div>
@endsection
