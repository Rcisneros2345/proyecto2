@extends('layouts.admin')

@section('title', 'Editar empleado')
@section('breadcrumb', 'Operación › Empleados › Editar')

@section('content')
@php
    $totalDevicesCount = $totalDevices ?? $employee->devices->count();
    $latestSync = $employee->syncs->first();
    $syncStatusColors = ['completed' => 'green', 'failed' => 'red', 'running' => 'amber', 'queued' => 'gray'];
    $latestSyncColor = $latestSync ? ($syncStatusColors[$latestSync->status] ?? 'gray') : 'gray';
    $latestSyncValue = $latestSync
        ? ucfirst($latestSync->status) . ' · ' . ($latestSync->created_at?->format('d/m H:i') ?? '')
        : '—';
@endphp

<x-page-header title="Editar empleado: {{ $employee->name }}" subtitle="ID {{ $employee->user_id }} · {{ $employee->type_label ?? 'Sin tipo' }}{{ $employee->departamento ? ' · ' . $employee->departamento : '' }}" :hide-title="false">
    @slot('actions')
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endslot
</x-page-header>

<!-- ═══ ZONA DE ESTADO — Resumen visual del empleado ═══ -->
@php
    $stateFpCount = $employee->fingerprints->unique('finger')->count();
    $stateFpColor = $stateFpCount === 0 ? 'gray' : ($stateFpCount < 3 ? 'amber' : 'green');
    $stateDeviceCount = $employee->devices->count();
    $stateDevicesEnrolled = $employee->devices->count();
    $stateHasCard = $employee->devices->contains(fn($d) => filled($d->pivot->card_number));
    $cardNumbers = $employee->devices->filter(fn($d) => filled($d->pivot->card_number))->pluck('card_number')->filter()->values();
    $stateSyncLabel = $latestSync ? ucfirst($latestSync->status) : 'Nunca ejecutada';
    $stateSyncIcon = match($latestSync?->status) {
        'completed' => 'bi-check-circle-fill',
        'failed' => 'bi-x-circle-fill',
        'running', 'queued' => 'bi-hourglass-split',
        default => 'bi-dash-circle',
    };
    $stateSyncColor = match($latestSync?->status) {
        'completed' => 'var(--cat-green)',
        'failed' => 'var(--cat-red)',
        'running', 'queued' => 'var(--cat-amber)',
        default => 'var(--cat-gray)',
    };
