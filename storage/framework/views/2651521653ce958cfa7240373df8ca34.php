<?php $__env->startSection('title', $alumno->nombre_completo . ' - ' . $ciclo->label); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Alumnos › ' . $alumno->nombre_completo); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => ''.e($alumno->nombre_completo).'','subtitle' => 'Control: '.e($alumno->numero_alumno).' | CURP: '.e($alumno->curp).'','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($alumno->nombre_completo).'','subtitle' => 'Control: '.e($alumno->numero_alumno).' | CURP: '.e($alumno->curp).'','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <div class="btn-group btn-group-sm">
            <a href="<?php echo e(route('academia.alumnos.kardex', $alumno)); ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-text me-1"></i> Kardex
            </a>
            <a href="<?php echo e(route('academia.alumnos.historial', $alumno)); ?>" class="btn btn-info">
                <i class="bi bi-clock-history me-1"></i> Historial
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


<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="h4 mb-1"><?php echo e($alumno->edad); ?></div>
                <div class="text-muted small">Edad</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="h4 mb-1"><?php echo e($inscripciones->count()); ?></div>
                <div class="text-muted small">Grupos inscritos</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="h4 mb-1"><?php echo e($kardex['resumen']['aprobadas'] ?? 0); ?></div>
                <div class="text-muted small text-success">Aprobadas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="h4 mb-1"><?php echo e($kardex['resumen']['reprobadas'] ?? 0); ?></div>
                <div class="text-muted small text-danger">Reprobadas</div>
            </div>
        </div>
    </div>
</div>


<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">Inscripciones del ciclo <?php echo e($ciclo->label); ?></span>
    </div>
    <div class="card-body p-0">
        <?php if($inscripciones->isEmpty()): ?>
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-person-x fs-1 mb-2"></i>
                <p>No hay inscripciones en este ciclo</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Nivel</th>
                            <th>Turno</th>
                            <th>Inscritos</th>
                            <th>Estatus</th>
                            <th>Inscripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($ins->grupo->codigo_grupo); ?></td>
                                <td><?php echo e($ins->grupo->nivel); ?></td>
                                <td>
                                    <span class="badge <?php echo e($ins->grupo->turnoRel && str_starts_with($ins->grupo->turnoRel->descripcion_corta, 'V') ? 'bg-purple' : 'bg-warning'); ?>">
                                        <?php echo e($ins->grupo->turnoRel?->descripcion_corta); ?>

                                    </span>
                                </td>
                                <td><?php echo e($ins->grupo->inscritos); ?></td>
                                <td>
                                    <span class="badge badge--status <?php echo e(($ins->estatus ?? null) === 'INSCRITO' ? 'badge--active' : 'badge--inactive'); ?>">
                                        <?php echo e($ins->estatus ?? '—'); ?>

                                    </span>
                                </td>
                                <td class="small text-muted"><?php echo e($ins->fecha_inscripcion?->format('d/m/Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card">
    <div class="card-header">
        <span class="fw-bold">Resumen Académico del Ciclo</span>
    </div>
    <div class="card-body">
        <?php if(empty($kardex['materias'])): ?>
            <div class="text-center text-muted py-4">
                <i class="bi bi-file-earmark-text fs-1 mb-2"></i>
                <p>No hay calificaciones registradas en este ciclo</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Sem</th>
                            <th>P1</th>
                            <th>P2</th>
                            <th>P3</th>
                            <th>CF</th>
                            <th>EXR</th>
                            <th>CT</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kardex['materias']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($m['nombre'] ?? $clave); ?></td>
                                <td><?php echo e($m['semestre'] ?? ''); ?></td>
                                <td><?php echo e($m['P1'] ?? '—'); ?></td>
                                <td><?php echo e($m['P2'] ?? '—'); ?></td>
                                <td><?php echo e($m['P3'] ?? '—'); ?></td>
                                <td><?php echo e($m['CF'] ?? '—'); ?></td>
                                <td><?php echo e($m['EXR'] ?? '—'); ?></td>
                                <td class="fw-semibold"><?php echo e($m['CT'] ?? '—'); ?></td>
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

            
            <div class="row g-3 mt-3">
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
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\alumnos\show.blade.php ENDPATH**/ ?>