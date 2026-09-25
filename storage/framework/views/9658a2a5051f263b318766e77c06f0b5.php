<?php $__env->startSection('title', 'Empleados'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Empleados'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Empleados','subtitle' => 'Consulta, administración y estado de los empleados en la red biométrica.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Empleados','subtitle' => 'Consulta, administración y estado de los empleados en la red biométrica.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <?php if(auth()->user()->canAccessModule('dispositivos', 'view')): ?>
            <a href="<?php echo e(route('employees.sobrantes')); ?>" class="btn btn-outline-secondary btn-sm" title="Ver sobrantes en dispositivos" aria-label="Ver sobrantes">
                <i class="bi bi-exclamation-triangle me-1"></i> Sobrantes
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


<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('employees.index')); ?>" class="row g-3 align-items-end" id="employees-filter-form">
            <div class="col-lg-4 col-12">
                <label class="form-label small mb-1" for="employeeSearch">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-tertiary-token"></i></span>
                    <input type="search" name="q" id="employeeSearch" value="<?php echo e(request('q')); ?>"
                           class="form-control" placeholder="Buscar por nombre, ID o puesto"
                           aria-label="Buscar empleados" autocomplete="off">
                </div>
            </div>

            <?php if(!empty($cargos) && count($cargos)): ?>
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small mb-1" for="filterCargo">Puesto</label>
                    <select name="cargo" id="filterCargo" class="form-select" aria-label="Filtrar por puesto">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $cargos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php if(request('cargo') === $c): echo 'selected'; endif; ?>><?php echo e($c); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if(!empty($departamentos) && count($departamentos)): ?>
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small mb-1" for="filterDepto">Departamento</label>
                    <select name="departamento" id="filterDepto" class="form-select" aria-label="Filtrar por departamento">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php if(request('departamento') === $d): echo 'selected'; endif; ?>><?php echo e($d); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if(!empty($sedes) && count($sedes)): ?>
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small mb-1" for="filterSede">Sede</label>
                    <select name="id_campus" id="filterSede" class="form-select" aria-label="Filtrar por sede">
                        <option value="">Todas</option>
                        <?php $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id_campus); ?>" <?php if(request('id_campus') == $s->id_campus): echo 'selected'; endif; ?>><?php echo e($s->descripcion); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-lg-2 col-12 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                <?php if(request()->hasAny(['q', 'cargo', 'departamento', 'id_campus', 'sin_huella', 'sin_device'])): ?>
                    <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                <?php endif; ?>
            </div>

            
            <?php if(request()->hasAny(['q', 'cargo', 'departamento', 'id_campus', 'sin_huella', 'sin_device'])): ?>
                <div class="col-12 d-flex gap-2 flex-wrap align-items-center pt-2 border-top">
                    <span class="small text-tertiary-token me-1">Activos:</span>
                    <?php if(request('q')): ?>
                        <span class="badge cat-blue d-inline-flex align-items-center gap-1">
                            <i class="bi bi-search"></i> <?php echo e(request('q')); ?>

                            <a href="<?php echo e(route('employees.index', request()->except('q'))); ?>" class="ms-1 text-decoration-none" aria-label="Quitar filtro búsqueda">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if(request('cargo')): ?>
                        <span class="badge cat-purple d-inline-flex align-items-center gap-1">
                            <i class="bi bi-briefcase"></i> <?php echo e(request('cargo')); ?>

                            <a href="<?php echo e(route('employees.index', request()->except('cargo'))); ?>" class="ms-1 text-decoration-none" aria-label="Quitar filtro puesto">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if(request('departamento')): ?>
                        <span class="badge cat-blue d-inline-flex align-items-center gap-1">
                            <i class="bi bi-building"></i> <?php echo e(request('departamento')); ?>

                            <a href="<?php echo e(route('employees.index', request()->except('departamento'))); ?>" class="ms-1 text-decoration-none" aria-label="Quitar filtro departamento">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if(request('id_campus')): ?>
                        <span class="badge cat-green d-inline-flex align-items-center gap-1">
                            <i class="bi bi-geo-alt"></i> Sede <?php echo e(request('id_campus')); ?>

                            <a href="<?php echo e(route('employees.index', request()->except('id_campus'))); ?>" class="ms-1 text-decoration-none" aria-label="Quitar filtro sede">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if(request('sin_huella')): ?>
                        <span class="badge cat-amber d-inline-flex align-items-center gap-1">
                            <i class="bi bi-fingerprint"></i> Sin huellas
                            <a href="<?php echo e(route('employees.index', request()->except('sin_huella'))); ?>" class="ms-1 text-decoration-none" aria-label="Quitar filtro sin huellas">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if(request('sin_device')): ?>
                        <span class="badge cat-amber d-inline-flex align-items-center gap-1">
                            <i class="bi bi-hdd-network"></i> Sin enrolar
                            <a href="<?php echo e(route('employees.index', request()->except('sin_device'))); ?>" class="ms-1 text-decoration-none" aria-label="Quitar filtro sin enrolar">&times;</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
            <?php echo $__env->make('employees.partials._quick-filters', [
                'baseUrl' => route('employees.index'),
                'activeParams' => request()->query(),
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </form>
    </div>
</div>


<ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link <?php echo e($status === 'todos' ? 'active' : ''); ?>"
           href="<?php echo e(route('employees.index', array_merge(request()->query(), ['status' => 'todos']))); ?>"
           role="tab" aria-selected="<?php echo e($status === 'todos' ? 'true' : 'false'); ?>">
            Todos
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?php echo e($status === 'activos' ? 'active' : ''); ?>"
           href="<?php echo e(route('employees.index', array_merge(request()->query(), ['status' => 'activos']))); ?>"
           role="tab" aria-selected="<?php echo e($status === 'activos' ? 'true' : 'false'); ?>">
            Activos
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?php echo e($status === 'bajas' ? 'active' : ''); ?>"
           href="<?php echo e(route('employees.index', array_merge(request()->query(), ['status' => 'bajas']))); ?>"
           role="tab" aria-selected="<?php echo e($status === 'bajas' ? 'true' : 'false'); ?>">
            Bajas
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="<?php echo e(route('employees.sobrantes')); ?>" role="tab" aria-selected="false">
            <i class="bi bi-exclamation-triangle"></i>
            Sobrantes
            <?php if(($sobrantesStats['total'] ?? 0) > 0): ?>
                <span class="badge cat-amber ms-1"><?php echo e($sobrantesStats['total']); ?></span>
            <?php endif; ?>
        </a>
    </li>
</ul>


<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div id="employees-counter" class="small text-tertiary-token" aria-live="polite">
        <i class="bi bi-people me-1"></i> <?php echo e($employees->total()); ?> empleados
        <?php if(request()->hasAny(['q', 'cargo', 'departamento', 'id_campus'])): ?>
            · filtrado por
            <span class="text-secondary-token">
                <?php echo e(request('q') ?: request('cargo') ?: request('departamento') ?: ('sede ' . request('id_campus'))); ?>

            </span>
        <?php endif; ?>
    </div>
    <?php if(auth()->user()->canAccessModule('empleados', 'create')): ?>
        <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-primary btn-sm" title="Agregar nuevo empleado" aria-label="Agregar empleado">
            <i class="bi bi-plus-lg me-1"></i> Agregar empleado
        </a>
    <?php endif; ?>
</div>


<div class="card shadow-sm" id="employees-table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="employees-table" class="table table-hover align-middle mb-0 table-cards">
                <thead>
                    <tr>
                        <th style="min-width:220px" scope="col">
                            Empleado <i class="bi bi-arrow-down-up ms-1" style="font-size:10px;opacity:.45"></i>
                        </th>
                        <th style="min-width:230px" scope="col">Datos personales</th>
                        <th style="min-width:180px" scope="col">
                            Puesto <i class="bi bi-arrow-down-up ms-1" style="font-size:10px;opacity:.45"></i>
                        </th>
                        <th style="min-width:150px" scope="col">Adscripción</th>
                        <th style="min-width:180px" scope="col">Horario contratado</th>
                        <th style="min-width:210px" scope="col">Hardware</th>
                        <th style="min-width:110px" scope="col">Estado</th>
                        <th class="text-end" style="min-width:140px" scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody data-is-admin="<?php echo e(auth()->user()->isAdmin() ? '1' : '0'); ?>">
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            // ── Dispositivos y enrolamiento ──
                            $enrollment = $employee->devices->first();
                            $devicesCount = $employee->devices->count();

                            // ── Huellas (con fallbacks) ──
                            $fpCount = $employee->fingerprints_count ?? $employee->fingerprints->count() ?? 0;

                            // ── Estado Firebird ──
                            $fbStatus = $employee->status_actual ?? null;
                            $isBaja = $fbStatus === 'B';

                            // ── Estado enrolamiento ──
                            $enrolled = $devicesCount > 0;
                            $anyActive = $enrolled && $employee->devices->contains(fn($d) => $d->pivot->active);

                            // ── Color huellas por count (centralizado en _fingerprint-badge) ──

                            // ── Último sync (si viene con latestSync eager o syncs) ──
                            $lastSync = null;
                            if (isset($employee->syncs) && method_exists($employee->syncs, 'first')) {
                                $lastSync = $employee->syncs->first();
                            } elseif (isset($employee->latestSync)) {
                                $lastSync = $employee->latestSync;
                            }

                            // ── Sede label (si existe relación o fallback a id_campus) ──
                            $sedeLabel = '—';
                            if (isset($employee->sede) && $employee->sede) {
                                $sedeLabel = $employee->sede->descripcion ?? $employee->id_campus ?? '—';
                            } elseif (!empty($employee->id_campus)) {
                                $sedeLabel = $employee->id_campus;
                            }
                        ?>
                        <tr class="<?php echo e($fpCount === 0 ? 'row-attention-none' : ($fpCount < 3 ? 'row-attention-fp' : '')); ?>">
                            
                            <td data-label="Empleado">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="avatar is-sm flex-shrink-0"><?php echo e(strtoupper(mb_substr($employee->name, 0, 1))); ?></span>
                                    <div class="min-w-0">
                                        <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="fw-semibold text-decoration-none d-block text-truncate" title="<?php echo e($employee->name); ?>">
                                            <?php echo e($employee->name); ?>

                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <code class="small" title="ID: <?php echo e($employee->user_id); ?>"><?php echo e($employee->user_id); ?></code>
                                            <?php if($employee->numero_empleado && $employee->numero_empleado !== $employee->user_id): ?>
                                                <span class="small text-tertiary-token">No. <?php echo e($employee->numero_empleado); ?></span>
                                            <?php endif; ?>
                                            <?php if(!empty($employee->fecha_ingreso)): ?>
                                                <span class="mono small text-tertiary-token" title="Fecha ingreso <?php echo e(\Carbon\Carbon::parse($employee->fecha_ingreso)->format('d/m/Y')); ?>">
                                                    <?php echo e(\Carbon\Carbon::parse($employee->fecha_ingreso)->locale('es')->isoFormat('MMM YYYY')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            
                            <td data-label="Datos personales">
                                <div class="small fw-semibold"><i class="bi bi-person-vcard me-1"></i><?php echo e($employee->sexo_label); ?></div>
                                <?php if($employee->fecha_nacimiento): ?>
                                    <div class="small text-secondary-token">Nacimiento: <?php echo e($employee->fecha_nacimiento->format('d/m/Y')); ?></div>
                                <?php endif; ?>
                                <?php if($employee->nacionalidad || $employee->estado_civil): ?>
                                    <div class="small text-secondary-token">
                                        <?php echo e($employee->nacionalidad ?: 'Nacionalidad no indicada'); ?>

                                        <?php if($employee->estado_civil): ?> · <?php echo e($employee->estado_civil); ?> <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($employee->telefono || $employee->celular || $employee->email): ?>
                                    <div class="small text-secondary-token text-truncate" title="<?php echo e($employee->email); ?>">
                                        <?php echo e($employee->telefono ?: $employee->celular ?: $employee->email); ?>

                                    </div>
                                <?php endif; ?>
                            </td>

                            
                            <td data-label="Puesto">
                                <?php
                                    $puestoLabel = $employee->puesto?->descripcion ?? $employee->cargo;
                                    $areaLabel = $employee->area?->descripcion ?? $employee->departamento;
                                ?>
                                <?php if(!empty($puestoLabel)): ?>
                                    <div class="fw-semibold small text-truncate" title="<?php echo e($puestoLabel); ?>">
                                        <i class="bi bi-briefcase me-1 text-tertiary-token"></i><?php echo e($puestoLabel); ?>

                                    </div>
                                    <div class="small text-secondary-token text-truncate" title="<?php echo e($areaLabel); ?>">
                                        <?php if(!empty($areaLabel)): ?>
                                            <i class="bi bi-building me-1"></i><?php echo e($areaLabel); ?>

                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="small text-tertiary-token">—</span>
                                    <?php if(!empty($areaLabel)): ?>
                                        <div class="small text-secondary-token text-truncate" title="<?php echo e($areaLabel); ?>">
                                            <i class="bi bi-building me-1"></i><?php echo e($areaLabel); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            
                            <td data-label="Sede">
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <?php if($sedeLabel !== '—'): ?>
                                        <span class="badge cat-blue" title="<?php echo e($employee->id_campus ? 'ID_CAMPUS '.$employee->id_campus : ''); ?>">
                                            <i class="bi bi-geo-alt me-1"></i><?php echo e($sedeLabel); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="small text-tertiary-token">—</span>
                                    <?php endif; ?>
                                    <?php if(!empty($employee->contrato)): ?>
                                        <span class="badge cat-gray"><?php echo e($employee->contrato); ?></span>
                                    <?php endif; ?>
                                    <?php if(!empty($employee->nivel)): ?>
                                        <span class="badge cat-purple" title="Nivel <?php echo e($employee->nivel); ?>"><?php echo e($employee->nivel); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            
                            <td data-label="Horario contratado">
                                <?php $__empty_2 = true; $__currentLoopData = $employee->horariosLaborales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $horario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <div class="small text-nowrap">
                                        <span class="fw-semibold"><?php echo e(mb_substr($horario->diaNombre(), 0, 3)); ?></span>
                                        <?php echo e($horario->hora_entrada?->format('H:i')); ?>–<?php echo e($horario->hora_salida?->format('H:i')); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="small text-tertiary-token">Sin horario registrado</span>
                                <?php endif; ?>
                            </td>

                            
                            <td data-label="Hardware">
                                
                                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                    <?php echo $__env->make('employees.partials._fingerprint-badge', [
                                        'fpCount' => $fpCount,
                                        'fpMax' => $employee->fingerprints->unique('finger')->count() ?: null,
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    
                                    <?php if($employee->devices->contains(fn($d) => filled($d->pivot->card_number))): ?>
                                        <span class="small text-tertiary-token" title="Con tarjeta RFID">
                                            <i class="bi bi-credit-card"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <?php $__empty_2 = true; $__currentLoopData = $employee->devices->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <a href="<?php echo e(route('devices.show', $device)); ?>" class="ref-chip"
                                           title="UID <?php echo e($device->pivot->device_uid); ?> · <?php echo e($device->pivot->roleLabel()); ?> · <?php echo e($device->pivot->card_number ? 'Tarjeta '.$device->pivot->card_number : 'Sin tarjeta'); ?>">
                                            <i class="bi bi-hdd-network"></i><?php echo e($device->name); ?>

                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <span class="badge cat-gray">Sin enrolar</span>
                                    <?php endif; ?>
                                    <?php if($devicesCount > 2): ?>
                                        <span class="badge cat-gray" title="<?php echo e($employee->devices->slice(2)->pluck('name')->join(', ')); ?>">
                                            +<?php echo e($devicesCount - 2); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($lastSync): ?>
                                    <?php
                                        $scMap = ['completed' => 'green', 'failed' => 'red', 'running' => 'amber', 'queued' => 'gray'];
                                        $sc = $scMap[$lastSync->status] ?? 'gray';
                                        $syncIcon = match($lastSync->status) {
                                            'completed' => 'bi-check-circle-fill',
                                            'failed' => 'bi-x-circle-fill',
                                            'running', 'queued' => 'bi-hourglass-split',
                                            default => 'bi-dash-circle',
                                        };
                                    ?>
                                    <div class="small mono text-tertiary-token mt-1 text-truncate"
                                         title="<?php echo e($lastSync->stage ?? ''); ?><?php echo e($lastSync->error_message ? ' · '.$lastSync->error_message : ''); ?>">
                                        <i class="<?php echo e($syncIcon); ?> me-1" style="color: var(--cat-<?php echo e($sc); ?>);"></i>
                                        <span style="color: var(--cat-<?php echo e($sc); ?>);"><?php echo e(ucfirst($lastSync->status)); ?></span>
                                        <?php echo e($lastSync->finished_at?->format('d/m H:i') ?? $lastSync->created_at?->format('d/m H:i')); ?>

                                    </div>
                                <?php endif; ?>
                            </td>

                            
                            <td data-label="Estado">
                                <?php if($isBaja): ?>
                                    <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'gray','label' => 'Baja']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','label' => 'Baja']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'green','label' => 'Activo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'green','label' => 'Activo']); ?>
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
                                <div class="small mt-1">
                                    <?php if(!$enrolled): ?>
                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'gray','label' => 'Sin enrolar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','label' => 'Sin enrolar']); ?>
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
                                    <?php elseif($anyActive): ?>
                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'green','label' => 'Enrolado']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'green','label' => 'Enrolado']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'amber','label' => 'Inactivo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'amber','label' => 'Inactivo']); ?>
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
                                </div>
                            </td>

                            
                            <td data-label="">
                                <div class="table-row-actions justify-content-end">
                                    <?php if(auth()->user()->canAccessModule('empleados', 'update')): ?>
                                        <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-sm btn-ghost"
                                           title="Editar empleado" aria-label="Editar <?php echo e($employee->name); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if($enrolled): ?>
                                            <form action="<?php echo e(route('employees.sync-devices', $employee)); ?>" method="POST" class="d-inline" data-sync>
                                                <?php echo csrf_field(); ?>
                                                <?php $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <input type="hidden" name="device_ids[]" value="<?php echo e($d->id); ?>">
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <button class="btn btn-sm btn-ghost"
                                                        title="Re-sincronizar en <?php echo e($devicesCount); ?> checador(es)"
                                                        aria-label="Sincronizar <?php echo e($employee->name); ?>">
                                                    <i class="bi bi-cloud-arrow-up"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" class="d-inline"
                                              data-confirm
                                              data-confirm-danger
                                              data-confirm-title="¿Quitar a <?php echo e($employee->name); ?>?"
                                              data-confirm-message="Se dará de baja en todos sus checadores y, si no queda enrolado en ninguno, también del catálogo. Sus checadas históricas se conservan.">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-icon-danger" title="Dar de baja" aria-label="Dar de baja <?php echo e($employee->name); ?>">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <?php $__currentLoopData = $employee->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('devices.show', $device)); ?>" class="btn btn-sm btn-ghost"
                                               title="Ver <?php echo e($device->name); ?>" aria-label="Ver dispositivo <?php echo e($device->name); ?>">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        
                        <tr id="empty-with-filters" style="<?php echo e(request()->hasAny(['q','cargo','departamento','id_campus','sin_huella','sin_device']) ? '' : 'display:none'); ?>">
                            <td colspan="6">
                                <?php echo $__env->make('partials.empty-state', [
                                    'icon'     => 'bi-search',
                                    'title'    => 'Sin resultados para tu filtro',
                                    'desc'     => 'Prueba con otro nombre, ID, puesto o departamento, o limpia los filtros.',
                                    'cta'      => ['label' => 'Limpiar filtros', 'url' => route('employees.index')],
                                    'ctaLink'  => true,
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        
                        <tr id="empty-no-filters" style="<?php echo e(request()->hasAny(['q','cargo','departamento','id_campus','sin_huella','sin_device']) ? 'display:none' : ''); ?>">
                            <td colspan="6">
                                <?php echo $__env->make('partials.empty-state', [
                                    'icon'     => 'bi-people',
                                    'title'    => 'No hay empleados',
                                    'desc'     => 'Vacía los checadores con «Traer usuarios» o sincroniza Firebird EMPLEADOS para poblar el catálogo.',
                                    'cta'      => auth()->user()->canAccessModule('empleados', 'create')
                                        ? ['label' => 'Agregar empleado', 'url' => route('employees.create')]
                                        : null,
                                    'ctaLink'  => true,
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div id="employees-skeleton" style="display:none">
        <?php for($i = 0; $i < 5; $i++): ?>
            <div class="skeleton skeleton-row mb-1"></div>
        <?php endfor; ?>
    </div>

    
    <div id="employees-error" class="panel-error" style="display:none" role="alert">
        <i class="bi bi-wifi-off"></i>
        <div class="pe-title">No se pudo cargar la información de empleados.</div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i> Reintentar
        </button>
    </div>

    
    <?php if($employees->hasPages()): ?>
        <div class="data-table-footer">
            <div class="data-table-summary">
                <?php
                    $fromItem = $employees->total() > 0 ? $employees->firstItem() : 0;
                    $toItem = $employees->total() > 0 ? $employees->lastItem() : 0;
                ?>
                Mostrando <?php echo e($fromItem); ?>–<?php echo e($toItem); ?> de <?php echo e($employees->total()); ?> registros
            </div>

            <div class="data-table-controls">
                <span class="small text-tertiary-token">Filas</span>
                <div class="data-table-per-page">
                    <?php $__currentLoopData = [25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $query = request()->query();
                            unset($query['page']);
                            $query['per_page'] = $option;
                            $url = request()->url() . '?' . http_build_query($query);
                        ?>
                        <a href="<?php echo e($url); ?>" class="data-table-per-page-link <?php echo e((int) request('per_page', 25) == $option ? 'active' : ''); ?>"><?php echo e($option); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="card-footer border-0 bg-transparent pt-0 pagination-footer">
            <?php echo e($employees->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/employees/index.blade.php ENDPATH**/ ?>