

<?php $__env->startSection('title', 'Asignar empleados al grupo'); ?>
<?php $__env->startSection('breadcrumb', 'Administración › Grupos de permisos › Empleados'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Asignar empleados','subtitle' => 'Grupo: '.e($permissionGroup->name).'','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Asignar empleados','subtitle' => 'Grupo: '.e($permissionGroup->name).'','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('permission-groups.index')); ?>" class="btn btn-outline-secondary">Volver</a>
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

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo e(route('permission-groups.save-employees', $permissionGroup)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-cards">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="select-all-employees" class="form-check-input"></th>
                            <th>Nombre</th>
                            <th>ID</th>
                            <th>Área</th>
                            <th>Puesto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="">
                                    <input type="checkbox" name="employee_ids[]" value="<?php echo e($employee->id); ?>" class="form-check-input employee-checkbox" <?php echo e(in_array($employee->id, $selectedEmployeeIds, true) ? 'checked' : ''); ?>>
                                </td>
                                <td data-label="Nombre"><?php echo e($employee->name); ?></td>
                                <td data-label="ID"><?php echo e($employee->user_id); ?></td>
                                <td data-label="Área"><?php echo e($employee->area?->identificador ?? '—'); ?></td>
                                <td data-label="Puesto"><?php echo e($employee->puesto?->identificador ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay empleados registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Guardar asignación</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
    document.getElementById('select-all-employees')?.addEventListener('change', function () {
        document.querySelectorAll('.employee-checkbox').forEach(function (checkbox) {
            checkbox.checked = this.checked;
        }, this);
    });
</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\permission-groups\assign-employees.blade.php ENDPATH**/ ?>