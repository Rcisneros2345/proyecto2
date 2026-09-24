<?php $__env->startSection('title', 'Asistencias'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Asistencias'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Asistencias','subtitle' => 'Seguimiento y revisión de marcajes de empleados y clases.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Asistencias','subtitle' => 'Seguimiento y revisión de marcajes de empleados y clases.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <small class="text-muted d-inline-flex align-items-center gap-2 flex-wrap">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Estados:</strong> <span class="badge cat-blue">Entrada</span> <span class="badge cat-green">Salida</span> <span class="badge cat-orange">Descanso</span> <span class="badge cat-purple">Regreso</span> <span class="badge cat-pink">Extra entrada</span> <span class="badge cat-lavender">Extra salida</span>
        </small>
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

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#employee-attendance">Checadas de empleados</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#class-attendance">Asistencia por clase</button></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="employee-attendance">

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['id' => 'attendanceFilters','action' => route('attendances.index'),'clearUrl' => route('attendances.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'attendanceFilters','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('attendances.index')),'clear-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('attendances.index'))]); ?>
            <div class="col-md-3">
                <label for="device_id" class="form-label small mb-1">Dispositivo</label>
                <select name="device_id" id="device_id" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($device->id); ?>" <?php if(request('device_id') == $device->id): echo 'selected'; endif; ?>><?php echo e($device->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="type" class="form-label small mb-1">Tipo de marcado</label>
                <select name="type" id="type" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('type') !== null && request('type') == $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="from" class="form-label small mb-1">Desde</label>
                <input type="date" id="from" name="from" class="form-control" value="<?php echo e(request('from')); ?>">
            </div>
            <div class="col-md-2">
                <label for="to" class="form-label small mb-1">Hasta</label>
                <input type="date" id="to" name="to" class="form-control" value="<?php echo e(request('to')); ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
            <?php if(auth()->user()->canAccessModule('asistencias', 'export')): ?>
                <div class="col-md-auto ms-auto d-flex gap-2 align-items-end">
                    <a href="<?php echo e(route('attendances.export', request()->query())); ?>" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Excel/CSV
                    </a>
                    <a href="<?php echo e(route('attendances.print', request()->query())); ?>" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-printer"></i> PDF/Imprimir
                    </a>
                </div>
            <?php endif; ?>
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
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Datos personales</th>
                        <th>ID</th>
                        <th>H. base</th>
                        <th>Llegada</th>
                        <th>Salida</th>
                        <th>Tipo de empleado</th>
                        <th>Puesto / área</th>
                        <th>Incidencias</th>
                        <th>Dispositivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="attendance-row <?php echo e($attendance->incidencias->contains(fn ($incidencia) => $incidencia->estado === 'aprobada') ? 'attendance-row--approved' : ''); ?>">
                            <td data-label="Fecha">
                                <span class="mono text-secondary-token" style="font-size:12px"><?php echo e(\Carbon\Carbon::parse($attendance->date)->locale('es')->isoFormat('D MMM YYYY')); ?></span>
                            </td>
                            <td data-label="Empleado">
                                <?php if($attendance->employee): ?>
                                    <span class="avatar is-sm me-2"><?php echo e(strtoupper(substr($attendance->employee->name, 0, 1))); ?></span>
                                    <span class="fw-semibold"><?php echo e($attendance->employee->name); ?></span>
                                <?php else: ?>
                                    <span class="badge cat-orange">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Datos personales">
                                <?php if($attendance->employee): ?>
                                    <div class="small fw-semibold"><i class="bi bi-person-vcard me-1"></i><?php echo e($attendance->employee->sexo_label); ?></div>
                                    <?php if($attendance->employee->fecha_nacimiento): ?>
                                        <div class="small text-secondary-token">Nacimiento: <?php echo e($attendance->employee->fecha_nacimiento->format('d/m/Y')); ?></div>
                                    <?php endif; ?>
                                    <?php if($attendance->employee->nacionalidad || $attendance->employee->estado_civil): ?>
                                        <div class="small text-secondary-token">
                                            <?php echo e($attendance->employee->nacionalidad ?: 'Nacionalidad no indicada'); ?>

                                            <?php if($attendance->employee->estado_civil): ?> · <?php echo e($attendance->employee->estado_civil); ?> <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($attendance->employee->telefono || $attendance->employee->celular || $attendance->employee->email): ?>
                                        <div class="small text-secondary-token text-truncate" title="<?php echo e($attendance->employee->email); ?>">
                                            <?php echo e($attendance->employee->telefono ?: $attendance->employee->celular ?: $attendance->employee->email); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="ID"><code><?php echo e($attendance->user_id); ?></code></td>
                            <td data-label="H. base">
                                <?php if($attendance->tiene_horario): ?>
                                    <span class="text-success" title="Horario definido">
                                        <?php echo e($attendance->horario_entrada_base); ?> - <?php echo e($attendance->horario_salida_base); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted" title="Horario por defecto">
                                        <?php echo e($attendance->horario_entrada_base); ?> - <?php echo e($attendance->horario_salida_base); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Llegada">
                                <?php $__empty_2 = true; $__currentLoopData = $attendance->llegada_resumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $punch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <div class="attendance-punch attendance-punch--in">
                                        <span class="attendance-punch__label"><i class="bi bi-box-arrow-in-right"></i><?php echo e($punch['label']); ?>:</span>
                                        <strong><?php echo e($punch['time']); ?></strong>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="attendance-empty">Sin entrada</span>
                                <?php endif; ?>
                                <div class="attendance-deviation <?php echo e(str_contains($attendance->observacion_llegada, 'tarde') ? 'attendance-deviation--late' : (str_contains($attendance->observacion_llegada, 'temprano') ? 'attendance-deviation--early' : '')); ?>">
                                    <?php echo e($attendance->observacion_llegada); ?>

                                </div>
                            </td>
                            <td data-label="Salida">
                                <?php $__empty_2 = true; $__currentLoopData = $attendance->salida_resumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $punch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <div class="attendance-punch attendance-punch--out">
                                        <span class="attendance-punch__label"><i class="bi bi-box-arrow-right"></i><?php echo e($punch['label']); ?>:</span>
                                        <strong><?php echo e($punch['time']); ?></strong>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="attendance-empty">Sin salida</span>
                                <?php endif; ?>
                                <div class="attendance-deviation <?php echo e(str_contains($attendance->observacion_salida, 'temprano') ? 'attendance-deviation--late' : (str_contains($attendance->observacion_salida, 'tarde') ? 'attendance-deviation--early' : '')); ?>">
                                    <?php echo e($attendance->observacion_salida); ?>

                                </div>
                            </td>
                            <td data-label="Tipo de empleado">
                                <span class="badge bg-light text-dark border"><?php echo e($attendance->employee?->type_label ?? 'Sin clasificar'); ?></span>
                            </td>
                            <td data-label="Puesto / área">
                                <?php if($attendance->employee): ?>
                                    <?php echo e($attendance->employee->puesto?->descripcion ?: ($attendance->employee->cargo ?: 'Sin puesto')); ?>

                                    <small class="d-block text-muted"><?php echo e($attendance->employee->area?->descripcion ?: ($attendance->employee->departamento ?: 'Sin área')); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Incidencias">
                                <?php $__empty_2 = true; $__currentLoopData = $attendance->incidencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incidencia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <?php
                                        $incidenciaEstado = ucfirst($incidencia->estado);
                                    ?>
                                    <div class="mb-1">
                                        <span class="attendance-incident-status attendance-incident-status--<?php echo e($incidencia->estado); ?>"><?php echo e($incidenciaEstado); ?></span>
                                        <span class="attendance-incident-title"><?php echo e($incidencia->asunto); ?></span>
                                    </div>
                                    <div class="small text-muted"><?php echo e($incidencia->tipo_justificacion); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="text-muted">Sin incidencia</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Dispositivo">
                                <?php echo e($attendance->device_names->join(', ') ?: '—'); ?>

                            </td>
                            <td data-label="Observación">
                                <?php $latestObservation = $attendance->latest_observation ?? null; ?>
                                <?php if($latestObservation): ?>
                                    <div class="small text-muted mb-1"><?php echo e(ucfirst($latestObservation->kind)); ?></div>
                                    <div class="small text-secondary-token text-truncate" style="max-width: 180px;" title="<?php echo e($latestObservation->message); ?>"><?php echo e($latestObservation->message); ?></div>
                                <?php else: ?>
                                    <?php $attendanceObservationId = $attendance->observation_attendance_id ?? $attendance->id ?? uniqid('attendance-observation-'); ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceObservationModal-<?php echo e($attendanceObservationId); ?>">
                                        <i class="bi bi-chat-left-text me-1"></i>Observación
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11">
                                <?php echo $__env->make('partials.empty-state', [
                                    'icon'     => request('type') || request('from') || request('to') || request('device_id')
                                        ? 'bi-search'
                                        : 'bi-calendar-x',
                                    'title'    => request('type') || request('from') || request('to') || request('device_id')
                                        ? 'Sin resultados para los filtros'
                                        : 'Aún no hay registros',
                                    'desc'     => request('type') || request('from') || request('to') || request('device_id')
                                        ? 'Prueba con otros criterios o limpia los filtros aplicados.'
                                        : 'Los registros aparecerán aquí cuando se sincronicen desde los checadores.',
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3"><?php echo e($attendances->links()); ?></div>
</div>

<div class="tab-pane fade" id="class-attendance">
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('attendances.index')); ?>" class="row g-2 align-items-end" id="classAttendanceFilters">
                <input type="hidden" name="class_tab" value="1">
                <div class="col-lg-2 col-md-4">
                    <label for="class_cycle" class="form-label small mb-1">Ciclo</label>
                    <select id="class_cycle" name="class_cycle" class="form-select form-select-sm">
                        <option value="">Ciclo con horario más reciente</option>
                        <?php $__currentLoopData = $classCycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cycle->label); ?>" <?php if(request('class_cycle') === $cycle->label): echo 'selected'; endif; ?>><?php echo e($cycle->label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_status" class="form-label small mb-1">Estado</label>
                    <select id="class_status" name="class_status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $classStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if($classStatus === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <label for="class_nivel" class="form-label small mb-1">Nivel</label>
                    <select id="class_nivel" name="class_nivel" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $classLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($level->value); ?>" <?php if(request('class_nivel') === $level->value): echo 'selected'; endif; ?>><?php echo e($level->label ?: $level->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <label for="class_turno" class="form-label small mb-1">Turno</label>
                    <select id="class_turno" name="class_turno" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $classTurns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($shift->value); ?>" <?php if(request('class_turno') === $shift->value): echo 'selected'; endif; ?>><?php echo e($shift->label ?: $shift->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_profesor" class="form-label small mb-1">Docente / profesor</label>
                    <select id="class_profesor" name="class_profesor" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $classProfessors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($professor->clave_profesor); ?>" <?php if(request('class_profesor') === $professor->clave_profesor): echo 'selected'; endif; ?>>
                                <?php echo e(trim(($professor->paterno ?? '').' '.($professor->materno ?? '').' '.($professor->nombre_profesor ?? '')) ?: $professor->clave_profesor); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <label for="class_group" class="form-label small mb-1">Grupo</label>
                    <select id="class_group" name="class_group" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $classGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($group); ?>" <?php if(request('class_group') === $group): echo 'selected'; endif; ?>><?php echo e($group); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_from" class="form-label small mb-1">Desde</label>
                    <input type="date" id="class_from" name="class_from" class="form-control form-control-sm" value="<?php echo e(request('class_from')); ?>">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_to" class="form-label small mb-1">Hasta</label>
                    <input type="date" id="class_to" name="class_to" class="form-control form-control-sm" value="<?php echo e(request('class_to')); ?>">
                </div>
                <div class="col-lg-1 col-md-4 d-flex gap-1">
                    <button class="btn btn-primary btn-sm flex-grow-1" title="Aplicar filtros" aria-label="Aplicar filtros de clases"><i class="bi bi-funnel"></i></button>
                    <a href="<?php echo e(route('attendances.index', ['class_tab' => 1])); ?>" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros" aria-label="Limpiar filtros de clases"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div>
                <span class="fw-semibold">Cuadrícula semanal</span>
                <small class="d-block text-muted">
                    <?php echo e($classCurrentCycle?->label ?? 'Sin ciclo configurado'); ?> · sesiones programadas de lunes a viernes
                </small>
            </div>
            <?php if(auth()->user()->canAccessModule('asistencias', 'export')): ?>
                <a href="<?php echo e(route('attendances.export.classes', request()->query())); ?>" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar docentes
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="class-week-summary" aria-label="Resumen semanal de asistencia">
                <div class="class-week-summary-item">
                    <span class="class-week-summary-label">Clases</span>
                    <strong><?php echo e($classWeeklyStats['classes']); ?></strong>
                </div>
                <div class="class-week-summary-item">
                    <span class="class-week-summary-label">Sesiones programadas</span>
                    <strong><?php echo e($classWeeklyStats['scheduled']); ?></strong>
                </div>
                <div class="class-week-summary-item is-success">
                    <span class="class-week-summary-label">Con asistencia</span>
                    <strong><?php echo e($classWeeklyStats['captured']); ?></strong>
                </div>
                <div class="class-week-summary-item is-warning">
                    <span class="class-week-summary-label">Pendientes</span>
                    <strong><?php echo e($classWeeklyStats['pending']); ?></strong>
                </div>
            </div>
            <?php if(empty($classWeeklyGrid)): ?>
                <?php echo $__env->make('partials.empty-state', [
                    'icon' => 'bi-calendar-week',
                    'title' => 'No hay horario semanal disponible',
                    'desc' => 'Sin ciclo o sesiones académicas sincronizadas para mostrar.',
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-cards class-week-grid">
                        <thead>
                            <tr>
                                <th style="min-width:230px">Clase</th>
                                <?php $__currentLoopData = $classWeekDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th style="min-width:170px"><?php echo e(ucfirst($date->locale('es')->isoFormat('dddd'))); ?><small class="d-block text-muted"><?php echo e($date->format('d/m')); ?></small></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = collect($classWeeklyGrid)->groupBy('codigo_grupo'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionCode => $sectionRows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $sectionFirst = $sectionRows->first();
                                    $sectionPending = $sectionRows->sum('pending');
                                    $sectionScheduled = $sectionRows->sum('scheduled');
                                    $sectionCaptured = $sectionRows->sum('captured');
                                ?>
                                <tr class="class-week-section-row">
                                    <td colspan="6">
                                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                            <div>
                                                <strong><i class="bi bi-diagram-3 me-1"></i>Sección <?php echo e($sectionCode); ?></strong>
                                                <span class="small text-muted ms-2"><?php echo e($sectionFirst['carrera'] ?: 'Carrera no definida'); ?> · <?php echo e($sectionFirst['nivel']); ?> · <?php echo e($sectionFirst['turno']); ?></span>
                                            </div>
                                            <span class="small text-muted"><?php echo e($sectionCaptured); ?>/<?php echo e($sectionScheduled); ?> con asistencia · <?php echo e($sectionPending); ?> pendiente(s)</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php $__currentLoopData = $sectionRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td data-label="Clase">
                                            <strong class="d-block"><?php echo e($classRow['materia']); ?></strong>
                                            <span class="small text-muted d-block"><?php echo e($classRow['profesor']); ?></span>
                                            <span class="badge <?php echo e($classRow['pending'] > 0 ? 'cat-orange' : 'cat-green'); ?> mt-1">
                                                <?php echo e($classRow['pending'] > 0 ? $classRow['pending'].' pendiente(s)' : 'Completa'); ?>

                                            </span>
                                        </td>
                                        <?php $__currentLoopData = $classWeekDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td data-label="<?php echo e(ucfirst($date->locale('es')->isoFormat('dddd'))); ?>">
                                                <?php $__empty_1 = true; $__currentLoopData = $classRow['days'][$day] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php
                                                    $hasAttendance = filled($slot['status']);
                                                    $status = $slot['status'] ?? 'SIN ASISTENCIA';
                                                    $statusLabel = match($status) {
                                                        'PRESENTE' => 'Presente',
                                                        'AUSENTE' => 'Ausente',
                                                        'RETARDO' => 'Retardo',
                                                        'JUSTIFICADO' => 'Justificado',
                                                        default => 'Sin asistencia',
                                                    };
                                                    $statusClass = match($status) {
                                                        'PRESENTE' => 'cat-green',
                                                        'AUSENTE' => 'cat-red',
                                                        'RETARDO' => 'cat-amber',
                                                        'JUSTIFICADO' => 'cat-blue',
                                                        default => 'cat-orange',
                                                    };
                                                ?>
                                                    <div class="class-week-slot mb-2">
                                                        <div class="small fw-semibold"><?php echo e($slot['session']); ?></div>
                                                        <div class="small text-muted"><?php echo e($slot['time']); ?></div>
                                                        <span class="badge <?php echo e($statusClass); ?> mt-1"><?php echo e($statusLabel); ?></span>
                                                        <?php if(!$hasAttendance): ?>
                                                            <div class="small text-warning-emphasis">Esta clase no tiene asistencia</div>
                                                        <?php endif; ?>
                                                        <?php if($slot['observaciones']): ?>
                                                            <div class="small text-muted text-truncate" title="<?php echo e($slot['observaciones']); ?>"><?php echo e($slot['observaciones']); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <span class="small text-muted">Sin clase</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2 flex-wrap px-3 py-2 border-top small text-muted">
                    <span><span class="badge cat-green">Presente</span> Capturada</span>
                    <span><span class="badge cat-red">Ausente</span> Ausencia</span>
                    <span><span class="badge cat-orange">Sin asistencia</span> Pendiente</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Registros capturados</span>
            <small class="text-muted">
                <?php echo e(request('class_status') === 'SIN_ASIGNAR' ? 'Los pendientes se muestran en la cuadrícula semanal' : 'Detalle de asistencias registradas'); ?>

            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-cards">
                <thead><tr><th>Fecha</th><th>Registro</th><th>Docente</th><th>Grupo / carrera</th><th>Materia</th><th>Día</th><th>Sesión</th><th>Horario</th><th>Ubicación</th><th>Estado</th><th>Sección</th>
<th>Observaciones</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $classAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classAttendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($classAttendance->fecha)->locale('es')->isoFormat('D MMM YYYY')); ?></td>
                            <td><?php echo e($classAttendance->created_at ? \Carbon\Carbon::parse($classAttendance->created_at)->locale('es')->isoFormat('D MMM YYYY HH:mm:ss') : '—'); ?></td>
                            <td><strong><?php echo e(trim(($classAttendance->profesor_paterno ?? '').' '.($classAttendance->profesor_materno ?? '').' '.($classAttendance->nombre_profesor ?? '')) ?: $classAttendance->clave_profesor); ?></strong><small class="d-block text-muted"><?php echo e($classAttendance->clave_profesor); ?></small></td>
                            <td><strong><?php echo e($classAttendance->codigo_grupo); ?></strong><small class="d-block text-muted"><?php echo e($classAttendance->carrera ?? 'Carrera no definida'); ?></small><small class="d-block text-muted"><?php echo e($classAttendance->nivel ?? 'Nivel no definido'); ?> · <?php echo e($classAttendance->turno ?? 'Turno no definido'); ?></small></td>
                            <td><?php echo e($classAttendance->nombre_asignatura ?? $classAttendance->clave_asignatura); ?></td>
                            <td><?php echo e([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'][$classAttendance->dia] ?? 'Día '.$classAttendance->dia); ?></td>
                            <td><?php echo e($classAttendance->sesion); ?></td>
                            <td><?php echo e($classAttendance->hora_inicio ? \Carbon\Carbon::parse($classAttendance->hora_inicio)->format('H:i') : '—'); ?> - <?php echo e($classAttendance->hora_fin ? \Carbon\Carbon::parse($classAttendance->hora_fin)->format('H:i') : '—'); ?></td>
                            <td><?php echo e($classAttendance->sede_nombre ?? $classAttendance->id_campus ?? 'Sede no definida'); ?><small class="d-block text-muted">Edificio <?php echo e($classAttendance->edificio ?? '—'); ?> · Aula <?php echo e($classAttendance->aula ?? '—'); ?></small><small class="d-block text-muted">Ciclo <?php echo e($classAttendance->inicial); ?>-<?php echo e($classAttendance->final); ?>-<?php echo e($classAttendance->periodo); ?></small></td>
                            <td><span class="badge bg-<?php echo e($classAttendance->estado === 'PRESENTE' ? 'success' : ($classAttendance->estado === 'AUSENTE' ? 'danger' : 'warning')); ?>"><?php echo e($classAttendance->estado); ?></span></td>
                            <td>
                                <?php if($classAttendance->has_horario): ?>
                                    <span class="badge bg-success">Con sección</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Sin sección</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($classAttendance->observaciones ?: '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="11"><?php echo $__env->make('partials.empty-state', ['icon' => 'bi-calendar-x', 'title' => 'Sin asistencia por clase', 'desc' => 'Las capturas docentes aparecerán aquí cuando se registren.'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3"><?php echo e($classAttendances->links()); ?></div>
</div>
</div>

<?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $attendanceObservationId = $attendance->observation_attendance_id ?? $attendance->id ?? uniqid('attendance-observation-'); ?>
    <div class="modal fade" id="attendanceObservationModal-<?php echo e($attendanceObservationId); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('attendances.observations.store', ['attendance' => $attendance->observation_attendance_id ?? $attendance->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Observación de asistencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="attendance-observation-kind-<?php echo e($attendance->id); ?>" class="form-label">Tipo</label>
                            <select id="attendance-observation-kind-<?php echo e($attendance->id); ?>" name="kind" class="form-select" required>
                                <option value="late">Retraso</option>
                                <option value="early">Llegada temprana</option>
                                <option value="note">Nota</option>
                                <option value="manual">Observación manual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="attendance-observation-message-<?php echo e($attendance->id); ?>" class="form-label">Detalle</label>
                            <textarea id="attendance-observation-message-<?php echo e($attendance->id); ?>" name="message" rows="4" class="form-control" maxlength="1000" placeholder="Describe la incidencia de la asistencia" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar observación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
const attendanceFilters = document.getElementById('attendanceFilters');
const classAttendancePanel = document.getElementById('class-attendance');

attendanceFilters?.addEventListener('change', async (event) => {
    event.preventDefault();
    const form = event.target.form;
    if (!form) return;

    const url = new URL(form.action, window.location.origin);
    url.search = new URLSearchParams(new FormData(form)).toString();
    const response = await fetch(url, {
        headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    if (!response.ok) return;

    const html = await response.text();
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const newTable = doc.querySelector('#employee-attendance table tbody');
    const currentTable = document.querySelector('#employee-attendance table tbody');
    if (newTable && currentTable) currentTable.replaceWith(newTable);
});

function bindClassAttendanceFilters() {
    const form = document.getElementById('classAttendanceFilters');
    if (!form || form.dataset.ajaxBound === '1') return;
    form.dataset.ajaxBound = '1';

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        if (button) button.disabled = true;

        try {
            const url = new URL(form.action, window.location.origin);
            url.search = new URLSearchParams(new FormData(form)).toString();
            const response = await fetch(url, {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('No se pudo filtrar la asistencia por clase.');

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newPanel = doc.querySelector('#class-attendance');
            if (!newPanel || !classAttendancePanel) return;

            classAttendancePanel.innerHTML = newPanel.innerHTML;
            window.history.replaceState({}, '', url);
            bindClassAttendanceFilters();
        } catch (error) {
            console.error(error);
        } finally {
            if (button) button.disabled = false;
        }
    });
}

bindClassAttendanceFilters();
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\attendances\index.blade.php ENDPATH**/ ?>