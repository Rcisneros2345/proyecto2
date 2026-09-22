<?php $__env->startSection('title', 'Notificaciones'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Notificaciones'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Centro de notificaciones','subtitle' => 'Avisos importantes sobre la red, sincronizaciones y asistencias.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Centro de notificaciones','subtitle' => 'Avisos importantes sobre la red, sincronizaciones y asistencias.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('operations.queue')); ?>" class="btn btn-outline-secondary"><i class="bi bi-list-task me-1"></i> Ver cola</a>
    <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<div class="card shadow-sm">
    <div class="list-group list-group-flush">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e($notification['url'] ?? '#'); ?>" class="list-group-item list-group-item-action d-flex gap-3 align-items-start py-3">
                <span class="avatar is-sm"><?php echo e($notification['initials']); ?></span>
                <span class="flex-grow-1"><strong class="d-block"><?php echo e($notification['title']); ?></strong><span class="text-muted small d-block mt-1"><?php echo e($notification['desc']); ?></span><span class="text-tertiary-token small"><?php echo e($notification['category']); ?> · <?php echo e($notification['time']); ?></span></span>
                <?php if(!$notification['read']): ?><span class="notify-unread-dot mt-2"></span><?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="notify-empty"><i class="bi bi-bell"></i><strong>No hay notificaciones</strong><span class="notify-empty-sub">Estás al día.</span></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\operations\notifications.blade.php ENDPATH**/ ?>