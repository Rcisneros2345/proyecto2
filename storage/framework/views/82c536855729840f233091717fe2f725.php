<?php $__env->startSection('title', 'Huellas por empleado'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Huellas'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Huellas biométricas','subtitle' => 'Consulta qué empleados tienen plantillas guardadas y en qué dispositivo.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Huellas biométricas','subtitle' => 'Consulta qué empleados tienen plantillas guardadas y en qué dispositivo.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-ghost"><i class="bi bi-people me-1"></i> Ver empleados</a>
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

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['action' => route('fingerprints.index'),'clearUrl' => route('fingerprints.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('fingerprints.index')),'clear-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('fingerprints.index'))]); ?>
            <div class="col-md-5">
                <label for="q" class="form-label small mb-1">Empleado o ID</label>
                <input type="search" id="q" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="Buscar por nombre o ID">
            </div>
            <div class="col-md-4">
                <label for="device_id" class="form-label small mb-1">Dispositivo</label>
                <select id="device_id" name="device_id" class="form-select">
                    <option value="">Todos los dispositivos</option>
                    <?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($device->id); ?>" <?php if(request('device_id') == $device->id): echo 'selected'; endif; ?>><?php echo e($device->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
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

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-cards">
                <thead><tr><th>Empleado</th><th>Dispositivo</th><th>Huellas</th><th>Dedos registrados</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="Empleado"><span class="fw-semibold"><?php echo e($employee->name); ?></span><div class="small text-muted">ID <?php echo e($employee->user_id); ?> · <?php echo e($employee->devices->count()); ?> equipo(s)</div></td>
                            <td data-label="Dispositivo">
                                <?php $__empty_2 = true; $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <a href="<?php echo e(route('devices.show', $device)); ?>" class="ref-chip me-1 mb-1" title="UID <?php echo e($device->pivot->device_uid); ?>"><i class="bi bi-hdd-network"></i><?php echo e($device->name); ?></a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="text-secondary-token small">Sin enrolamiento</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Huellas"><span class="badge <?php echo e($employee->fingerprints_count ? 'cat-green' : 'cat-gray'); ?>"><?php echo e($employee->fingerprints_count); ?> guardadas</span></td>
                            <td data-label="Dedos registrados">
                                <?php $__empty_2 = true; $__currentLoopData = $employee->fingerprints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fingerprint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <span class="badge cat-blue me-1">Dedo <?php echo e($fingerprint->finger); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="text-muted small">Sin plantillas</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-sm btn-ghost" title="Ver empleado y huellas"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5"><?php echo $__env->make('partials.empty-state', ['icon' => 'bi-fingerprint', 'title' => 'No hay huellas guardadas', 'desc' => 'Sincroniza las huellas desde el detalle de un dispositivo para verlas aquí.'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3"><?php echo e($employees->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\fingerprints\index.blade.php ENDPATH**/ ?>