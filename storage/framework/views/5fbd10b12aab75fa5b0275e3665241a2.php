<!-- Partial: employees/partials/_sync-preview-drawer.blade.php
     Drawer lateral derecho para previsualizar el diff antes de sincronizar.
     Lo llena el JS de la seccion scripts de edit: tbody#sync-diff-table.
     Sin JS inline aquí; solo markup + hooks data-*.
-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="syncDiffOffcanvas" aria-labelledby="syncDiffOffcanvasLabel" style="width: 480px; max-width: 100vw;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="syncDiffOffcanvasLabel">Previsualizar sincronización</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <p class="small text-secondary-token mb-3">Esto se enviará a cada checador. Revisa antes de confirmar.</p>
        <div id="sync-diff-loading" class="text-center py-4" style="display: none;">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            <span class="small">Comparando con cada checador…</span>
        </div>
        <div id="sync-diff-error" class="alert alert-warning py-2 small" style="display: none;" role="alert">
            No se pudo comparar. Revisa tu conexión e inténtalo de nuevo.
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" aria-label="Diferencias por dispositivo">
                <thead>
                    <tr>
                        <th scope="col">Dispositivo</th>
                        <th scope="col">Acción</th>
                        <th scope="col">Cambios</th>
                    </tr>
                </thead>
                <tbody id="sync-diff-table"></tbody>
            </table>
        </div>
        <div class="alert alert-secondary py-2 small mt-3 mb-0">
            <i class="bi bi-shield-lock me-1"></i>
            El PIN nunca se muestra: verás <strong>Cambia</strong> o <strong>Igual</strong>.
        </div>
    </div>
    <div class="offcanvas-footer d-flex justify-content-between align-items-center border-top p-3">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
        <form id="sync-confirm-form" action="<?php echo e(route('employees.sync-devices', $employee)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <span id="sync-confirm-ids"></span>
            <button type="submit" class="btn btn-primary" id="sync-confirm-btn" disabled>
                <i class="bi bi-send me-1"></i> Confirmar y sincronizar
            </button>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/partials/_sync-preview-drawer.blade.php ENDPATH**/ ?>