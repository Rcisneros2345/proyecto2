<?php $__env->startSection('title', $grupo->codigo_grupo . ' - ' . $ciclo->label); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Grupos › ' . $grupo->codigo_grupo); ?>

<?php $__env->startSection('content'); ?>
<?php
    $partesCodigo = $grupo->codigo_grupo_partes;
?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => ''.e($grupo->codigo_grupo).'','subtitle' => ''.e($grupo->grado).'° · '.e($grupo->turno_nombre).' · '.e($grupo->nivelRel?->descripcion ?? $grupo->nivel).' · '.e($grupo->modalidad_nombre).' · '.e($grupo->inscritos).' inscritos · '.e($grupo->sede?->descripcion).'','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($grupo->codigo_grupo).'','subtitle' => ''.e($grupo->grado).'° · '.e($grupo->turno_nombre).' · '.e($grupo->nivelRel?->descripcion ?? $grupo->nivel).' · '.e($grupo->modalidad_nombre).' · '.e($grupo->inscritos).' inscritos · '.e($grupo->sede?->descripcion).'','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <?php if(auth()->user()->canAccessModule('academia.grupos', 'asistencia')): ?>
            <div class="btn-group btn-group-sm">
                <a href="<?php echo e(route('academia.grupos.asistencia', $grupo)); ?>" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Asistencia
                </a>
            </div>
        <?php endif; ?>
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

<p class="text-muted small mb-4">
    Plan <?php echo e($partesCodigo['anio_plan'] ?? '—'); ?> · Nivel <?php echo e($partesCodigo['nivel'] ?? $grupo->nivel); ?> ·
    Sede código <?php echo e($partesCodigo['sede'] ?? '—'); ?> · Modelo <?php echo e($partesCodigo['modelo'] ?? '—'); ?> ·
    Grado/grupo <?php echo e($partesCodigo['grado_grupo'] ?? '—'); ?>

    <?php if($partesCodigo['nivel_superior']): ?> · Ingeniería/Licenciatura (<?php echo e($partesCodigo['nivel_superior']); ?>) <?php endif; ?>
</p>


<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-alumnos">Alumnos (<?php echo e($alumnos->total()); ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-horarios">Horarios</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-conflictos">Conflictos Aula</button></li>
</ul>

<div class="tab-content">
    
    <div class="tab-pane fade show active" id="tab-alumnos">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Nivel / Carrera</th>
                                <th>Contacto</th>
                                <th>Estatus académico</th>
                                <th>Estatus en grupo</th>
                                <th>Inscripción</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $alumnos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alumnoGrupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($alumnoGrupo->numero_alumno); ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($alumnoGrupo->nombre_completo); ?></div>
                                        <small class="text-muted">CURP: <?php echo e($alumnoGrupo->curp ?: 'No registrada'); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo e($alumnoGrupo->nivelRel?->descripcion ?? $alumnoGrupo->nivel ?? '—'); ?></div>
                                        <small class="text-muted"><?php echo e($alumnoGrupo->carrera ?: 'Carrera no registrada'); ?></small>
                                    </td>
                                    <td class="small">
                                        <div><?php echo e($alumnoGrupo->telefono ?: 'Sin teléfono'); ?></div>
                                        <div class="text-muted"><?php echo e($alumnoGrupo->email ?: 'Sin correo'); ?></div>
                                    </td>
                                    <td>
                                        <?php
                                            $estatusAcademico = strtoupper((string) $alumnoGrupo->estatus);
                                            $estatusAcademicoClase = $estatusAcademico === 'ACTIVO' ? 'badge--active' : 'badge--inactive';
                                        ?>
                                        <span class="badge badge--status <?php echo e($estatusAcademicoClase); ?>">
                                            <?php echo e($alumnoGrupo->estatus ?: '—'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge--status <?php echo e(($alumnoGrupo->pivot_estatus ?? null) === 'INSCRITO' ? 'badge--active' : 'badge--inactive'); ?>">
                                            <?php echo e($alumnoGrupo->pivot_estatus ?? '—'); ?>

                                        </span>
                                    </td>
                                    <td class="small text-muted"><?php echo e($alumnoGrupo->pivot_fecha_inscripcion ? \Carbon\Carbon::parse($alumnoGrupo->pivot_fecha_inscripcion)->format('d/m/Y') : '—'); ?></td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('academia.alumnos.show', $alumnoGrupo)); ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php echo e($alumnos->links()); ?>

        </div>
    </div>

    
    <div class="tab-pane fade" id="tab-horarios">
        <?php if($horarios->isEmpty()): ?>
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-calendar-x fs-1 mb-2"></i>
                    <p>No hay horarios programados para este grupo</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body p-0">
                    <?php $__currentLoopData = $horarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dia => $clases): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php echo e($loop->first ? '' : 'border-top'); ?>">
                            <div class="p-3 bg-light border-bottom fw-semibold">
                                <span class="badge <?php echo e(in_array($dia, [6,7]) ? 'bg-purple' : 'bg-primary'); ?> me-2">
                                    <?php echo e(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'][$dia-1]); ?>

                                </span>
                                <?php echo e($clases->first()->sesionBase?->descripcion); ?>

                            </div>
                            <?php $__currentLoopData = $clases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-3 border-bottom d-flex align-items-center gap-3">
                                    <div class="text-nowrap small text-muted" style="width: 120px;">
                                        <?php echo e($clase->sesionBase?->hora_inicio?->format('H:i')); ?> - <?php echo e($clase->sesionBase?->hora_fin?->format('H:i')); ?>

                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?php echo e($clase->materia?->label); ?></div>
                                        <div class="small text-muted">
                                            <?php echo e($clase->profesor?->nombre_completo); ?> · <?php echo e($clase->ubicacion); ?> · <span class="badge <?php echo e($clase->tipoClase === 'PTC' ? 'bg-purple' : 'bg-info'); ?>"><?php echo e($clase->tipoClase); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="tab-pane fade" id="tab-conflictos">
        <?php if(empty($conflictos)): ?>
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
                    <p>No se detectaron conflictos de aula</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
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
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/academia/grupos/show.blade.php ENDPATH**/ ?>