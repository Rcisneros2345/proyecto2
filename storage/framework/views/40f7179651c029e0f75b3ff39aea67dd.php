<?php
    $ciclo = $ciclo ?? app('App\Services\CicloActualService')->resolve(request());
    $summary = app('App\Services\AcademiaDashboardService')->build($ciclo);
    $materiasCount = $summary['kpis']['materias'] ?? 0;
?>

<?php if($materiasCount > 0): ?>
  <a href="<?php echo e(route('academia.materias.index')); ?>" class="card card-link h-100 p-3 text-decoration-none" style="border-left:3px solid var(--cat-amber);"
     aria-label="Materias: <?php echo e($materiasCount); ?> registros, Materias del ciclo">
    <div class="d-flex align-items-start gap-3">
      <span class="kpi-icon amber" style="width:42px;height:42px;flex:0 0 42px"><i class="bi bi-journal-bookmark"></i></span>
      <div class="flex-grow-1 min-w-0">
        <div class="fw-bold" style="font-size:13px">Materias</div>
        <div class="d-flex align-items-baseline gap-2">
          <span class="fw-bold" style="font-family:'JetBrains Mono',monospace;font-size:22px" data-mod-ciclo="Materias"><?php echo e($materiasCount); ?></span>
          <?php if($materiasCount !== null): ?>
            <span class="small text-tertiary-token">en este ciclo</span>
          <?php endif; ?>
        </div>
        <div class="small text-secondary-token mt-1">Ver y gestionar materias <i class="bi bi-arrow-right ms-1"></i></div>
      </div>
    </div>
  </a>
<?php else: ?>
  <a href="#" class="card card-link h-100 p-3 text-decoration-none opacity-50" aria-label="Materias: Sin datos">
    <div class="d-flex align-items-start gap-3">
      <span class="kpi-icon" style="width:42px;height:42px;flex:0 0 42px"><i class="bi bi-journal-bookmark"></i></span>
      <div class="flex-grow-1 min-w-0">
        <div class="fw-bold" style="font-size:13px">Materias</div>
        <div class="small text-tertiary-text">Sin materias en este ciclo</div>
      </div>
    </div>
  </a>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/academia/dashboard/_materias-module.blade.php ENDPATH**/ ?>