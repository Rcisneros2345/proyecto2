<?php $__env->startSection('title', "Editar {$device->name}"); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Dispositivos › Editar'); ?>

<?php
    $statusColor = match($device->status) {
        'online' => 'green',
        'offline' => 'red',
        default => 'gray',
    };
    $statusIcon = match($device->status) {
        'online' => 'bi-wifi',
        'offline' => 'bi-wifi-off',
        default => 'bi-question-circle',
    };
    $statusLabel = \App\Models\Device::states()[$device->status] ?? $device->status;
?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Editar '.e($device->name).'','subtitle' => 'Actualiza la configuración de conexión del dispositivo.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar '.e($device->name).'','subtitle' => 'Actualiza la configuración de conexión del dispositivo.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('devices.show', $device)); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
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


<div class="device-state-zone" role="region" aria-label="Estado actual del dispositivo <?php echo e($device->name); ?>">
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-hdd-network me-1" aria-hidden="true"></i>Dispositivo</span>
        <span class="ds-value"><?php echo e($device->name); ?></span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Conexión</span>
        <span class="ds-value mono"><?php echo e($device->ip); ?>:<?php echo e($device->port); ?></span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-circle-fill me-1" aria-hidden="true"></i>Estado</span>
        <span class="ds-value"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => $statusColor,'dot' => true,'icon' => ''.e($statusIcon).'','label' => $statusLabel,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusColor),'dot' => true,'icon' => ''.e($statusIcon).'','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusLabel),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-people me-1" aria-hidden="true"></i>Empleados</span>
        <span class="ds-value mono"><?php echo e($device->employees_count); ?></span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-calendar-check me-1" aria-hidden="true"></i>Registros</span>
        <span class="ds-value mono"><?php echo e($device->attendances_count); ?></span>
    </div>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-fingerprint me-1" aria-hidden="true"></i>Huellas</span>
        <span class="ds-value mono"><?php echo e($device->fingerprints_count); ?></span>
    </div>
    <?php if($device->latestSync?->finished_at): ?>
    <div class="ds-item">
        <span class="ds-label"><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Última sincronización</span>
        <span class="ds-value"><?php echo e($device->latestSync->finished_at->format('d/m/Y H:i')); ?></span>
    </div>
    <?php endif; ?>
</div>

<div class="card shadow-sm col-lg-6">
    <div class="card-body">
        <form action="<?php echo e(route('devices.update', $device)); ?>" method="POST" novalidate>
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Identificación del dispositivo</legend>

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name"
                           value="<?php echo e(old('name', $device->name)); ?>" required aria-required="true">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </fieldset>

            
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Datos de conexión</legend>

                <div class="mb-3">
                    <label for="ip" class="form-label">Dirección IP <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                    <input type="text" class="form-control <?php $__errorArgs = ['ip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ip" name="ip"
                           value="<?php echo e(old('ip', $device->ip ?? env('ZKTECO_DEFAULT_IP'))); ?>" required aria-required="true">
                    <div class="form-text">IP actual: <code><?php echo e($device->ip ?? env('ZKTECO_DEFAULT_IP', 'No configurado')); ?></code></div>
                    <?php $__errorArgs = ['ip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="port" class="form-label">Puerto <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                        <input type="number" class="form-control <?php $__errorArgs = ['port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="port" name="port"
                               value="<?php echo e(old('port', $device->port)); ?>" required aria-required="true">
                        <?php $__errorArgs = ['port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="password" class="form-label">Contraseña / CLAVE</label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password"
                               name="password" value="<?php echo e(old('password', $device->password)); ?>">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Dejar vacío para mantener la actual.</div>
                    </div>
                </div>
            </fieldset>

            
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Información adicional</legend>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description"
                              name="description" rows="2"><?php echo e(old('description', $device->description)); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </fieldset>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Actualizar configuración
                </button>
                <a href="<?php echo e(route('devices.show', $device)); ?>" class="btn btn-link">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\devices\edit.blade.php ENDPATH**/ ?>