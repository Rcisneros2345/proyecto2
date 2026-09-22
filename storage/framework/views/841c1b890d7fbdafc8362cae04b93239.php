

<?php $__env->startSection('title', 'Asignaciones de captura'); ?>
<?php $__env->startSection('breadcrumb', 'Administración > Usuarios > Captura de asistencia'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Asignaciones de captura','subtitle' => 'Define los niveles y sedes donde este usuario puede capturar asistencia de clase.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Asignaciones de captura','subtitle' => 'Define los niveles y sedes donde este usuario puede capturar asistencia de clase.']); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('preferencia.usuarios.edit', $user)); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al usuario
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
    <div class="card-header">
        <strong><?php echo e($user->name); ?></strong>
        <span class="text-muted"> · <?php echo e($user->email); ?></span>
    </div>
    <div class="card-body">
        <p class="text-muted small">Una fila autoriza la combinación indicada. Deja nivel o sede vacío para usarlo como comodín dentro del ciclo seleccionado.</p>
        <form method="POST" action="<?php echo e(route('preferencia.usuarios.captura-asistencia.update', $user)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Sede</th>
                            <th>Ciclo</th>
                            <th>Activa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rows = $assignments->values()->all(); ?>
                        <?php $defaultCycle = $ciclos->first(); ?>
                        <?php for($index = 0; $index < max(count($rows) + 2, 3); $index++): ?>
                            <?php $assignment = $rows[$index] ?? null; ?>
                            <?php $cycle = $assignment ?: $defaultCycle; ?>
                            <?php $isActive = $assignment ? (bool) $assignment->active : false; ?>
                            <tr>
                                <td>
                                    <select name="assignments[<?php echo e($index); ?>][nivel]" class="form-select">
                                        <option value="">Todos los niveles</option>
                                        <?php $__currentLoopData = $niveles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nivel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($nivel->nivel); ?>" <?php if(old("assignments.$index.nivel", $assignment?->nivel) === $nivel->nivel): echo 'selected'; endif; ?>><?php echo e($nivel->label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="assignments[<?php echo e($index); ?>][id_campus]" class="form-select">
                                        <option value="">Todas las sedes</option>
                                        <?php $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sede->id_campus); ?>" <?php if((string) old("assignments.$index.id_campus", $assignment?->id_campus) === (string) $sede->id_campus): echo 'selected'; endif; ?>><?php echo e($sede->descripcion); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="assignments[<?php echo e($index); ?>][ciclo]" class="form-select" data-cycle-select="<?php echo e($index); ?>">
                                        <option value="">Todos los ciclos</option>
                                        <?php $__currentLoopData = $ciclos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ciclo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $cycleValue = implode('-', [$ciclo->inicial, $ciclo->final, $ciclo->periodo]); ?>
                                            <option value="<?php echo e($cycleValue); ?>" <?php if($cycle && $cycle->inicial === $ciclo->inicial && $cycle->final === $ciclo->final && $cycle->periodo === $ciclo->periodo): echo 'selected'; endif; ?>><?php echo e($ciclo->label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="hidden" name="assignments[<?php echo e($index); ?>][inicial]" value="<?php echo e($cycle?->inicial); ?>">
                                    <input type="hidden" name="assignments[<?php echo e($index); ?>][final]" value="<?php echo e($cycle?->final); ?>">
                                    <input type="hidden" name="assignments[<?php echo e($index); ?>][periodo]" value="<?php echo e($cycle?->periodo); ?>">
                                </td>
                                <td>
                                    <input type="hidden" name="assignments[<?php echo e($index); ?>][active]" value="0">
                                    <input type="checkbox" name="assignments[<?php echo e($index); ?>][active]" value="1" class="form-check-input" <?php if(old("assignments.$index.active", $isActive)): echo 'checked'; endif; ?>>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="<?php echo e(route('preferencia.usuarios.edit', $user)); ?>" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar asignaciones</button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('[data-cycle-select]').forEach((select) => {
    select.addEventListener('change', () => {
        const row = select.closest('tr');
        const values = (select.value || '').split('-');
        ['inicial', 'final', 'periodo'].forEach((field, index) => {
            row.querySelector(`[name$="[${field}]"]`).value = values[index] || '';
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/preferencia/captura-asistencia.blade.php ENDPATH**/ ?>