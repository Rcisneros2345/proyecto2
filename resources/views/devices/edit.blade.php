@extends('layouts.admin')

@section('title', "Editar {$device->name}")
@section('breadcrumb', 'Operación › Dispositivos › Editar')

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
@endphp

@section('content')
<x-page-header title="Editar {{ $device->name }}" subtitle="Actualiza la configuración de conexión del dispositivo." :hide-title="false">
    @slot('actions')
        <a href="{{ route('devices.show', $device) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endslot
</x-page-header>

{{-- [UI ONLY] State zone: visual snapshot of the device's current operating state --}}
<div class="device-state-zone" role="region" aria-label="Estado actual del dispositivo {{ $device->name }}">
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-hdd-network me-1" aria-hidden="true"></i>Dispositivo</span>
        <span class="ds-value">{{ $device->name }}</span>
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
    @if ($device->latestSync?->finished_at)
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Última sincronización</span>
        <span class="ds-value">{{ $device->latestSync->finished_at->format('d/m/Y H:i') }}</span>
    </div>
    @endif
</div>

<div class="card shadow-sm col-lg-6">
    <div class="card-body">
        <form action="{{ route('devices.update', $device) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- Section: Identificación --}}
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Identificación del dispositivo</legend>

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                           value="{{ old('name', $device->name) }}" required aria-required="true">
                    @error('name') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                </div>
            </fieldset>

            {{-- Section: Conexión --}}
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Datos de conexión</legend>

                <div class="mb-3">
                    <label for="ip" class="form-label">Dirección IP <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                    <input type="text" class="form-control @error('ip') is-invalid @enderror" id="ip" name="ip"
                           value="{{ old('ip', $device->ip ?? env('ZKTECO_DEFAULT_IP')) }}" required aria-required="true">
                    <div class="form-text">IP actual: <code>{{ $device->ip ?? env('ZKTECO_DEFAULT_IP', 'No configurado') }}</code></div>
                    @error('ip') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="port" class="form-label">Puerto <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                        <input type="number" class="form-control @error('port') is-invalid @enderror" id="port" name="port"
                               value="{{ old('port', $device->port) }}" required aria-required="true">
                        @error('port') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label for="password" class="form-label">Contraseña / CLAVE</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                               name="password" value="{{ old('password', $device->password) }}">
                        @error('password') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                        <div class="form-text">Dejar vacío para mantener la actual.</div>
                    </div>
                </div>
            </fieldset>

            {{-- Section: Información adicional --}}
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Información adicional</legend>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                              name="description" rows="2">{{ old('description', $device->description) }}</textarea>
                    @error('description') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                </div>
            </fieldset>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Actualizar configuración
                </button>
                <a href="{{ route('devices.show', $device) }}" class="btn btn-link">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
