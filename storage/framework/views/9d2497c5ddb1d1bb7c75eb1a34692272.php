<?php ($item = $item ?? null); ?>
<div class="row g-3">
    <div class="col-md-4">
        <label for="section" class="form-label">Sección</label>
        <input id="section" name="section" class="form-control" value="<?php echo e(old('section', $item?->section)); ?>" required>
    </div>
    <div class="col-md-8">
        <label for="label" class="form-label">Etiqueta</label>
        <input id="label" name="label" class="form-control" value="<?php echo e(old('label', $item?->label)); ?>" required>
    </div>
    <div class="col-md-6">
        <label for="route_name" class="form-label">Nombre de ruta</label>
        <input id="route_name" name="route_name" class="form-control" placeholder="ej. employees.index" value="<?php echo e(old('route_name', $item?->route_name)); ?>" required>
    </div>
    <div class="col-md-6">
        <label for="icon" class="form-label">Icono Bootstrap</label>
        <input id="icon" name="icon" class="form-control" placeholder="bi-people" value="<?php echo e(old('icon', $item?->icon)); ?>">
    </div>
    <div class="col-md-4">
        <label for="module_id" class="form-label">Módulo</label>
        <select id="module_id" name="module_id" class="form-select">
            <option value="">Sin módulo</option>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($module->id); ?>" <?php if(old('module_id', $item?->module_id) == $module->id): echo 'selected'; endif; ?>><?php echo e($module->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="permission_action" class="form-label">Acción requerida</label>
        <select id="permission_action" name="permission_action" class="form-select">
            <?php $__currentLoopData = ['view', 'create', 'update', 'delete', 'approve', 'export', 'sync']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($action); ?>" <?php if(old('permission_action', $item?->permission_action ?? 'view') === $action): echo 'selected'; endif; ?>><?php echo e($action); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="sort_order" class="form-label">Orden</label>
        <input id="sort_order" name="sort_order" type="number" min="0" class="form-control" value="<?php echo e(old('sort_order', $item?->sort_order ?? 0)); ?>" required>
    </div>
    <div class="col-12 d-flex gap-4">
        <div class="form-check">
            <input id="active" name="active" type="checkbox" value="1" class="form-check-input" <?php if(old('active', $item?->active ?? true)): echo 'checked'; endif; ?>>
            <label for="active" class="form-check-label">Entrada activa</label>
        </div>
        <div class="form-check">
            <input id="admin_only" name="admin_only" type="checkbox" value="1" class="form-check-input" <?php if(old('admin_only', $item?->admin_only ?? false)): echo 'checked'; endif; ?>>
            <label for="admin_only" class="form-check-label">Solo administradores</label>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\navigation-items\form.blade.php ENDPATH**/ ?>