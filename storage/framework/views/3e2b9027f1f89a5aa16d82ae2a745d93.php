
<?php
    $baseUrl = $baseUrl ?? route('employees.index');
    $activeParams = $activeParams ?? request()->query();
?>
<div class="col-12 d-flex gap-2 flex-wrap pt-2 border-top">
    <span class="small text-tertiary-token me-1">Rápidos:</span>
    <a href="<?php echo e($baseUrl . '?' . http_build_query(array_merge($activeParams, ['sin_huella' => 1]))); ?>"
       class="ref-chip" aria-label="Filtrar sin huellas">
        <i class="bi bi-fingerprint"></i> Sin huellas
    </a>
    <a href="<?php echo e($baseUrl . '?' . http_build_query(array_merge($activeParams, ['sin_device' => 1]))); ?>"
       class="ref-chip" aria-label="Filtrar sin enrolar">
        <i class="bi bi-hdd-network"></i> Sin enrolar
    </a>
    <a href="<?php echo e(route('employees.sobrantes')); ?>" class="ref-chip" aria-label="Ver sobrantes">
        <i class="bi bi-exclamation-triangle"></i> Sobrantes
    </a>
</div>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/partials/_quick-filters.blade.php ENDPATH**/ ?>