<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title',
    'subtitle' => null,
    'actions' => null,
    'hideTitle' => false,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title',
    'subtitle' => null,
    'actions' => null,
    'hideTitle' => false,
]); ?>
<?php foreach (array_filter(([
    'title',
    'subtitle' => null,
    'actions' => null,
    'hideTitle' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="page-header">
    <div class="page-header__copy">
        <?php if (! ($hideTitle || $__env->hasSection('title'))): ?>
            <h1 class="page-header__title"><?php echo e($title); ?></h1>
        <?php endif; ?>
        <?php if($subtitle): ?>
            <p class="page-header__subtitle"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>

    <?php if(!empty($actions)): ?>
        <div class="page-header__actions">
            <?php echo e($actions); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/components/page-header.blade.php ENDPATH**/ ?>