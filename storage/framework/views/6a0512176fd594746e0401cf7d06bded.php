
<?php
    $formId = $formId ?? 'sync-devices-form';
    $showSelectAll = $showSelectAll ?? true;
    $showSubmit = $showSubmit ?? true;
    $submitLabel = $submitLabel ?? 'Sincronizar seleccionados';
    $inlineClass = $inlineClass ?? '';
?>
<form id="<?php echo e($formId); ?>"
      action="<?php echo e(route('employees.sync-devices', $employee)); ?>"
      method="POST"
      data-diff-url="<?php echo e(route('employees.enrollment-diff', $employee)); ?>"
      class="<?php echo e($inlineClass); ?>">
    <?php echo csrf_field(); ?>
    <fieldset>
        <legend class="visually-hidden">Selecciona checadores destino</legend>
        <div class="employee-device-select-list">
            <?php $__currentLoopData = $syncDevices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $syncDevice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="employee-device-select">
                    <input type="checkbox" name="device_ids[]" value="<?php echo e($syncDevice->id); ?>"
                           <?php if($employee->devices->contains('id', $syncDevice->id)): echo 'checked'; endif; ?>>
                    <span>
                        <strong><?php echo e($syncDevice->name); ?></strong>
                        <small><?php echo e($syncDevice->ip); ?> · <?php echo e($employee->devices->contains('id', $syncDevice->id) ? 'Actualizar acceso' : 'Agregar acceso'); ?></small>
                    </span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </fieldset>
    <?php if($showSubmit): ?>
        <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-send me-1"></i> <?php echo e($submitLabel); ?></button>
        <div class="form-text mt-2">Verás qué cambiará en cada checador antes de confirmar. Envía nombre, PIN, tarjeta, rol y todas las huellas. Cada checador genera una tarea en la cola.</div>
    <?php endif; ?>
</form>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/partials/_sync-devices-form.blade.php ENDPATH**/ ?>