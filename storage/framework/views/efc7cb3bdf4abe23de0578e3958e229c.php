<?php $__env->startSection('title', 'Editar Ciclo: ' . $ciclo->label); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Ciclos › Editar'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Editar Ciclo: '.e($ciclo->label).'','subtitle' => 'Actualiza la descripción, fechas y estado del ciclo académico.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar Ciclo: '.e($ciclo->label).'','subtitle' => 'Actualiza la descripción, fechas y estado del ciclo académico.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('academia.ciclos.show', $ciclo)); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
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

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('academia.ciclos.update', $ciclo)); ?>" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Año Inicial</label>
                    <input type="number" class="form-control" value="<?php echo e($ciclo->inicial); ?>" readonly>
                    <div class="form-text">No editable</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Año Final</label>
                    <input type="number" class="form-control" value="<?php echo e($ciclo->final); ?>" readonly>
                    <div class="form-text">No editable</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Periodo</label>
                    <input type="number" class="form-control" value="<?php echo e($ciclo->periodo); ?>" readonly>
                    <div class="form-text">No editable</div>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" value="<?php echo e($ciclo->descripcion); ?>" placeholder="Ej: Ciclo 2025-2026">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha Inicial</label>
                    <input type="date" name="fecha_inicial" class="form-control" value="<?php echo e($ciclo->fecha_inicial); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha Final</label>
                    <input type="date" name="fecha_final" class="form-control" value="<?php echo e($ciclo->fecha_final); ?>">
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="activo" id="activo" <?php echo e($ciclo->activo ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo e(route('academia.ciclos.show', $ciclo)); ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\ciclos\edit.blade.php ENDPATH**/ ?>