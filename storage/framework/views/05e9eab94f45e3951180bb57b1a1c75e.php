<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['icon', 'label', 'value', 'color']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['icon', 'label', 'value', 'color']); ?>
<?php foreach (array_filter((['icon', 'label', 'value', 'color']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    // Map color names to --cat-* token references for consistent theming
    $catTokenMap = [
        'blue'    => 'var(--cat-blue)',
        'orange'  => 'var(--cat-orange)',
        'purple'  => 'var(--cat-purple)',
        'pink'    => 'var(--cat-pink)',
        'red'     => 'var(--cat-red)',
        'green'   => 'var(--cat-green)',
        'amber'   => 'var(--cat-amber)',
        'lavender'=> 'var(--cat-lavender)',
        'gray'    => 'var(--cat-gray)',
        // Bootstrap semantic aliases → closest cat token
        'success' => 'var(--cat-green)',
        'danger'  => 'var(--cat-red)',
        'warning' => 'var(--cat-orange)',
        'info'    => 'var(--cat-blue)',
    ];
    $catToken = $catTokenMap[$color] ?? null;

    // Fallback for colors without a cat token (e.g. teal)
    $iconBg = $catToken ? "color-mix(in srgb, {$catToken} 15%, transparent)" : "var(--bs-{$color}-subtle, rgba(148,163,184,.14))";
    $iconColor = $catToken ?? "var(--cat-{$color}, var(--text-secondary))";
    $valueColor = $catToken ?? "var(--cat-{$color}, var(--text))";
?>

<div class="kpi-card card h-100 border-0 shadow-sm" style="--bs-card-border-color: <?php echo e($iconBg); ?>;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="kpi-icon rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: <?php echo e($iconBg); ?>;">
                        <i class="<?php echo e($icon); ?> fs-4" style="color: <?php echo e($iconColor); ?>;"></i>
                    </div>
                    <h6 class="mb-0 text-muted fw-semibold"><?php echo e($label); ?></h6>
                </div>
                <div class="kpi-value fs-2 fw-bold" style="color: <?php echo e($valueColor); ?>;" data-stat-value><?php echo e($value); ?></div>
            </div>
            <?php echo e($slot ?? ''); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\components\stat-card.blade.php ENDPATH**/ ?>