
<?php
    $fpCount = $fpCount ?? 0;
    $fpMax = $fpMax ?? null;
    $fpColor = $fpCount === 0 ? 'gray' : ($fpCount < 3 ? 'amber' : 'green');
?>
<span class="badge cat-<?php echo e($fpColor); ?>"
      title="<?php echo e($fpCount); ?> huella<?php echo e($fpCount !== 1 ? 's' : ''); ?> guardada<?php echo e($fpCount !== 1 ? 's' : ''); ?>">
    
    <?php if($fpMax !== null && $fpMax > 0): ?>
        <span class="dot-indicator" aria-hidden="true">
            <?php for($i = 0; $i < min($fpMax, 5); $i++): ?>
                <span class="dot <?php echo e($i < $fpCount ? "filled cat-{$fpColor}" : ''); ?>"></span>
            <?php endfor; ?>
        </span>
    <?php else: ?>
        <i class="bi bi-fingerprint me-1"></i>
    <?php endif; ?>
    <?php echo e($fpCount); ?><?php if($fpMax !== null): ?>/<?php echo e($fpMax); ?><?php endif; ?>
</span>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/partials/_fingerprint-badge.blade.php ENDPATH**/ ?>