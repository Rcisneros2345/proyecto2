<!-- Partial: employees/partials/_sync-progress-panel.blade.php
     Panel de progreso por dispositivo (polling 3s vía JS inline de edit).
     Se muestra solo cuando hay sincronizaciones no terminales.
     Variables: $employee (con relación syncs cargada).
-->
<?php
    $activeSyncs = $employee->syncs->filter(fn ($s) => in_array($s->status, ['queued', 'running'], true))->values();
?>
<div class="card shadow-sm mb-4" id="sync-progress-panel"
     data-url="<?php echo e(route('employees.sync-progress', $employee)); ?>"
     aria-live="polite"
     style="<?php echo e($activeSyncs->isEmpty() ? 'display: none;' : ''); ?>">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-0"><i class="bi bi-arrow-repeat me-2 text-tertiary-token"></i>Sincronización en curso</h2>
        <span class="spinner-border spinner-border-sm text-tertiary-token" role="status" aria-hidden="true"></span>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush" id="sync-progress-list">
            <?php $__currentLoopData = $activeSyncs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sync): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-group-item" data-device-row="<?php echo e($sync->device_id); ?>">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="fw-semibold small"><?php echo e($sync->device?->name ?? 'Dispositivo eliminado'); ?></span>
                        <span class="badge cat-amber" data-progress-status><?php echo e($sync->status); ?></span>
                    </div>
                    <div class="progress" style="height: 6px;" role="progressbar" aria-label="Progreso en <?php echo e($sync->device?->name); ?>">
                        <div class="progress-bar" data-progress-bar style="width: 5%"></div>
                    </div>
                    <div class="small text-tertiary-token mt-1" data-progress-stage><?php echo e($sync->stage); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="p-3 text-center" id="sync-progress-done" style="display: none;">
            <p class="small text-secondary-token mb-2">Sincronización terminada.</p>
            <button type="button" class="btn btn-sm btn-primary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar vista
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/partials/_sync-progress-panel.blade.php ENDPATH**/ ?>