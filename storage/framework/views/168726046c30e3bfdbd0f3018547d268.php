

<?php $__env->startSection('title', 'Permisos'); ?>
<?php $__env->startSection('breadcrumb', 'Administración › Permisos'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Permisos','subtitle' => 'Administra módulos, acciones y su asociación con grupos.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Permisos','subtitle' => 'Administra módulos, acciones y su asociación con grupos.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('permissions.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo permiso
        </a>
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
    <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h5 mb-1"><?php echo e($module->name); ?></h2>
                            <p class="text-muted small mb-0"><?php echo e($module->slug); ?></p>
                        </div>
                        <span class="badge bg-light text-dark"><?php echo e($module->permissions->count()); ?> permisos</span>
                    </div>

                    <div class="list-group list-group-flush">
                        <?php $__empty_2 = true; $__currentLoopData = $module->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <div>
                                        <div class="fw-semibold"><?php echo e($permission->name); ?></div>
                                        <small class="text-muted"><?php echo e($permission->action); ?> · <?php echo e($permission->slug); ?></small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('permissions.assign-groups', $permission)); ?>" class="btn btn-outline-primary" title="Grupos"><i class="bi bi-people"></i></a>
                                        <a href="<?php echo e(route('permissions.edit', $permission)); ?>" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <form action="<?php echo e(route('permissions.destroy', $permission)); ?>" method="POST" onsubmit="return confirm('¿Deseas eliminar este permiso?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <div class="text-center text-muted py-3">Sin permisos definidos.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <?php echo $__env->make('partials.empty-state', [
                        'icon' => 'bi-key',
                        'title' => 'No hay módulos configurados',
                        'desc' => 'Cuando se registren permisos, aparecerán agrupados por módulo.',
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/permissions/index.blade.php ENDPATH**/ ?>