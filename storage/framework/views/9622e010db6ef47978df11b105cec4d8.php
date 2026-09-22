<?php $__env->startSection('title', 'Conflictos de Aula'); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Horarios › Conflictos de Aula'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Conflictos de Aula','subtitle' => 'Revisa las coincidencias de aula detectadas en el ciclo '.e($ciclo->label).'.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Conflictos de Aula','subtitle' => 'Revisa las coincidencias de aula detectadas en el ciclo '.e($ciclo->label).'.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('academia.ciclos.index')); ?>" class="btn btn-outline-secondary btn-sm">
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

<?php if(empty($conflictos)): ?>
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
            <p>No se detectaron conflictos de aula en el ciclo <?php echo e($ciclo->label); ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-header bg-danger-subtle">
            <span class="fw-bold text-danger">Se detectaron <?php echo e(count($conflictos)); ?> conflicto(s) de aula</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Sesión</th>
                            <th>Sede</th>
                            <th>Edificio</th>
                            <th>Aula</th>
                            <th>Clases en conflicto</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $conflictos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'][$c->dia-1]); ?></td>
                                <td><?php echo e($c->sesion); ?></td>
                                <td><?php echo e($c->id_campus); ?></td>
                                <td><?php echo e($c->edificio); ?></td>
                                <td><?php echo e($c->aula); ?></td>
                                <td><span class="badge bg-danger"><?php echo e($c->total); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="verDetalleConflicto(<?php echo e($c->dia); ?>, <?php echo e($c->sesion); ?>, '<?php echo e($c->id_campus); ?>', '<?php echo e($c->edificio); ?>', '<?php echo e($c->aula); ?>')">
                                        Ver detalle
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\horarios\aula.blade.php ENDPATH**/ ?>