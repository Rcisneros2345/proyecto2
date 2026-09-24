<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['variant' => 'default', 'size' => 'md', 'label' => null, 'color' => null, 'dot' => false, 'icon' => null, 'class' => '']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['variant' => 'default', 'size' => 'md', 'label' => null, 'color' => null, 'dot' => false, 'icon' => null, 'class' => '']); ?>
<?php foreach (array_filter((['variant' => 'default', 'size' => 'md', 'label' => null, 'color' => null, 'dot' => false, 'icon' => null, 'class' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    // Semantic variant → badge--active / badge--inactive (status badges)
    $variantClasses = [
        'default' => 'badge--inactive',
        'primary' => 'badge--active',
        'success' => 'badge--active',
        'warning' => 'badge--inactive',
        'danger'  => 'badge--inactive',
        'info'    => 'badge--inactive',
        'purple'  => 'badge--inactive',
        'pink'    => 'badge--inactive',
        'teal'    => 'badge--inactive',
        'orange'  => 'badge--inactive',
    ];

    // Data color → cat-* classes (decorative / grouping badges)
    $colorClasses = [
        'blue'    => 'cat-blue',
        'orange'  => 'cat-orange',
        'purple'  => 'cat-purple',
        'pink'    => 'cat-pink',
        'red'     => 'cat-red',
        'green'   => 'cat-green',
        'amber'   => 'cat-amber',
        'lavender'=> 'cat-lavender',
        'gray'    => 'cat-gray',
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5',
        'md' => 'px-2.5 py-0.5',
        'lg' => 'px-3 py-1',
    ];

    $baseClass = 'badge badge--status';
    $variantClass = $color ? '' : ($variantClasses[$variant] ?? $variantClasses['default']);
    $catClass = $colorClasses[$color] ?? '';
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $dotClass = $dot ? 'badge-with-dot' : '';
?>

<span class="<?php echo e($baseClass); ?> <?php echo e($variantClass); ?> <?php echo e($catClass); ?> <?php echo e($sizeClass); ?> <?php echo e($dotClass); ?> <?php echo e($class); ?>">
    <?php if($icon): ?><i class="<?php echo e($icon); ?> me-1"></i><?php endif; ?>
    <?php echo e($label); ?><?php echo e($slot); ?>

</span><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/components/badge.blade.php ENDPATH**/ ?>