

<?php $__env->startSection('title', 'Navegación del sistema'); ?>
<?php $__env->startSection('breadcrumb', 'Administración › Navegación'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Navegación del sistema','subtitle' => 'Administra las entradas, rutas y permisos visibles en el menú.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Navegación del sistema','subtitle' => 'Administra las entradas, rutas y permisos visibles en el menú.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('navigation-items.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva entrada
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

<div class="card data-table-shell">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Sección</th>
                        <th>Etiqueta</th>
                        <th>Ruta</th>
                        <th>Módulo</th>
                        <th>Permiso</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->section); ?></td>
                            <td><i class="bi <?php echo e($item->icon); ?> me-1"></i><?php echo e($item->label); ?></td>
                            <td><code><?php echo e($item->route_name); ?></code></td>
                            <td><?php echo e($item->module?->name ?? 'Sin módulo'); ?></td>
                            <td><span class="badge bg-light text-dark"><?php echo e($item->permission_action); ?></span></td>
                            <td><?php echo e($item->sort_order); ?></td>
                            <td>
                                <span class="badge badge--status <?php echo e($item->active ? 'badge--active' : 'badge--inactive'); ?>">
                                    <?php echo e($item->active ? 'Activa' : 'Inactiva'); ?>

                                </span>
                                <?php if($item->admin_only): ?>
                                    <span class="badge bg-warning-subtle text-warning">Admin</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('navigation-items.edit', $item)); ?>" class="btn btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <form action="<?php echo e(route('navigation-items.destroy', $item)); ?>" method="POST" onsubmit="return confirm('¿Deseas eliminar esta entrada?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8"><?php echo $__env->make('partials.empty-state', ['icon' => 'bi-list', 'title' => 'No hay entradas de navegación', 'desc' => 'Crea la primera entrada para construir el menú desde la base de datos.'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\navigation-items\index.blade.php ENDPATH**/ ?>