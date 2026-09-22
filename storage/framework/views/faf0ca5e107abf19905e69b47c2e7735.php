

<?php $__env->startSection('title', 'Incidencias'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Incidencias'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Incidencias','subtitle' => 'Seguimiento y aprobación de incidencias de personal y profesores.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Incidencias','subtitle' => 'Seguimiento y aprobación de incidencias de personal y profesores.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <?php if(auth()->user()->canAccessModule('incidencias', 'create')): ?>
            <a href="<?php echo e(route('incidencias.create')); ?>" class="btn btn-primary">Nueva incidencia</a>
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

<div class="card mb-4">
    <div class="card-body">
        <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['action' => route('incidencias.index'),'clearUrl' => route('incidencias.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('incidencias.index')),'clear-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('incidencias.index'))]); ?>
            <div class="col-md-4">
                <label for="q" class="form-label">Buscar</label>
                <input type="text" id="q" name="q" value="<?php echo e($q); ?>" class="form-control" placeholder="Asunto, tipo, motivo, número">
            </div>
            <div class="col-md-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="pendiente" <?php echo e($estado === 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                    <option value="aprobada" <?php echo e($estado === 'aprobada' ? 'selected' : ''); ?>>Aprobada</option>
                    <option value="rechazada" <?php echo e($estado === 'rechazada' ? 'selected' : ''); ?>>Rechazada</option>
                </select>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale9f22847d79d6273acb27aff60f1f678)): ?>
<?php $attributes = $__attributesOriginale9f22847d79d6273acb27aff60f1f678; ?>
<?php unset($__attributesOriginale9f22847d79d6273acb27aff60f1f678); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale9f22847d79d6273acb27aff60f1f678)): ?>
<?php $component = $__componentOriginale9f22847d79d6273acb27aff60f1f678; ?>
<?php unset($__componentOriginale9f22847d79d6273acb27aff60f1f678); ?>
<?php endif; ?>
    </div>
</div>

<div class="card data-table-shell">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Persona</th>
                        <th>Área</th>
                        <th>Puesto</th>
                        <th>Director / Responsable</th>
                        <th>Asunto</th>
                        <th>Creada</th>
                        <th>Falta programada</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $incidencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incidencia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo e($incidencia->empleado ? 'Empleado' : 'Profesor'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    <?php if($incidencia->empleado): ?>
                                        <?php echo e($incidencia->empleado->name); ?>

                                    <?php else: ?>
                                        <?php echo e(trim("{$incidencia->profesor?->paterno} {$incidencia->profesor?->materno} {$incidencia->profesor?->nombre_profesor}")); ?>

                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    <?php echo e($incidencia->numero_empleado ?? ($incidencia->empleado?->user_id ?? $incidencia->profesor?->clave_profesor ?? '—')); ?>

                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo e($incidencia->area?->identificador ?? '—'); ?></div>
                                <small class="text-muted"><?php echo e($incidencia->area?->descripcion ?? 'Sin descripción'); ?></small>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo e($incidencia->puesto?->identificador ?? '—'); ?></div>
                                <small class="text-muted"><?php echo e($incidencia->puesto?->descripcion ?? 'Sin descripción'); ?></small>
                            </td>
                            <td>
                                <?php
                                    $directorNombre = $incidencia->director?->name ?? $incidencia->responsableArea?->name ?? '—';
                                ?>
                                <div class="fw-semibold"><?php echo e($directorNombre); ?></div>
                                <small class="text-muted">
                                    <?php echo e($incidencia->director?->user_id ?? $incidencia->responsableArea?->user_id ?? 'Sin responsable'); ?>

                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo e($incidencia->asunto); ?></div>
                                <small class="text-muted"><?php echo e($incidencia->tipo_justificacion); ?></small>
                            </td>
                            <td><?php echo e($incidencia->fecha_creacion?->format('d/m/Y H:i') ?? '—'); ?></td>
                            <td><?php echo e(($incidencia->fecha_falta_programada ?? $incidencia->fecha_justificacion)?->format('d/m/Y') ?? '—'); ?></td>
                            <td>
                                <?php if($incidencia->tipo_duracion === 'horario'): ?>
                                    <?php echo e(substr((string) $incidencia->hora_inicio, 0, 5)); ?> - <?php echo e(substr((string) $incidencia->hora_fin, 0, 5)); ?>

                                <?php else: ?>
                                    Día completo
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $estadoBadge = match($incidencia->estado) {
                                    'aprobada' => 'badge--active',
                                    'rechazada' => 'badge--inactive',
                                    default => 'badge--inactive'
                                };
                                ?>
                                <span class="badge badge--status <?php echo e($estadoBadge); ?>"><?php echo e($incidencia->estado); ?></span>
                                <?php if($incidencia->visto_at): ?>
                                    <small class="d-block text-success mt-1"><i class="bi bi-eye me-1"></i>Vista</small>
                                <?php endif; ?>
                                <?php if($incidencia->firmado_at): ?>
                                    <small class="d-block text-primary"><i class="bi bi-pen me-1"></i>Firmada</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(auth()->user()->canAccessModule('incidencias', 'approve')): ?>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <form method="POST" action="<?php echo e(route('incidencias.estado', $incidencia)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="estado" value="aprobada">
                                            <button type="submit" class="btn btn-outline-success" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('incidencias.estado', $incidencia)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="estado" value="rechazada">
                                            <button type="submit" class="btn btn-outline-danger" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                                <?php if(! $incidencia->visto_at && $incidencia->created_by_user_id === auth()->id()): ?>
                                    <form method="POST" action="<?php echo e(route('incidencias.vista', $incidencia)); ?>" class="mt-1">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar vista</button>
                                    </form>
                                <?php endif; ?>
                                <?php if($incidencia->estado === 'aprobada' && ! $incidencia->firmado_at && $incidencia->created_by_user_id === auth()->id()): ?>
                                    <form method="POST" action="<?php echo e(route('incidencias.firmar', $incidencia)); ?>" class="mt-1">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Firmar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-5">
                                <?php echo $__env->make('partials.empty-state', [
                                    'icon' => 'bi-exclamation-circle',
                                    'title' => 'No hay incidencias registradas',
                                    'desc' => 'Cuando se generen solicitudes o aprobaciones aparecerán aquí.',
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <?php echo e($incidencias->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\incidencias\index.blade.php ENDPATH**/ ?>