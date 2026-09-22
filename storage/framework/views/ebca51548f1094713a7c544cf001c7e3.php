<?php $__env->startSection('title', 'Kardex: ' . $alumno->nombre_completo . ' - ' . $ciclo->label); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Alumnos › Kardex'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Kardex: '.e($alumno->nombre_completo).'','subtitle' => 'Control: '.e($alumno->numero_alumno).' | Ciclo: '.e($ciclo->label).'','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kardex: '.e($alumno->nombre_completo).'','subtitle' => 'Control: '.e($alumno->numero_alumno).' | Ciclo: '.e($ciclo->label).'','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <div class="btn-group btn-group-sm">
            <a href="<?php echo e(route('academia.alumnos.show', $alumno)); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="<?php echo e(route('academia.kardex.print', ['alumno_id' => $alumno->numero_alumno, 'ciclo_principal' => $ciclo->label])); ?>" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i> Imprimir
            </a>
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

<?php if(empty($kardex['materias'])): ?>
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-file-earmark-text fs-1 mb-2"></i>
            <p>No hay calificaciones registradas en este ciclo</p>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-header">
            <span class="fw-bold">Calificaciones del Ciclo</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Sem</th>
                            <th class="text-center">P1</th>
                            <th class="text-center">P2</th>
                            <th class="text-center">P3</th>
                            <th class="text-center">CF</th>
                            <th class="text-center">EXR</th>
                            <th class="text-center">EXRS</th>
                            <th class="text-center">CT</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kardex['materias']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($m['nombre'] ?? $clave); ?></td>
                                <td><?php echo e($m['semestre'] ?? ''); ?></td>
                                <td class="text-center"><?php echo e($m['P1'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['P2'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['P3'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['CF'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['EXR'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['EXRS'] ?? '—'); ?></td>
                                <td class="text-center fw-semibold"><?php echo e($m['CT'] ?? '—'); ?></td>
                                <td>
                                    <?php
                                        $estadoClass = match($m['ESTADO']) {
                                            'APROBADO' => 'bg-success',
                                            'REPROBADO' => 'bg-danger',
                                            'SIN DERECHO' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?php echo e($estadoClass); ?>">
                                        <?php echo e($m['ESTADO']); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <div class="h3 text-success"><?php echo e($kardex['resumen']['aprobadas']); ?></div>
                    <small class="text-muted">Aprobadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <div class="h3 text-danger"><?php echo e($kardex['resumen']['reprobadas']); ?></div>
                    <small class="text-muted">Reprobadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <div class="h3 text-warning"><?php echo e($kardex['resumen']['sin_derecho']); ?></div>
                    <small class="text-muted">Sin derecho</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <div class="h3 text-info"><?php echo e($kardex['resumen']['promedio'] ?? '—'); ?></div>
                    <small class="text-muted">Promedio</small>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="fw-bold">Detalle por Evaluación</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>P1</th>
                            <th>P2</th>
                            <th>P3</th>
                            <th>CF</th>
                            <th>EXR</th>
                            <th>EXRS</th>
                            <th>CT</th>
                            <th>Literal</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kardex['materias']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold small"><?php echo e($m['nombre'] ?? $clave); ?></td>
                                <td class="text-center"><?php echo e($m['P1'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['P2'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['P3'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['CF'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['EXR'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['EXRS'] ?? '—'); ?></td>
                                <td class="text-center fw-semibold"><?php echo e($m['CT'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo e($m['LITERAL']); ?></td>
                                <td>
                                    <?php
                                        $estadoClass = match($m['ESTADO']) {
                                            'APROBADO' => 'bg-success',
                                            'REPROBADO' => 'bg-danger',
                                            'SIN DERECHO' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?php echo e($estadoClass); ?>">
                                        <?php echo e($m['ESTADO']); ?>

                                    </span>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\alumnos\kardex.blade.php ENDPATH**/ ?>