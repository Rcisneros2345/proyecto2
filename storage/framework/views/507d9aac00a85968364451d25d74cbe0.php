<?php $__env->startSection('title', 'Kardex'); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Kardex'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Kardex de Alumnos','subtitle' => 'Consulta las calificaciones de los alumnos por ciclo escolar.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kardex de Alumnos','subtitle' => 'Consulta las calificaciones de los alumnos por ciclo escolar.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('academia.ciclos.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-calendar me-1"></i> Cambiar ciclo
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


<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('academia.kardex.show')); ?>" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Buscar alumno</label>
                <input type="text" name="buscar" class="form-control" placeholder="Número de control, nombre, CURP..." value="<?php echo e(request('buscar')); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>


<div class="row g-3">
    <div class="col-md-4">
        <a href="<?php echo e(route('academia.kardex.show')); ?>" class="card text-decoration-none border-primary h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-search fs-1 text-primary mb-2"></i>
                <h5 class="card-title text-dark">Consultar Kardex</h5>
                <p class="card-text text-muted small">Busca un alumno para ver su kardex del ciclo actual</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?php echo e(route('academia.kardex.historial')); ?>" class="card text-decoration-none border-info h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-clock-history fs-1 text-info mb-2"></i>
                <h5 class="card-title text-dark">Historial</h5>
                <p class="card-text text-muted small">Consulta el historial completo de un alumno</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?php echo e(route('academia.kardex.print')); ?>" class="card text-decoration-none border-success h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-printer fs-1 text-success mb-2"></i>
                <h5 class="card-title text-dark">Imprimir Kardex</h5>
                <p class="card-text text-muted small">Genera un PDF con el kardex de un alumno</p>
            </div>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\kardex\index.blade.php ENDPATH**/ ?>