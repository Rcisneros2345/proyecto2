<!-- Partial: employees/partials/_sync-progress-panel.blade.php
     Panel de progreso por dispositivo (polling 3s vía JS inline de edit).
     Se muestra solo cuando hay sincronizaciones no terminales.
     Variables: $employee (con relación syncs cargada).
-->
@php
    $activeSyncs = $employee->syncs->filter(fn ($s) => in_array($s->status, ['queued', 'running'], true))->values();
@endphp
<div class="card shadow-sm mb-4" id="sync-progress-panel"
     data-url="{{ route('employees.sync-progress', $employee) }}"
     aria-live="polite"
     style="{{ $activeSyncs->isEmpty() ? 'display: none;' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-0"><i class="bi bi-arrow-repeat me-2 text-tertiary-token"></i>Sincronización en curso</h2>
        <span class="spinner-border spinner-border-sm text-tertiary-token" role="status" aria-hidden="true"></span>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush" id="sync-progress-list">
            @foreach($activeSyncs as $sync)
                <div class="list-group-item" data-device-row="{{ $sync->device_id }}">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="fw-semibold small">{{ $sync->device?->name ?? 'Dispositivo eliminado' }}</span>
                        <span class="badge cat-amber" data-progress-status>{{ $sync->status }}</span>
                    </div>
                    <div class="progress" style="height: 6px;" role="progressbar" aria-label="Progreso en {{ $sync->device?->name }}">
                        <div class="progress-bar" data-progress-bar style="width: 5%"></div>
                    </div>
                    <div class="small text-tertiary-token mt-1" data-progress-stage>{{ $sync->stage }}</div>
                </div>
            @endforeach
        </div>
        <div class="p-3 text-center" id="sync-progress-done" style="display: none;">
            <p class="small text-secondary-token mb-2">Sincronización terminada.</p>
            <button type="button" class="btn btn-sm btn-primary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar vista
            </button>
        </div>
    </div>
</div>
