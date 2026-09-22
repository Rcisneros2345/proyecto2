

<?php $__env->startSection('title', $area->identificador); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Áreas › ' . $area->identificador); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => ''.e($area->identificador).'','subtitle' => ''.e($area->descripcion ?? 'Sin descripción').'','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($area->identificador).'','subtitle' => ''.e($area->descripcion ?? 'Sin descripción').'','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <div class="btn-group btn-group-sm">
            <a href="<?php echo e(route('areas.index')); ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('areas.edit', $area)); ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Editar</a>
            <?php endif; ?>
        </div>
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

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><strong>Detalle del área</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Identificador</dt>
                    <dd class="col-sm-7"><?php echo e($area->identificador); ?></dd>

                    <dt class="col-sm-5">Descripción</dt>
                    <dd class="col-sm-7"><?php echo e($area->descripcion ?? '—'); ?></dd>

                    <dt class="col-sm-5">Responsable</dt>
                    <dd class="col-sm-7"><?php echo e($area->empleadoResponsable?->name ?? '—'); ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><strong>Puestos asociados</strong></div>
            <div class="card-body">
                <?php if($area->puestos->isEmpty()): ?>
                    <p class="text-muted mb-0">No hay puestos vinculados a esta área.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $area->puestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong><?php echo e($puesto->identificador); ?></strong><br>
                                    <small class="text-muted"><?php echo e($puesto->descripcion ?? 'Sin descripción'); ?></small>
                                </span>
                                <a href="<?php echo e(route('puestos.show', $puesto)); ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\areas\show.blade.php ENDPATH**/ ?>