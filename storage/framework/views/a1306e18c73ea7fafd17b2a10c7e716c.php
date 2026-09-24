<?php $__env->startSection('title', 'Cursos'); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Cursos'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Cursos','subtitle' => 'Catálogo académico de cursos y su relación con materias, profesores y ciclo actual.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cursos','subtitle' => 'Catálogo académico de cursos y su relación con materias, profesores y ciclo actual.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('academia.ciclos.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-calendar me-1"></i> Cambiar ciclo
        </a>
        <?php if(auth()->user()->canAccessModule('academia.cursos', 'create')): ?>
            <a href="<?php echo e(route('academia.cursos.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo curso
            </a>
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

<div class="card">
    <div class="card-body p-0">
        <?php if($cursos->isEmpty()): ?>
            <div class="card-body text-center text-muted py-5">
                <?php echo $__env->make('partials.empty-state', [
                    'icon' => 'bi-book',
                    'title' => 'No hay cursos registrados para este ciclo',
                    'desc' => 'Crea el primer curso para empezar a asignar docentes, materias y alumnos.',
                    'cta' => auth()->user()->canAccessModule('academia.cursos', 'create')
                        ? ['label' => 'Crear curso', 'url' => route('academia.cursos.create')]
                        : null,
                    'ctaLink' => auth()->user()->canAccessModule('academia.cursos', 'create'),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Descripción</th>
                            <th>Materia</th>
                            <th>Maestro(s)</th>
                            <th>Horario propio</th>
                            <th>Nivel</th>
                            <th>Turno</th>
                            <th>Sede</th>
                            <th class="text-center">Alumnos</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($c->clave_curso); ?></td>
                                <td><?php echo e($c->nombre_curso); ?></td>
                                <td><?php echo e($c->materia?->nombre_asignatura ?? $c->materias->first()?->materia?->nombre_asignatura ?? 'Materia no asignada'); ?></td>
                                <td class="small">
                                    <?php $__empty_1 = true; $__currentLoopData = $c->docentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div><?php echo e($docente->nombre_completo ?: $docente->clave_profesor); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="text-muted">Sin maestro asignado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <?php if($c->desde || $c->hasta || $c->sesiones): ?>
                                        <div>
                                            <?php echo e($c->desde?->format('d/m/Y') ?? 'Sin inicio'); ?>

                                            al
                                            <?php echo e($c->hasta?->format('d/m/Y') ?? 'Sin fin'); ?>

                                        </div>
                                        <div class="text-muted">
                                            <?php echo e($c->sesiones ?? 0); ?> sesiones · <?php echo e($c->turno_nombre); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Sin horario propio</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($c->nivelRel?->descripcion ?? $c->plan?->nivelRel?->descripcion ?? $c->nivel ?? 'Nivel no asignado'); ?></td>
                                <td><?php echo e($c->turno_nombre); ?></td>
                                <td><?php echo e($c->sede?->descripcion ?? $c->id_campus); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo e($c->alumnos_count); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge--status <?php echo e($c->activo ? 'badge--active' : 'badge--inactive'); ?>">
                                        <?php echo e($c->activo ? 'Activo' : 'Inactivo'); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('academia.cursos.show', $c)); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php echo e($cursos->withQueryString()->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\cursos\index.blade.php ENDPATH**/ ?>