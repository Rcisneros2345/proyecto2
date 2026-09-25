

<?php $__env->startSection('title', 'Grupos de permisos'); ?>
<?php $__env->startSection('breadcrumb', 'Administración › Grupos de permisos'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Grupos de permisos','subtitle' => 'Define perfiles reutilizables por área, responsable o tipo de acceso.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Grupos de permisos','subtitle' => 'Define perfiles reutilizables por área, responsable o tipo de acceso.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('permission-groups.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo grupo
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
    <?php $__empty_1 = true; $__currentLoopData = $permissionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h5 mb-1"><?php echo e($group->name); ?></h2>
                            <p class="text-muted small mb-0"><?php echo e($group->description ?? 'Sin descripción'); ?></p>
                        </div>
                        <?php if($group->is_default): ?>
                            <span class="badge badge--status badge--active">Predeterminado</span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark"><?php echo e($group->permissions_count); ?> permisos</span>
                        <span class="badge bg-light text-dark"><?php echo e($group->employees->count()); ?> empleados</span>
                        <span class="badge bg-light text-dark"><?php echo e($group->profesores->count()); ?> profesores</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('permission-groups.permissions', $group)); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-key me-1"></i> Permisos
                        </a>
                        <a href="<?php echo e(route('permission-groups.assign-employees', $group)); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-people me-1"></i> Asignar empleados
                        </a>
                        <a href="<?php echo e(route('permission-groups.assign-profesores', $group)); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-person-badge me-1"></i> Asignar profesores
                        </a>
                        <a href="<?php echo e(route('permission-groups.edit', $group)); ?>" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>

                        <form action="<?php echo e(route('permission-groups.destroy', $group)); ?>" method="POST" onsubmit="return confirm('¿Deseas eliminar este grupo?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <?php echo $__env->make('partials.empty-state', [
                        'icon' => 'bi-shield-check',
                        'title' => 'No hay grupos de permisos configurados',
                        'desc' => 'Crea la primera definición de perfil para comenzar a asignar accesos.',
                        'cta' => ['label' => 'Crear grupo', 'url' => route('permission-groups.create')],
                        'ctaLink' => true,
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/permission-groups/index.blade.php ENDPATH**/ ?>