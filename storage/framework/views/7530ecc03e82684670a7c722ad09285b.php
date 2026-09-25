

<?php $__env->startSection('title', 'Editar empleado'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Empleados › Editar'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalDevicesCount = $totalDevices ?? $employee->devices->count();
    $latestSync = $employee->syncs->first();
    $syncStatusColors = ['completed' => 'green', 'failed' => 'red', 'running' => 'amber', 'queued' => 'gray'];
    $latestSyncColor = $latestSync ? ($syncStatusColors[$latestSync->status] ?? 'gray') : 'gray';
    $latestSyncValue = $latestSync
        ? ucfirst($latestSync->status) . ' · ' . ($latestSync->created_at?->format('d/m H:i') ?? '')
        : '—';
?>

<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Editar empleado: '.e($employee->name).'','subtitle' => 'ID '.e($employee->user_id).' · '.e($employee->type_label ?? 'Sin tipo').''.e($employee->departamento ? ' · ' . $employee->departamento : '').'','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar empleado: '.e($employee->name).'','subtitle' => 'ID '.e($employee->user_id).' · '.e($employee->type_label ?? 'Sin tipo').''.e($employee->departamento ? ' · ' . $employee->departamento : '').'','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-secondary">
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

<!-- ═══ ZONA DE ESTADO — Resumen visual del empleado ═══ -->
<?php
    $stateFpCount = $employee->fingerprints->unique('finger')->count();
    $stateFpColor = $stateFpCount === 0 ? 'gray' : ($stateFpCount < 3 ? 'amber' : 'green');
    $stateDeviceCount = $employee->devices->count();
    $stateDevicesEnrolled = $employee->devices->count();
    $stateHasCard = $employee->devices->contains(fn($d) => filled($d->pivot->card_number));
    $cardNumbers = $employee->devices->filter(fn($d) => filled($d->pivot->card_number))->pluck('card_number')->filter()->values();
    $stateSyncLabel = $latestSync ? ucfirst($latestSync->status) : 'Nunca ejecutada';
    $stateSyncIcon = match($latestSync?->status) {
        'completed' => 'bi-check-circle-fill',
        'failed' => 'bi-x-circle-fill',
        'running', 'queued' => 'bi-hourglass-split',
        default => 'bi-dash-circle',
    };
    $stateSyncColor = match($latestSync?->status) {
        'completed' => 'var(--cat-green)',
        'failed' => 'var(--cat-red)',
        'running', 'queued' => 'var(--cat-amber)',
        default => 'var(--cat-gray)',
    };
?>
<div class="device-state-zone mb-4" role="region" aria-label="Estado del empleado">
    
    <div class="ds-item">
        <span class="ds-label">Nombre</span>
        <span class="ds-value"><?php echo e($employee->name); ?></span>
    </div>

    
    <div class="ds-item">
        <span class="ds-label">Estado</span>
        <span class="ds-value">
            <?php if($employee->status_actual === 'B'): ?>
                <span style="color: var(--cat-red);">&#x1F534; Baja</span>
            <?php else: ?>
                <span style="color: var(--cat-green);">&#x1F7E2; Activo</span>
            <?php endif; ?>
        </span>
    </div>

    
    <div class="ds-item">
        <span class="ds-label">Huellas</span>
        <span class="ds-value">
            <?php if($stateFpCount > 0): ?>
                <span class="dot-indicator" aria-label="<?php echo e($stateFpCount); ?> huellas">
                    <?php for($i = 0; $i < min($stateFpCount, 5); $i++): ?>
                        <span class="dot filled cat-<?php echo e($stateFpColor); ?>"></span>
                    <?php endfor; ?>
                </span>
                <span class="mono" style="color: var(--cat-<?php echo e($stateFpColor); ?>);"><?php echo e($stateFpCount); ?></span>
            <?php else: ?>
                <span class="text-tertiary-token">Sin enrolamiento</span>
            <?php endif; ?>
        </span>
    </div>

    
    <div class="ds-item">
        <span class="ds-label">Dispositivos</span>
        <span class="ds-value">
            <?php if($stateDeviceCount > 0): ?>
                <span class="dot-indicator" aria-label="<?php echo e($stateDeviceCount); ?> de <?php echo e($totalDevicesCount); ?> dispositivos">
                    <?php for($i = 0; $i < min($totalDevicesCount, 5); $i++): ?>
                        <span class="dot <?php echo e($i < $stateDeviceCount ? 'filled cat-blue' : ''); ?>"></span>
                    <?php endfor; ?>
                </span>
                <span class="mono" style="color: var(--cat-blue);"><?php echo e($stateDeviceCount); ?><span class="text-tertiary-token">/<?php echo e($totalDevicesCount); ?></span></span>
            <?php else: ?>
                <span class="text-tertiary-token">Sin enrolar</span>
            <?php endif; ?>
        </span>
    </div>

    
    <div class="ds-item">
        <span class="ds-label">Sincronización</span>
        <span class="ds-value">
            <i class="<?php echo e($stateSyncIcon); ?>" style="color: <?php echo e($stateSyncColor); ?>;"></i>
            <span style="color: <?php echo e($stateSyncColor); ?>;"><?php echo e($stateSyncLabel); ?></span>
            <?php if($latestSync): ?>
                <span class="ds-value mono text-tertiary-token" style="font-size: 12px; font-weight: 400;">
                    <?php echo e($latestSync->finished_at?->format('d/m H:i') ?? $latestSync->created_at?->format('d/m H:i')); ?>

                </span>
            <?php endif; ?>
        </span>
    </div>

    
    <?php if($stateHasCard): ?>
        <div class="ds-item">
            <span class="ds-label">Tarjeta RFID</span>
            <span class="ds-value mono">
                <i class="bi bi-credit-card me-1 text-tertiary-token"></i>
                <?php echo e($cardNumbers->implode(', ')); ?>

            </span>
        </div>
    <?php endif; ?>
</div>

<!-- KPI Grid (4 cards) -->
<div class="kpi-grid mb-4">
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['icon' => 'bi-fingerprint','value' => $employee->fingerprints->unique('finger')->count(),'label' => 'Huellas disponibles','color' => 'purple']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-fingerprint','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employee->fingerprints->unique('finger')->count()),'label' => 'Huellas disponibles','color' => 'purple']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['icon' => 'bi-hdd-network','value' => $employee->devices->count() . '/' . $totalDevicesCount,'label' => 'Dispositivos enrolados','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-hdd-network','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employee->devices->count() . '/' . $totalDevicesCount),'label' => 'Dispositivos enrolados','color' => 'blue']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['icon' => 'bi-card-text','value' => $employee->devices->filter(fn($d)=>filled($d->pivot->card_number))->count(),'label' => 'Tarjetas asignadas','color' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-card-text','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employee->devices->filter(fn($d)=>filled($d->pivot->card_number))->count()),'label' => 'Tarjetas asignadas','color' => 'green']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['icon' => 'bi-clock','value' => $latestSyncValue,'label' => 'Último sync','color' => $latestSyncColor]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-clock','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($latestSyncValue),'label' => 'Último sync','color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($latestSyncColor)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
</div>

<!-- Bootstrap Tabs -->
<div class="row g-4 align-items-start">
    <div class="col-12">
        <ul class="nav nav-tabs flex-nowrap overflow-auto" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-identidad-btn" data-bs-toggle="tab" data-bs-target="#pane-identidad" type="button" role="tab" aria-controls="pane-identidad" aria-selected="true">Identidad</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-enrolamientos-btn" data-bs-toggle="tab" data-bs-target="#pane-enrolamientos" type="button" role="tab" aria-controls="pane-enrolamientos" aria-selected="false">Enrolamientos</button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content -->
<div class="row g-4 align-items-start">

    
    <div class="col-12 col-xl-8">
        <div class="tab-content">

            
            <div class="tab-pane fade show active" id="pane-identidad" role="tabpanel" aria-labelledby="tab-identidad-btn">

                
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><i class="bi bi-person me-2 text-tertiary-token"></i>Identidad</h2>
                        <span class="small text-tertiary-token"><i class="bi bi-info-circle me-1"></i>Catálogo central</span>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('employees.update', $employee)); ?>" method="POST" novalidate data-guard-submit>
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('name', $employee->name)); ?>" maxlength="24" required
                                           aria-describedby="name-help" autocomplete="name">
                                    <div id="name-help" class="form-text">Máximo 24 caracteres. Se refleja en todos los checadores tras sincronizar.</div>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="user_id_display" class="form-label">ID / Badge</label>
                                    <input type="text" id="user_id_display" class="form-control" value="<?php echo e($employee->user_id); ?>" readonly disabled>
                                    <div class="form-text">No editable aquí. Si necesitas cambiar badge, duplica/reenrola (escalar a soporte).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado catálogo</label>
                                    <div class="pt-1">
                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => $employee->status_actual === 'B' ? 'gray' : 'green','label' => $employee->status_actual_label ?? ($employee->status_actual === 'B' ? 'Baja' : 'Activo')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employee->status_actual === 'B' ? 'gray' : 'green'),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($employee->status_actual_label ?? ($employee->status_actual === 'B' ? 'Baja' : 'Activo'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                                    </div>
                                    <div class="form-text">Bajas se gestionan desde la tabla con confirmación.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="area_id" class="form-label">Área</label>
                                    <select id="area_id" name="area_id" class="form-select <?php $__errorArgs = ['area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Sin área</option>
                                        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($area->id); ?>" <?php if(old('area_id', $employee->area_id) == $area->id): echo 'selected'; endif; ?>>
                                                <?php echo e($area->identificador); ?> - <?php echo e($area->descripcion); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="puesto_id" class="form-label">Puesto</label>
                                    <select id="puesto_id" name="puesto_id" class="form-select <?php $__errorArgs = ['puesto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Sin puesto</option>
                                        <?php $__currentLoopData = $puestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($puesto->id); ?>" <?php if(old('puesto_id', $employee->puesto_id) == $puesto->id): echo 'selected'; endif; ?>>
                                                <?php echo e($puesto->identificador); ?> - <?php echo e($puesto->descripcion); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['puesto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="auth_user_id" class="form-label">Usuario de acceso</label>
                                    <select id="auth_user_id" name="auth_user_id" class="form-select <?php $__errorArgs = ['auth_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Sin usuario vinculado</option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>" <?php if(old('auth_user_id', $employee->auth_user_id) == $user->id): echo 'selected'; endif; ?>
                                            ><?php echo e($user->name); ?><?php echo e($user->username ? ' · ' . $user->username : ''); ?> (<?php echo e($user->email); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="form-text">Este vínculo determina la identidad para permisos e incidencias.</div>
                                    <?php $__errorArgs = ['auth_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-save me-1"></i> Guardar identidad</button>
                            </div>
                        </form>
                    </div>
                </div>

                
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><i class="bi bi-shield-lock me-2 text-tertiary-token"></i>Credenciales de acceso</h2>
                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'amber','icon' => 'bi-exclamation-circle','label' => 'Propaga a ' . ($employee->devices->count() ?: '—') . ' checadores al sincronizar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'amber','icon' => 'bi-exclamation-circle','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Propaga a ' . ($employee->devices->count() ?: '—') . ' checadores al sincronizar')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('employees.update', $employee)); ?>" method="POST" id="employee-credentials-form" novalidate data-guard-submit>
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="name" value="<?php echo e(old('name', $employee->name)); ?>" data-sync-name>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">PIN / Contraseña</label>
                                    <input type="password" id="password" name="password"
                                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           maxlength="8" inputmode="numeric" pattern="[0-9]{1,8}"
                                           placeholder="Conservar actual" aria-describedby="password-help" autocomplete="off">
                                    <div id="password-help" class="form-text">Solo números, 1–8 dígitos. Vacío = no cambia. Se aplica al sincronizar.</div>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Rol en checador</label>
                                    <select id="role" name="role" class="form-select <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" aria-describedby="role-help">
                                        <?php $currentRole = old('role', $employee->devices->first()?->pivot->role ?? 0); ?>
                                        <?php $__currentLoopData = \App\Models\Employee::roles(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php if($currentRole == $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div id="role-help" class="form-text">0 = Usuario · 13 = Supervisor · 14 = Admin.</div>
                                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 small mt-3 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Nombre, PIN y rol se guardan en el catálogo y se <strong>propagan a los <?php echo e($employee->devices->count()); ?> checador(es) enrolados</strong> solo al ejecutar "Sincronizar". Si no está enrolado, quedan solo en catálogo.
                            </div>
                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            
            <div class="tab-pane fade" id="pane-enrolamientos" role="tabpanel" aria-labelledby="tab-enrolamientos-btn">

                
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><i class="bi bi-hdd-network me-2 text-tertiary-token"></i>Dispositivos enrolados · <?php echo e($employee->devices->count()); ?></h2>
                        <?php if($employee->devices->isNotEmpty()): ?>
                            <span class="small text-tertiary-token">Tarjeta por checador</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if($employee->devices->isNotEmpty()): ?>
                            <div class="table-responsive mb-3">
                                <table class="table table-hover align-middle mb-0 table-cards" aria-label="Resumen de enrolamientos por dispositivo">
                                    <thead>
                                        <tr>
                                            <th style="min-width:180px" scope="col">Dispositivo</th>
                                            <th style="min-width:70px" scope="col">UID</th>
                                            <th style="min-width:110px" scope="col">Rol</th>
                                            <th style="min-width:110px" scope="col">Tarjeta</th>
                                            <th style="min-width:80px" scope="col">Huellas</th>
                                            <th style="min-width:90px" scope="col">Activo</th>
                                            <th style="min-width:130px" scope="col">Sync</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $fpN = $device->pivot->fingerprint_count ?? 0;
                                                $devSync = $employee->syncs->firstWhere('device_id', $device->id);
                                                $devSyncColor = $devSync ? (['completed' => 'green', 'failed' => 'red', 'running' => 'amber', 'queued' => 'gray'][$devSync->status] ?? 'gray') : 'gray';
                                            ?>
                                            <tr>
                                                <td data-label="Dispositivo">
                                                    <a href="<?php echo e(route('devices.show', $device)); ?>" class="ref-chip" title="Ver <?php echo e($device->name); ?>">
                                                        <i class="bi bi-hdd-network"></i><?php echo e($device->name); ?>

                                                    </a>
                                                </td>
                                                <td data-label="UID"><span class="mono small"><?php echo e($device->pivot->device_uid); ?></span></td>
                                                <td data-label="Rol"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'gray','label' => $device->pivot->roleLabel()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($device->pivot->roleLabel())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></td>
                                                <td data-label="Tarjeta">
                                                    <?php if(filled($device->pivot->card_number)): ?>
                                                        <span class="mono small"><?php echo e($device->pivot->card_number); ?></span>
                                                    <?php else: ?>
                                                        <span class="small text-tertiary-token">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Huellas">
                                                    <?php echo $__env->make('employees.partials._fingerprint-badge', ['fpCount' => $fpN], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                </td>
                                                <td data-label="Activo">
                                                    <?php if($device->pivot->active): ?>
                                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'green','label' => 'Sí']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'green','label' => 'Sí']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                                                    <?php else: ?>
                                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'gray','label' => 'No']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','label' => 'No']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Sync">
                                                    <?php if($devSync): ?>
                                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => $devSyncColor,'label' => $devSync->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($devSyncColor),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($devSync->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                                                        <span class="mono small text-tertiary-token"><?php echo e($devSync->created_at?->format('d/m H:i')); ?></span>
                                                    <?php else: ?>
                                                        <span class="small text-tertiary-token">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <h3 class="h6 small text-tertiary-token text-uppercase mb-2">Tarjeta por checador</h3>
                        <?php endif; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="employee-device-entry">
                                <div class="employee-device-meta">
                                    <a href="<?php echo e(route('devices.show', $device)); ?>" class="ref-chip" title="UID <?php echo e($device->pivot->device_uid); ?> en <?php echo e($device->name); ?>">
                                        <i class="bi bi-hdd-network"></i><?php echo e($device->name); ?>

                                    </a>
                                    <span class="mono text-secondary-token small">UID <?php echo e($device->pivot->device_uid); ?></span>
                                </div>
                                <form action="<?php echo e(route('employees.update-card', $employee)); ?>" method="POST" class="mt-2" novalidate data-guard-submit>
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="device_id" value="<?php echo e($device->id); ?>">
                                    <label class="form-label small mb-1" for="card-<?php echo e($device->id); ?>">Código de tarjeta</label>
                                    <div class="input-group" style="max-width: 380px">
                                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                        <input type="text" id="card-<?php echo e($device->id); ?>" name="card_number"
                                               class="form-control <?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               value="<?php echo e(old('card_number', $device->pivot->card_number)); ?>"
                                               inputmode="numeric" maxlength="10" pattern="[0-9]+"
                                               placeholder="Sin tarjeta" aria-label="Código de tarjeta para <?php echo e($device->name); ?>">
                                        <button class="btn btn-outline-primary" type="submit">Guardar</button>
                                    </div>
                                    <?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">Solo números, máx. 10 dígitos.</div>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="alert alert-warning py-2 small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Este empleado no está enrolado en ningún checador; los cambios se guardarán solo en el catálogo.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if($syncDevices->isNotEmpty()): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2">
                        <h2 class="h6 mb-0"><i class="bi bi-send me-2 text-tertiary-token"></i>Enviar a dispositivos</h2>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-select-all-devices>Seleccionar todos</button>
                    </div>
                    <div class="card-body">
                        <?php echo $__env->make('employees.partials._sync-devices-form', [
                            'employee' => $employee,
                            'syncDevices' => $syncDevices,
                            'formId' => 'sync-devices-form',
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php echo $__env->make('employees.partials._sync-progress-panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </div>

        </div>
    </div>

    
    <div class="col-12 col-xl-4">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0"><i class="bi bi-fingerprint me-2"></i>Huellas guardadas</h2>
                <?php
                    $asideFpCount = $employee->fingerprints->count();
                ?>
                <?php echo $__env->make('employees.partials._fingerprint-badge', ['fpCount' => $asideFpCount, 'fpMax' => $employee->fingerprints->unique('finger')->count() ?: null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $employee->fingerprints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fingerprint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="employee-fingerprint-row">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar is-sm" style="background: var(--primary-soft); color: var(--primary)"><i class="bi bi-fingerprint"></i></span>
                                <div>
                                    <div class="fw-semibold small">Dedo <?php echo e($fingerprint->finger); ?></div>
                                    <span class="badge cat-gray"><?php echo e($fingerprint->device?->name ?? 'Origen desconocido'); ?></span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <?php if($fingerprint->device_id && $employee->devices->count() > 1): ?>
                                    <form action="<?php echo e(route('employees.copy-fingerprint', [$employee, $fingerprint])); ?>" method="POST" class="d-flex gap-1 flex-grow-1" style="max-width: 260px">
                                        <?php echo csrf_field(); ?>
                                        <select name="target_device_id" class="form-select form-select-sm" aria-label="Checador destino para dedo <?php echo e($fingerprint->finger); ?>" required>
                                            <option value="">Copiar a…</option>
                                            <?php $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $targetDevice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($targetDevice->id !== $fingerprint->device_id): ?>
                                                    <option value="<?php echo e($targetDevice->id); ?>"><?php echo e($targetDevice->name); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="submit" title="Copiar huella y conservar original" aria-label="Copiar huella y conservar original"><i class="bi bi-copy"></i></button>
                                    </form>
                                <?php endif; ?>
                                <form action="<?php echo e(route('employees.delete-fingerprint', [$employee, $fingerprint])); ?>" method="POST"
                                      data-confirm data-confirm-danger
                                      data-confirm-title="¿Quitar esta huella?"
                                      data-confirm-message="Se eliminará el dedo <?php echo e($fingerprint->finger); ?> del empleado y del checador de origen.">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-icon-danger" type="submit" title="Quitar huella"><i class="bi bi-trash me-1"></i>Quitar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php echo $__env->make('partials.empty-state', [
                        'icon' => 'bi-fingerprint',
                        'title' => 'Sin huellas sincronizadas',
                        'desc' => 'Este empleado no tiene huellas. Extrae desde el checador o enrola biométricamente.',
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(auth()->user()->isAdmin() && $employee->devices->isNotEmpty()): ?>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <?php $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <form action="<?php echo e(route('devices.sync-fingerprints', $device)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="employee_id" value="<?php echo e($employee->id); ?>">
                                <button class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-repeat me-1"></i> Actualizar desde <?php echo e($device->name); ?></button>
                            </form>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($employee->syncs->isNotEmpty()): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h6 mb-0"><i class="bi bi-clock-history me-2 text-tertiary-token"></i>Historial de sincronización</h2>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $employee->syncs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sync): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $syncColor = ['completed'=>'green','failed'=>'red','running'=>'amber','queued'=>'gray'][$sync->status] ?? 'gray'; ?>
                        <div class="list-group-item d-flex align-items-center gap-3 py-3">
                            <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => $syncColor,'label' => strtoupper(substr($sync->status,0,1))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($syncColor),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(strtoupper(substr($sync->status,0,1)))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small text-truncate"><?php echo e($sync->device?->name ?? 'Dispositivo eliminado'); ?> · <?php echo e($sync->operation_label); ?></div>
                                <div class="small text-tertiary-token text-truncate"><?php echo e($sync->stage); ?> · <?php echo e($sync->created_at?->format('d/m/Y H:i')); ?><?php if($sync->error_message): ?> · <?php echo e($sync->error_message); ?><?php endif; ?></div>
                            </div>
                            <span class="mono small text-secondary-token"><?php echo e($sync->processed); ?>/<?php echo e($sync->total); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="card-footer bg-transparent text-center py-2">
                    <a href="<?php echo e(route('operations.queue')); ?>" class="btn btn-sm btn-ghost">Ver cola completa <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="card shadow-sm border" style="border-color: var(--border) !important;">
            <div class="card-body">
                <h3 class="h6 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Zona de riesgo</h3>
                <p class="small text-secondary-token mb-3">Dar de baja elimina accesos en todos los checadores. Se conserva histórico de checadas.</p>
                <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST"
                      data-confirm data-confirm-danger
                      data-confirm-title="¿Quitar a <?php echo e($employee->name); ?>?"
                      data-confirm-message="Se dará de baja en todos sus checadores y, si no queda enrolado en ninguno, también del catálogo. Sus checadas históricas se conservan.">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-person-x me-1"></i> Dar de baja empleado</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('employees.partials._sync-preview-drawer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Sincronizar input name visible con hidden de credenciales
document.getElementById('name')?.addEventListener('input', e => {
    const h = document.querySelector('[data-sync-name]');
    if (h) h.value = e.target.value;
});
document.querySelector('[data-select-all-devices]')?.addEventListener('click', (event) => {
    const cbs = document.querySelectorAll('.employee-device-select input[type="checkbox"]');
    const anyUnchecked = [...cbs].some(cb => !cb.checked);
    cbs.forEach(cb => cb.checked = anyUnchecked);
    event.currentTarget.textContent = anyUnchecked ? 'Quitar selección' : 'Seleccionar todos';
});
// Spinner on submit para sync forms + guardado (update/update-card)
document.querySelectorAll('form[action*="/sync-"], form[data-guard-submit]').forEach(form => {
    form.addEventListener('submit', () => {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Procesando…';
    });
});

// ── Preview diff antes de sincronizar (abre drawer, single fetch) ──
(function initSyncPreview() {
    const syncForm = document.getElementById('sync-devices-form');
    const offcanvasEl = document.getElementById('syncDiffOffcanvas');
    if (!syncForm || !offcanvasEl || typeof bootstrap === 'undefined') return;
    const diffTable = document.getElementById('sync-diff-table');
    const diffLoading = document.getElementById('sync-diff-loading');
    const diffError = document.getElementById('sync-diff-error');
    const confirmIds = document.getElementById('sync-confirm-ids');
    const confirmBtn = document.getElementById('sync-confirm-btn');
    const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
    const DOT = { create: 'cat-blue', update: 'cat-amber', noop: 'cat-gray' };
    const LABEL = { create: 'Alta', update: 'Actualizar', noop: 'Sin cambios' };
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));

    syncForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const ids = [...syncForm.querySelectorAll('input[name="device_ids[]"]:checked')].map((cb) => cb.value);
        if (!ids.length) {
            window.dashToast?.({ type: 'warning', title: 'Sin checadores', message: 'Selecciona al menos un checador destino.' });
            return;
        }
        diffTable.innerHTML = '';
        diffError.style.display = 'none';
        diffLoading.style.display = '';
        confirmBtn.disabled = true;
        offcanvas.show();
        try {
            const url = new URL(syncForm.dataset.diffUrl, window.location.origin);
            ids.forEach((id) => url.searchParams.append('device_ids[]', id));
            const resp = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();
            const items = data.diff || [];
            diffTable.innerHTML = items.length ? items.map((item) => {
                const changed = (item.changes || []).filter((c) => c.status === 'Cambia');
                const list = changed.length
                    ? '<ul class="mb-0 ps-3 small">' + changed.map((c) => `<li><strong>${esc(c.field)}</strong>: ${esc(c.from)} → ${esc(c.to)}</li>`).join('') + '</ul>'
                    : '<span class="small text-tertiary-token">Sin cambios</span>';
                const warn = (item.warnings || []).includes('card_duplicate')
                    ? '<div class="small text-warning mt-1"><i class="bi bi-exclamation-triangle me-1"></i>Tarjeta duplicada en este checador</div>'
                    : '';
                return `<tr><td class="small">${esc(item.device_name)}</td><td><span class="badge ${DOT[item.action] || 'cat-gray'}">${LABEL[item.action] || esc(item.action)}</span></td><td>${list}${warn}</td></tr>`;
            }).join('') : '<tr><td colspan="3" class="small text-tertiary-token">Sin diferencias.</td></tr>';
            confirmIds.innerHTML = ids.map((id) => `<input type="hidden" name="device_ids[]" value="${esc(id)}">`).join('');
            confirmBtn.disabled = false;
        } catch (err) {
            diffError.style.display = '';
        } finally {
            diffLoading.style.display = 'none';
        }
    });
})();

// ── Progreso de sincronización (polling 3s, patrón devices/show) ──
(function initSyncProgress() {
    const panel = document.getElementById('sync-progress-panel');
    if (!panel || panel.style.display === 'none') return; // sin syncs activos: cero requests
    const list = document.getElementById('sync-progress-list');
    const done = document.getElementById('sync-progress-done');
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
    let timer = null;

    async function tick() {
        let data = null;
        try {
            const resp = await fetch(panel.dataset.url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!resp.ok) return;
            data = await resp.json();
        } catch (e) { return; } // reintenta en el siguiente ciclo
        const syncs = (data.syncs || []).slice(0, 6);
        list.innerHTML = syncs.map((s) => {
            const pct = s.total > 0 ? Math.min(100, Math.round((s.processed / s.total) * 100)) : 5;
            const badge = s.is_terminal ? (s.status === 'completed' ? 'cat-green' : 'cat-red') : 'cat-amber';
            const err = (!s.is_terminal || s.status !== 'failed') || !s.error_message ? '' : `<div class="small text-danger mt-1">${esc(s.error_message)}</div>`;
            return `<div class="list-group-item"><div class="d-flex justify-content-between align-items-center gap-2 mb-1">`
                + `<span class="fw-semibold small">${esc(s.device_name)}</span><span class="badge ${badge}">${esc(s.status)}</span></div>`
                + `<div class="progress" style="height: 6px;" role="progressbar" aria-label="Progreso en ${esc(s.device_name)}"><div class="progress-bar" style="width: ${pct}%"></div></div>`
                + `<div class="small text-tertiary-token mt-1">${esc(s.stage)} · ${s.processed}/${s.total}</div>${err}</div>`;
        }).join('');
        if (syncs.length && syncs.every((s) => s.is_terminal)) {
            clearInterval(timer);
            timer = null;
            syncs.forEach((s) => {
                if (s.status === 'completed') window.dashToast?.({ type: 'success', title: 'Sincronizado', message: s.device_name });
                else if (s.status === 'failed') window.dashToast?.({ type: 'error', title: 'Falló sincronización', message: (s.device_name + (s.error_message ? ': ' + s.error_message : '')) });
            });
            done.style.display = '';
        }
    }

    timer = setInterval(tick, 3000);
    tick();
})();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/edit.blade.php ENDPATH**/ ?>