@endphp
<div class="device-state-zone mb-4" role="region" aria-label="Estado del empleado">
    {{-- Nombre / Identidad --}}
    <div class="ds-item">
        <span class="ds-label">Nombre</span>
        <span class="ds-value">{{ $employee->name }}</span>
    </div>

    {{-- Estado del empleado --}}
    <div class="ds-item">
        <span class="ds-label">Estado</span>
        <span class="ds-value">
            @if($employee->status_actual === 'B')
                <span style="color: var(--cat-red);">&#x1F534; Baja</span>
            @else
                <span style="color: var(--cat-green);">&#x1F7E2; Activo</span>
            @endif
        </span>
    </div>

    {{-- Huellas --}}
    <div class="ds-item">
        <span class="ds-label">Huellas</span>
        <span class="ds-value">
            @if($stateFpCount > 0)
                <span class="dot-indicator" aria-label="{{ $stateFpCount }} huellas">
                    @for($i = 0; $i < min($stateFpCount, 5); $i++)
                        <span class="dot filled cat-{{ $stateFpColor }}"></span>
                    @endfor
                </span>
                <span class="mono" style="color: var(--cat-{{ $stateFpColor }});">{{ $stateFpCount }}</span>
            @else
                <span class="text-tertiary-token">Sin enrolamiento</span>
            @endif
        </span>
    </div>

    {{-- Enrolamiento en dispositivos --}}
    <div class="ds-item">
        <span class="ds-label">Dispositivos</span>
        <span class="ds-value">
            @if($stateDeviceCount > 0)
                <span class="dot-indicator" aria-label="{{ $stateDeviceCount }} de {{ $totalDevicesCount }} dispositivos">
                    @for($i = 0; $i < min($totalDevicesCount, 5); $i++)
                        <span class="dot {{ $i < $stateDeviceCount ? 'filled cat-blue' : '' }}"></span>
                    @endfor
                </span>
                <span class="mono" style="color: var(--cat-blue);">{{ $stateDeviceCount }}<span class="text-tertiary-token">/{{ $totalDevicesCount }}</span></span>
            @else
                <span class="text-tertiary-token">Sin enrolar</span>
            @endif
        </span>
    </div>

    {{-- Sincronización --}}
    <div class="ds-item">
        <span class="ds-label">Sincronización</span>
        <span class="ds-value">
            <i class="{{ $stateSyncIcon }}" style="color: {{ $stateSyncColor }};"></i>
            <span style="color: {{ $stateSyncColor }};">{{ $stateSyncLabel }}</span>
            @if($latestSync)
                <span class="ds-value mono text-tertiary-token" style="font-size: 12px; font-weight: 400;">
                    {{ $latestSync->finished_at?->format('d/m H:i') ?? $latestSync->created_at?->format('d/m H:i') }}
                </span>
            @endif
        </span>
    </div>

    {{-- Tarjeta RFID --}}
    @if($stateHasCard)
        <div class="ds-item">
            <span class="ds-label">Tarjeta RFID</span>
            <span class="ds-value mono">
                <i class="bi bi-credit-card me-1 text-tertiary-token"></i>
                {{ $cardNumbers->implode(', ') }}
            </span>
        </div>
    @endif
</div>

<!-- KPI Grid (4 cards) -->
<div class="kpi-grid mb-4">
    <x-stat-card icon="bi-fingerprint" :value="$employee->fingerprints->unique('finger')->count()" label="Huellas disponibles" color="purple" />
    <x-stat-card icon="bi-hdd-network" :value="$employee->devices->count() . '/' . $totalDevicesCount" label="Dispositivos enrolados" color="blue" />
    <x-stat-card icon="bi-card-text" :value="$employee->devices->filter(fn($d)=>filled($d->pivot->card_number))->count()" label="Tarjetas asignadas" color="green" />
    <x-stat-card icon="bi-clock" :value="$latestSyncValue" label="Último sync" :color="$latestSyncColor" />
</div>

<!-- Bootstrap Tabs -->
<div class="row g-4 align-items-start">
    <div class="col-12">
        <ul class="nav nav-tabs flex-nowrap overflow-auto" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-identidad-btn" data-bs-toggle="tab" data-bs-target="#pane-identidad" type="button" role="tab" aria-controls="pane-identidad" aria-selected="true">Identidad</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-enrolamientos-btn" data-bs-toggle="tab" data-bs-target="#pane-enrolamientos" type="button" role="tab" aria-controls="pane-enrolamientos" aria-selected="false">Enrolamientos</button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content -->
<div class="row g-4 align-items-start">

    {{-- COLUMNA PRINCIPAL --}}
    <div class="col-12 col-xl-8">
        <div class="tab-content">

            {{-- Tab: Identidad --}}
            <div class="tab-pane fade show active" id="pane-identidad" role="tabpanel" aria-labelledby="tab-identidad-btn">

                {{-- Card A — Identidad --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><i class="bi bi-person me-2 text-tertiary-token"></i>Identidad</h2>
                        <span class="small text-tertiary-token"><i class="bi bi-info-circle me-1"></i>Catálogo central</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employees.update', $employee) }}" method="POST" novalidate data-guard-submit>
                            @csrf @method('PUT')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $employee->name) }}" maxlength="24" required
                                           aria-describedby="name-help" autocomplete="name">
                                    <div id="name-help" class="form-text">Máximo 24 caracteres. Se refleja en todos los checadores tras sincronizar.</div>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="user_id_display" class="form-label">ID / Badge</label>
                                    <input type="text" id="user_id_display" class="form-control" value="{{ $employee->user_id }}" readonly disabled>
                                    <div class="form-text">No editable aquí. Si necesitas cambiar badge, duplica/reenrola (escalar a soporte).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado catálogo</label>
                                    <div class="pt-1">
                                        <x-badge :color="$employee->status_actual === 'B' ? 'gray' : 'green'"
                                                  :label="$employee->status_actual_label ?? ($employee->status_actual === 'B' ? 'Baja' : 'Activo')" />
                                    </div>
                                    <div class="form-text">Bajas se gestionan desde la tabla con confirmación.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="area_id" class="form-label">Área</label>
                                    <select id="area_id" name="area_id" class="form-select @error('area_id') is-invalid @enderror">
                                        <option value="">Sin área</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}" @selected(old('area_id', $employee->area_id) == $area->id)>
                                                {{ $area->identificador }} - {{ $area->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('area_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="puesto_id" class="form-label">Puesto</label>
                                    <select id="puesto_id" name="puesto_id" class="form-select @error('puesto_id') is-invalid @enderror">
                                        <option value="">Sin puesto</option>
                                        @foreach ($puestos as $puesto)
                                            <option value="{{ $puesto->id }}" @selected(old('puesto_id', $employee->puesto_id) == $puesto->id)>
                                                {{ $puesto->identificador }} - {{ $puesto->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('puesto_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="auth_user_id" class="form-label">Usuario de acceso</label>
                                    <select id="auth_user_id" name="auth_user_id" class="form-select @error('auth_user_id') is-invalid @enderror">
                                        <option value="">Sin usuario vinculado</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" @selected(old('auth_user_id', $employee->auth_user_id) == $user->id)
                                            >{{ $user->name }}{{ $user->username ? ' · ' . $user->username : '' }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Este vínculo determina la identidad para permisos e incidencias.</div>
                                    @error('auth_user_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-save me-1"></i> Guardar identidad</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Card B — Credenciales --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><i class="bi bi-shield-lock me-2 text-tertiary-token"></i>Credenciales de acceso</h2>
                        <x-badge color="amber" icon="bi-exclamation-circle" :label="'Propaga a ' . ($employee->devices->count() ?: '—') . ' checadores al sincronizar'" />
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employees.update', $employee) }}" method="POST" id="employee-credentials-form" novalidate data-guard-submit>
                            @csrf @method('PUT')
                            <input type="hidden" name="name" value="{{ old('name', $employee->name) }}" data-sync-name>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">PIN / Contraseña</label>
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           maxlength="8" inputmode="numeric" pattern="[0-9]{1,8}"
                                           placeholder="Conservar actual" aria-describedby="password-help" autocomplete="off">
                                    <div id="password-help" class="form-text">Solo números, 1–8 dígitos. Vacío = no cambia. Se aplica al sincronizar.</div>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Rol en checador</label>
                                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" aria-describedby="role-help">
                                        @php $currentRole = old('role', $employee->devices->first()?->pivot->role ?? 0); @endphp
                                        @foreach(\App\Models\Employee::roles() as $value => $label)
                                            <option value="{{ $value }}" @selected($currentRole == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div id="role-help" class="form-text">0 = Usuario · 13 = Supervisor · 14 = Admin.</div>
                                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="alert alert-info py-2 small mt-3 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Nombre, PIN y rol se guardan en el catálogo y se <strong>propagan a los {{ $employee->devices->count() }} checador(es) enrolados</strong> solo al ejecutar "Sincronizar". Si no está enrolado, quedan solo en catálogo.
                            </div>
                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            {{-- Tab: Enrolamientos --}}
            <div class="tab-pane fade" id="pane-enrolamientos" role="tabpanel" aria-labelledby="tab-enrolamientos-btn">

                {{-- Card C — Enrolamientos --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><i class="bi bi-hdd-network me-2 text-tertiary-token"></i>Dispositivos enrolados · {{ $employee->devices->count() }}</h2>
                        @if($employee->devices->isNotEmpty())
                            <span class="small text-tertiary-token">Tarjeta por checador</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($employee->devices->isNotEmpty())
                            <div class="table-responsive mb-3">
                                <table class="table table-hover align-middle mb-0 table-cards" aria-label="Resumen de enrolamientos por dispositivo">
                                    <thead>
                                        <tr>
                                            <th style="min-width:180px" scope="col">Dispositivo</th>
                                            <th style="min-width:70px" scope="col">UID</th>
                                            <th style="min-width:110px" scope="col">Rol</th>
                                            <th style="min-width:110px" scope="col">Tarjeta</th>
                                            <th style="min-width:80px" scope="col">Huellas</th>
                                            <th style="min-width:90px" scope="col">Activo</th>
                                            <th style="min-width:130px" scope="col">Sync</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->devices as $device)
                                            @php
                                                $fpN = $device->pivot->fingerprint_count ?? 0;
                                                $devSync = $employee->syncs->firstWhere('device_id', $device->id);
                                                $devSyncColor = $devSync ? (['completed' => 'green', 'failed' => 'red', 'running' => 'amber', 'queued' => 'gray'][$devSync->status] ?? 'gray') : 'gray';
                                            @endphp
                                            <tr>
                                                <td data-label="Dispositivo">
                                                    <a href="{{ route('devices.show', $device) }}" class="ref-chip" title="Ver {{ $device->name }}">
                                                        <i class="bi bi-hdd-network"></i>{{ $device->name }}
                                                    </a>
                                                </td>
                                                <td data-label="UID"><span class="mono small">{{ $device->pivot->device_uid }}</span></td>
                                                <td data-label="Rol"><x-badge color="gray" :label="$device->pivot->roleLabel()" /></td>
                                                <td data-label="Tarjeta">
                                                    @if(filled($device->pivot->card_number))
                                                        <span class="mono small">{{ $device->pivot->card_number }}</span>
                                                    @else
                                                        <span class="small text-tertiary-token">—</span>
                                                    @endif
                                                </td>
                                                <td data-label="Huellas">
                                                    @include('employees.partials._fingerprint-badge', ['fpCount' => $fpN])
                                                </td>
                                                <td data-label="Activo">
                                                    @if($device->pivot->active)
                                                        <x-badge color="green" label="Sí" />
                                                    @else
                                                        <x-badge color="gray" label="No" />
                                                    @endif
                                                </td>
                                                <td data-label="Sync">
                                                    @if($devSync)
                                                        <x-badge :color="$devSyncColor" :label="$devSync->status" />
                                                        <span class="mono small text-tertiary-token">{{ $devSync->created_at?->format('d/m H:i') }}</span>
                                                    @else
                                                        <span class="small text-tertiary-token">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <h3 class="h6 small text-tertiary-token text-uppercase mb-2">Tarjeta por checador</h3>
                        @endif
                        @forelse($employee->devices as $device)
                            <div class="employee-device-entry">
                                <div class="employee-device-meta">
                                    <a href="{{ route('devices.show', $device) }}" class="ref-chip" title="UID {{ $device->pivot->device_uid }} en {{ $device->name }}">
                                        <i class="bi bi-hdd-network"></i>{{ $device->name }}
                                    </a>
                                    <span class="mono text-secondary-token small">UID {{ $device->pivot->device_uid }}</span>
                                </div>
                                <form action="{{ route('employees.update-card', $employee) }}" method="POST" class="mt-2" novalidate data-guard-submit>
                                    @csrf
                                    <input type="hidden" name="device_id" value="{{ $device->id }}">
                                    <label class="form-label small mb-1" for="card-{{ $device->id }}">Código de tarjeta</label>
                                    <div class="input-group" style="max-width: 380px">
                                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                        <input type="text" id="card-{{ $device->id }}" name="card_number"
                                               class="form-control @error('card_number') is-invalid @enderror"
                                               value="{{ old('card_number', $device->pivot->card_number) }}"
                                               inputmode="numeric" maxlength="10" pattern="[0-9]+"
                                               placeholder="Sin tarjeta" aria-label="Código de tarjeta para {{ $device->name }}">
                                        <button class="btn btn-outline-primary" type="submit">Guardar</button>
                                    </div>
                                    @error('card_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="form-text">Solo números, máx. 10 dígitos.</div>
                                </form>
                            </div>
                        @empty
                            <div class="alert alert-warning py-2 small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Este empleado no está enrolado en ningún checador; los cambios se guardarán solo en el catálogo.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Card D — Distribuir --}}
                @if($syncDevices->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2">
                        <h2 class="h6 mb-0"><i class="bi bi-send me-2 text-tertiary-token"></i>Enviar a dispositivos</h2>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-select-all-devices>Seleccionar todos</button>
                    </div>
                    <div class="card-body">
                        @include('employees.partials._sync-devices-form', [
                            'employee' => $employee,
                            'syncDevices' => $syncDevices,
                            'formId' => 'sync-devices-form',
                        ])
                    </div>
                </div>
                @endif

                @include('employees.partials._sync-progress-panel')

            </div>

        </div>
    </div>

    {{-- COLUMNA LATERAL (Aside) --}}
    <div class="col-12 col-xl-4">
        {{-- CARD E — Huellas --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0"><i class="bi bi-fingerprint me-2"></i>Huellas guardadas</h2>
                @php
                    $asideFpCount = $employee->fingerprints->count();
                @endphp
                @include('employees.partials._fingerprint-badge', ['fpCount' => $asideFpCount, 'fpMax' => $employee->fingerprints->unique('finger')->count() ?: null])
            </div>
            <div class="card-body">
                @forelse($employee->fingerprints as $fingerprint)
                    <div class="employee-fingerprint-row">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar is-sm" style="background: var(--primary-soft); color: var(--primary)"><i class="bi bi-fingerprint"></i></span>
                                <div>
                                    <div class="fw-semibold small">Dedo {{ $fingerprint->finger }}</div>
                                    <span class="badge cat-gray">{{ $fingerprint->device?->name ?? 'Origen desconocido' }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @if($fingerprint->device_id && $employee->devices->count() > 1)
                                    <form action="{{ route('employees.copy-fingerprint', [$employee, $fingerprint]) }}" method="POST" class="d-flex gap-1 flex-grow-1" style="max-width: 260px">
                                        @csrf
                                        <select name="target_device_id" class="form-select form-select-sm" aria-label="Checador destino para dedo {{ $fingerprint->finger }}" required>
                                            <option value="">Copiar a…</option>
                                            @foreach($employee->devices as $targetDevice)
                                                @if($targetDevice->id !== $fingerprint->device_id)
                                                    <option value="{{ $targetDevice->id }}">{{ $targetDevice->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="submit" title="Copiar huella y conservar original" aria-label="Copiar huella y conservar original"><i class="bi bi-copy"></i></button>
                                    </form>
                                @endif
                                <form action="{{ route('employees.delete-fingerprint', [$employee, $fingerprint]) }}" method="POST"
                                      data-confirm data-confirm-danger
                                      data-confirm-title="¿Quitar esta huella?"
                                      data-confirm-message="Se eliminará el dedo {{ $fingerprint->finger }} del empleado y del checador de origen.">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon-danger" type="submit" title="Quitar huella"><i class="bi bi-trash me-1"></i>Quitar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    @include('partials.empty-state', [
                        'icon' => 'bi-fingerprint',
                        'title' => 'Sin huellas sincronizadas',
                        'desc' => 'Este empleado no tiene huellas. Extrae desde el checador o enrola biométricamente.',
                    ])
                @endforelse

                @if(auth()->user()->isAdmin() && $employee->devices->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @foreach($employee->devices as $device)
                            <form action="{{ route('devices.sync-fingerprints', $device) }}" method="POST">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <button class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-repeat me-1"></i> Actualizar desde {{ $device->name }}</button>
                            </form>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD F — Historial --}}
        @if($employee->syncs->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h6 mb-0"><i class="bi bi-clock-history me-2 text-tertiary-token"></i>Historial de sincronización</h2>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($employee->syncs as $sync)
                        @php $syncColor = ['completed'=>'green','failed'=>'red','running'=>'amber','queued'=>'gray'][$sync->status] ?? 'gray'; @endphp
                        <div class="list-group-item d-flex align-items-center gap-3 py-3">
                            <x-badge :color="$syncColor" :label="strtoupper(substr($sync->status,0,1))" />
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small text-truncate">{{ $sync->device?->name ?? 'Dispositivo eliminado' }} · {{ $sync->operation_label }}</div>
                                <div class="small text-tertiary-token text-truncate">{{ $sync->stage }} · {{ $sync->created_at?->format('d/m/Y H:i') }}@if($sync->error_message) · {{ $sync->error_message }}@endif</div>
                            </div>
                            <span class="mono small text-secondary-token">{{ $sync->processed }}/{{ $sync->total }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-transparent text-center py-2">
                    <a href="{{ route('operations.queue') }}" class="btn btn-sm btn-ghost">Ver cola completa <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        @endif

        {{-- CARD G — Zona de riesgo --}}
        <div class="card shadow-sm border" style="border-color: var(--border) !important;">
            <div class="card-body">
                <h3 class="h6 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Zona de riesgo</h3>
                <p class="small text-secondary-token mb-3">Dar de baja elimina accesos en todos los checadores. Se conserva histórico de checadas.</p>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                      data-confirm data-confirm-danger
                      data-confirm-title="¿Quitar a {{ $employee->name }}?"
                      data-confirm-message="Se dará de baja en todos sus checadores y, si no queda enrolado en ninguno, también del catálogo. Sus checadas históricas se conservan.">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-person-x me-1"></i> Dar de baja empleado</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('employees.partials._sync-preview-drawer')
@endsection

@push('scripts')
<script>
// Sincronizar input name visible con hidden de credenciales
document.getElementById('name')?.addEventListener('input', e => {
    const h = document.querySelector('[data-sync-name]');
    if (h) h.value = e.target.value;
});
document.querySelector('[data-select-all-devices]')?.addEventListener('click', (event) => {
    const cbs = document.querySelectorAll('.employee-device-select input[type="checkbox"]');
    const anyUnchecked = [...cbs].some(cb => !cb.checked);
    cbs.forEach(cb => cb.checked = anyUnchecked);
    event.currentTarget.textContent = anyUnchecked ? 'Quitar selección' : 'Seleccionar todos';
});
// Spinner on submit para sync forms + guardado (update/update-card)
document.querySelectorAll('form[action*="/sync-"], form[data-guard-submit]').forEach(form => {
    form.addEventListener('submit', () => {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Procesando…';
    });
});

// ── Preview diff antes de sincronizar (abre drawer, single fetch) ──
(function initSyncPreview() {
    const syncForm = document.getElementById('sync-devices-form');
    const offcanvasEl = document.getElementById('syncDiffOffcanvas');
    if (!syncForm || !offcanvasEl || typeof bootstrap === 'undefined') return;
    const diffTable = document.getElementById('sync-diff-table');
    const diffLoading = document.getElementById('sync-diff-loading');
    const diffError = document.getElementById('sync-diff-error');
    const confirmIds = document.getElementById('sync-confirm-ids');
    const confirmBtn = document.getElementById('sync-confirm-btn');
    const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
    const DOT = { create: 'cat-blue', update: 'cat-amber', noop: 'cat-gray' };
    const LABEL = { create: 'Alta', update: 'Actualizar', noop: 'Sin cambios' };
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));

    syncForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const ids = [...syncForm.querySelectorAll('input[name="device_ids[]"]:checked')].map((cb) => cb.value);
        if (!ids.length) {
            window.dashToast?.({ type: 'warning', title: 'Sin checadores', message: 'Selecciona al menos un checador destino.' });
            return;
        }
        diffTable.innerHTML = '';
        diffError.style.display = 'none';
        diffLoading.style.display = '';
        confirmBtn.disabled = true;
        offcanvas.show();
        try {
            const url = new URL(syncForm.dataset.diffUrl, window.location.origin);
            ids.forEach((id) => url.searchParams.append('device_ids[]', id));
            const resp = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();
            const items = data.diff || [];
            diffTable.innerHTML = items.length ? items.map((item) => {
                const changed = (item.changes || []).filter((c) => c.status === 'Cambia');
                const list = changed.length
                    ? '<ul class="mb-0 ps-3 small">' + changed.map((c) => `<li><strong>${esc(c.field)}</strong>: ${esc(c.from)} → ${esc(c.to)}</li>`).join('') + '</ul>'
                    : '<span class="small text-tertiary-token">Sin cambios</span>';
                const warn = (item.warnings || []).includes('card_duplicate')
                    ? '<div class="small text-warning mt-1"><i class="bi bi-exclamation-triangle me-1"></i>Tarjeta duplicada en este checador</div>'
                    : '';
                return `<tr><td class="small">${esc(item.device_name)}</td><td><span class="badge ${DOT[item.action] || 'cat-gray'}">${LABEL[item.action] || esc(item.action)}</span></td><td>${list}${warn}</td></tr>`;
            }).join('') : '<tr><td colspan="3" class="small text-tertiary-token">Sin diferencias.</td></tr>';
            confirmIds.innerHTML = ids.map((id) => `<input type="hidden" name="device_ids[]" value="${esc(id)}">`).join('');
            confirmBtn.disabled = false;
        } catch (err) {
            diffError.style.display = '';
        } finally {
            diffLoading.style.display = 'none';
        }
    });
})();

// ── Progreso de sincronización (polling 3s, patrón devices/show) ──
(function initSyncProgress() {
    const panel = document.getElementById('sync-progress-panel');
    if (!panel || panel.style.display === 'none') return; // sin syncs activos: cero requests
    const list = document.getElementById('sync-progress-list');
    const done = document.getElementById('sync-progress-done');
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
    let timer = null;

    async function tick() {
        let data = null;
        try {
            const resp = await fetch(panel.dataset.url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!resp.ok) return;
            data = await resp.json();
        } catch (e) { return; } // reintenta en el siguiente ciclo
        const syncs = (data.syncs || []).slice(0, 6);
        list.innerHTML = syncs.map((s) => {
            const pct = s.total > 0 ? Math.min(100, Math.round((s.processed / s.total) * 100)) : 5;
            const badge = s.is_terminal ? (s.status === 'completed' ? 'cat-green' : 'cat-red') : 'cat-amber';
            const err = (!s.is_terminal || s.status !== 'failed') || !s.error_message ? '' : `<div class="small text-danger mt-1">${esc(s.error_message)}</div>`;
            return `<div class="list-group-item"><div class="d-flex justify-content-between align-items-center gap-2 mb-1">`
                + `<span class="fw-semibold small">${esc(s.device_name)}</span><span class="badge ${badge}">${esc(s.status)}</span></div>`
                + `<div class="progress" style="height: 6px;" role="progressbar" aria-label="Progreso en ${esc(s.device_name)}"><div class="progress-bar" style="width: ${pct}%"></div></div>`
                + `<div class="small text-tertiary-token mt-1">${esc(s.stage)} · ${s.processed}/${s.total}</div>${err}</div>`;
        }).join('');
        if (syncs.length && syncs.every((s) => s.is_terminal)) {
            clearInterval(timer);
            timer = null;
            syncs.forEach((s) => {
                if (s.status === 'completed') window.dashToast?.({ type: 'success', title: 'Sincronizado', message: s.device_name });
                else if (s.status === 'failed') window.dashToast?.({ type: 'error', title: 'Falló sincronización', message: (s.device_name + (s.error_message ? ': ' + s.error_message : '')) });
            });
            done.style.display = '';
        }
    }

    timer = setInterval(tick, 3000);
    tick();
})();
</script>
@endpush