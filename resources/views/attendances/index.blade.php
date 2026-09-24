@extends('layouts.admin')

@section('title', 'Asistencias')
@section('breadcrumb', 'Operación › Asistencias')

@section('content')
<x-page-header title="Asistencias" subtitle="Seguimiento y revisión de marcajes de empleados y clases." :hide-title="false">
    @slot('actions')
        <small class="text-muted d-inline-flex align-items-center gap-2 flex-wrap">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Estados:</strong> <span class="badge cat-blue">Entrada</span> <span class="badge cat-green">Salida</span> <span class="badge cat-orange">Descanso</span> <span class="badge cat-purple">Regreso</span> <span class="badge cat-pink">Extra entrada</span> <span class="badge cat-lavender">Extra salida</span>
        </small>
    @endslot
</x-page-header>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#employee-attendance">Checadas de empleados</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#class-attendance">Asistencia por clase</button></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="employee-attendance">

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <x-filter-bar id="attendanceFilters" :action="route('attendances.index')" :clear-url="route('attendances.index')">
            <div class="col-md-3">
                <label for="device_id" class="form-label small mb-1">Dispositivo</label>
                <select name="device_id" id="device_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($devices as $device)
                        <option value="{{ $device->id }}" @selected(request('device_id') == $device->id)>{{ $device->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="type" class="form-label small mb-1">Tipo de marcado</label>
                <select name="type" id="type" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($states as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') !== null && request('type') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="from" class="form-label small mb-1">Desde</label>
                <input type="date" id="from" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label for="to" class="form-label small mb-1">Hasta</label>
                <input type="date" id="to" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
            @if (auth()->user()->canAccessModule('asistencias', 'export'))
                <div class="col-md-auto ms-auto d-flex gap-2 align-items-end">
                    <a href="{{ route('attendances.export', request()->query()) }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Excel/CSV
                    </a>
                    <a href="{{ route('attendances.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-printer"></i> PDF/Imprimir
                    </a>
                </div>
            @endif
        </x-filter-bar>
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
                    @forelse ($attendances as $attendance)
                        <tr class="attendance-row {{ $attendance->incidencias->contains(fn ($incidencia) => $incidencia->estado === 'aprobada') ? 'attendance-row--approved' : '' }}">
                            <td data-label="Fecha">
                                <span class="mono text-secondary-token" style="font-size:12px">{{ \Carbon\Carbon::parse($attendance->date)->locale('es')->isoFormat('D MMM YYYY') }}</span>
                            </td>
                            <td data-label="Empleado">
                                @if ($attendance->employee)
                                    <span class="avatar is-sm me-2">{{ strtoupper(substr($attendance->employee->name, 0, 1)) }}</span>
                                    <span class="fw-semibold">{{ $attendance->employee->name }}</span>
                                @else
                                    <span class="badge cat-orange">Sin asignar</span>
                                @endif
                            </td>
                            <td data-label="Datos personales">
                                @if ($attendance->employee)
                                    <div class="small fw-semibold"><i class="bi bi-person-vcard me-1"></i>{{ $attendance->employee->sexo_label }}</div>
                                    @if($attendance->employee->fecha_nacimiento)
                                        <div class="small text-secondary-token">Nacimiento: {{ $attendance->employee->fecha_nacimiento->format('d/m/Y') }}</div>
                                    @endif
                                    @if($attendance->employee->nacionalidad || $attendance->employee->estado_civil)
                                        <div class="small text-secondary-token">
                                            {{ $attendance->employee->nacionalidad ?: 'Nacionalidad no indicada' }}
                                            @if($attendance->employee->estado_civil) · {{ $attendance->employee->estado_civil }} @endif
                                        </div>
                                    @endif
                                    @if($attendance->employee->telefono || $attendance->employee->celular || $attendance->employee->email)
                                        <div class="small text-secondary-token text-truncate" title="{{ $attendance->employee->email }}">
                                            {{ $attendance->employee->telefono ?: $attendance->employee->celular ?: $attendance->employee->email }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td data-label="ID"><code>{{ $attendance->user_id }}</code></td>
                            <td data-label="H. base">
                                @if ($attendance->tiene_horario)
                                    <span class="text-success" title="Horario definido">
                                        {{ $attendance->horario_entrada_base }} - {{ $attendance->horario_salida_base }}
                                    </span>
                                @else
                                    <span class="text-muted" title="Horario por defecto">
                                        {{ $attendance->horario_entrada_base }} - {{ $attendance->horario_salida_base }}
                                    </span>
                                @endif
                            </td>
                            <td data-label="Llegada">
                                @forelse($attendance->llegada_resumen as $punch)
                                    <div class="attendance-punch attendance-punch--in">
                                        <span class="attendance-punch__label"><i class="bi bi-box-arrow-in-right"></i>{{ $punch['label'] }}:</span>
                                        <strong>{{ $punch['time'] }}</strong>
                                    </div>
                                @empty
                                    <span class="attendance-empty">Sin entrada</span>
                                @endforelse
                                <div class="attendance-deviation {{ str_contains($attendance->observacion_llegada, 'tarde') ? 'attendance-deviation--late' : (str_contains($attendance->observacion_llegada, 'temprano') ? 'attendance-deviation--early' : '') }}">
                                    {{ $attendance->observacion_llegada }}
                                </div>
                            </td>
                            <td data-label="Salida">
                                @forelse($attendance->salida_resumen as $punch)
                                    <div class="attendance-punch attendance-punch--out">
                                        <span class="attendance-punch__label"><i class="bi bi-box-arrow-right"></i>{{ $punch['label'] }}:</span>
                                        <strong>{{ $punch['time'] }}</strong>
                                    </div>
                                @empty
                                    <span class="attendance-empty">Sin salida</span>
                                @endforelse
                                <div class="attendance-deviation {{ str_contains($attendance->observacion_salida, 'temprano') ? 'attendance-deviation--late' : (str_contains($attendance->observacion_salida, 'tarde') ? 'attendance-deviation--early' : '') }}">
                                    {{ $attendance->observacion_salida }}
                                </div>
                            </td>
                            <td data-label="Tipo de empleado">
                                <span class="badge bg-light text-dark border">{{ $attendance->employee?->type_label ?? 'Sin clasificar' }}</span>
                            </td>
                            <td data-label="Puesto / área">
                                @if ($attendance->employee)
                                    {{ $attendance->employee->puesto?->descripcion ?: ($attendance->employee->cargo ?: 'Sin puesto') }}
                                    <small class="d-block text-muted">{{ $attendance->employee->area?->descripcion ?: ($attendance->employee->departamento ?: 'Sin área') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td data-label="Incidencias">
                                @forelse ($attendance->incidencias as $incidencia)
                                    @php
                                        $incidenciaEstado = ucfirst($incidencia->estado);
                                    @endphp
                                    <div class="mb-1">
                                        <span class="attendance-incident-status attendance-incident-status--{{ $incidencia->estado }}">{{ $incidenciaEstado }}</span>
                                        <span class="attendance-incident-title">{{ $incidencia->asunto }}</span>
                                    </div>
                                    <div class="small text-muted">{{ $incidencia->tipo_justificacion }}</div>
                                @empty
                                    <span class="text-muted">Sin incidencia</span>
                                @endforelse
                            </td>
                            <td data-label="Dispositivo">
                                {{ $attendance->device_names->join(', ') ?: '—' }}
                            </td>
                            <td data-label="Observación">
                                @php $latestObservation = $attendance->latest_observation ?? null; @endphp
                                @if ($latestObservation)
                                    <div class="small text-muted mb-1">{{ ucfirst($latestObservation->kind) }}</div>
                                    <div class="small text-secondary-token text-truncate" style="max-width: 180px;" title="{{ $latestObservation->message }}">{{ $latestObservation->message }}</div>
                                @else
                                    @php $attendanceObservationId = $attendance->observation_attendance_id ?? $attendance->id ?? uniqid('attendance-observation-'); @endphp
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceObservationModal-{{ $attendanceObservationId }}">
                                        <i class="bi bi-chat-left-text me-1"></i>Observación
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                @include('partials.empty-state', [
                                    'icon'     => request('type') || request('from') || request('to') || request('device_id')
                                        ? 'bi-search'
                                        : 'bi-calendar-x',
                                    'title'    => request('type') || request('from') || request('to') || request('device_id')
                                        ? 'Sin resultados para los filtros'
                                        : 'Aún no hay registros',
                                    'desc'     => request('type') || request('from') || request('to') || request('device_id')
                                        ? 'Prueba con otros criterios o limpia los filtros aplicados.'
                                        : 'Los registros aparecerán aquí cuando se sincronicen desde los checadores.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $attendances->links() }}</div>
</div>

<div class="tab-pane fade" id="class-attendance">
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('attendances.index') }}" class="row g-2 align-items-end" id="classAttendanceFilters">
                <input type="hidden" name="class_tab" value="1">
                <div class="col-lg-2 col-md-4">
                    <label for="class_cycle" class="form-label small mb-1">Ciclo</label>
                    <select id="class_cycle" name="class_cycle" class="form-select form-select-sm">
                        <option value="">Ciclo con horario más reciente</option>
                        @foreach($classCycles as $cycle)
                            <option value="{{ $cycle->label }}" @selected(request('class_cycle') === $cycle->label)>{{ $cycle->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_status" class="form-label small mb-1">Estado</label>
                    <select id="class_status" name="class_status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($classStatuses as $value => $label)
                            <option value="{{ $value }}" @selected($classStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <label for="class_nivel" class="form-label small mb-1">Nivel</label>
                    <select id="class_nivel" name="class_nivel" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($classLevels as $level)
                            <option value="{{ $level->value }}" @selected(request('class_nivel') === $level->value)>{{ $level->label ?: $level->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <label for="class_turno" class="form-label small mb-1">Turno</label>
                    <select id="class_turno" name="class_turno" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($classTurns as $shift)
                            <option value="{{ $shift->value }}" @selected(request('class_turno') === $shift->value)>{{ $shift->label ?: $shift->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_profesor" class="form-label small mb-1">Docente / profesor</label>
                    <select id="class_profesor" name="class_profesor" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($classProfessors as $professor)
                            <option value="{{ $professor->clave_profesor }}" @selected(request('class_profesor') === $professor->clave_profesor)>
                                {{ trim(($professor->paterno ?? '').' '.($professor->materno ?? '').' '.($professor->nombre_profesor ?? '')) ?: $professor->clave_profesor }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-3">
                    <label for="class_group" class="form-label small mb-1">Grupo</label>
                    <select id="class_group" name="class_group" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($classGroups as $group)
                            <option value="{{ $group }}" @selected(request('class_group') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_from" class="form-label small mb-1">Desde</label>
                    <input type="date" id="class_from" name="class_from" class="form-control form-control-sm" value="{{ request('class_from') }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="class_to" class="form-label small mb-1">Hasta</label>
                    <input type="date" id="class_to" name="class_to" class="form-control form-control-sm" value="{{ request('class_to') }}">
                </div>
                <div class="col-lg-1 col-md-4 d-flex gap-1">
                    <button class="btn btn-primary btn-sm flex-grow-1" title="Aplicar filtros" aria-label="Aplicar filtros de clases"><i class="bi bi-funnel"></i></button>
                    <a href="{{ route('attendances.index', ['class_tab' => 1]) }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros" aria-label="Limpiar filtros de clases"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div>
                <span class="fw-semibold">Cuadrícula semanal</span>
                <small class="d-block text-muted">
                    {{ $classCurrentCycle?->label ?? 'Sin ciclo configurado' }} · sesiones programadas de lunes a viernes
                </small>
            </div>
            @if (auth()->user()->canAccessModule('asistencias', 'export'))
                <a href="{{ route('attendances.export.classes', request()->query()) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar docentes
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="class-week-summary" aria-label="Resumen semanal de asistencia">
                <div class="class-week-summary-item">
                    <span class="class-week-summary-label">Clases</span>
                    <strong>{{ $classWeeklyStats['classes'] }}</strong>
                </div>
                <div class="class-week-summary-item">
                    <span class="class-week-summary-label">Sesiones programadas</span>
                    <strong>{{ $classWeeklyStats['scheduled'] }}</strong>
                </div>
                <div class="class-week-summary-item is-success">
                    <span class="class-week-summary-label">Con asistencia</span>
                    <strong>{{ $classWeeklyStats['captured'] }}</strong>
                </div>
                <div class="class-week-summary-item is-warning">
                    <span class="class-week-summary-label">Pendientes</span>
                    <strong>{{ $classWeeklyStats['pending'] }}</strong>
                </div>
            </div>
            @if(empty($classWeeklyGrid))
                @include('partials.empty-state', [
                    'icon' => 'bi-calendar-week',
                    'title' => 'No hay horario semanal disponible',
                    'desc' => 'Sin ciclo o sesiones académicas sincronizadas para mostrar.',
                ])
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-cards class-week-grid">
                        <thead>
                            <tr>
                                <th style="min-width:230px">Clase</th>
                                @foreach($classWeekDates as $day => $date)
                                    <th style="min-width:170px">{{ ucfirst($date->locale('es')->isoFormat('dddd')) }}<small class="d-block text-muted">{{ $date->format('d/m') }}</small></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($classWeeklyGrid)->groupBy('codigo_grupo') as $sectionCode => $sectionRows)
                                @php
                                    $sectionFirst = $sectionRows->first();
                                    $sectionPending = $sectionRows->sum('pending');
                                    $sectionScheduled = $sectionRows->sum('scheduled');
                                    $sectionCaptured = $sectionRows->sum('captured');
                                @endphp
                                <tr class="class-week-section-row">
                                    <td colspan="6">
                                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                            <div>
                                                <strong><i class="bi bi-diagram-3 me-1"></i>Sección {{ $sectionCode }}</strong>
                                                <span class="small text-muted ms-2">{{ $sectionFirst['carrera'] ?: 'Carrera no definida' }} · {{ $sectionFirst['nivel'] }} · {{ $sectionFirst['turno'] }}</span>
                                            </div>
                                            <span class="small text-muted">{{ $sectionCaptured }}/{{ $sectionScheduled }} con asistencia · {{ $sectionPending }} pendiente(s)</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($sectionRows as $classRow)
                                    <tr>
                                        <td data-label="Clase">
                                            <strong class="d-block">{{ $classRow['materia'] }}</strong>
                                            <span class="small text-muted d-block">{{ $classRow['profesor'] }}</span>
                                            <span class="badge {{ $classRow['pending'] > 0 ? 'cat-orange' : 'cat-green' }} mt-1">
                                                {{ $classRow['pending'] > 0 ? $classRow['pending'].' pendiente(s)' : 'Completa' }}
                                            </span>
                                        </td>
                                        @foreach($classWeekDates as $day => $date)
                                            <td data-label="{{ ucfirst($date->locale('es')->isoFormat('dddd')) }}">
                                                @forelse($classRow['days'][$day] ?? [] as $slot)
                                                @php
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
                                                @endphp
                                                    <div class="class-week-slot mb-2">
                                                        <div class="small fw-semibold">{{ $slot['session'] }}</div>
                                                        <div class="small text-muted">{{ $slot['time'] }}</div>
                                                        <span class="badge {{ $statusClass }} mt-1">{{ $statusLabel }}</span>
                                                        @if(!$hasAttendance)
                                                            <div class="small text-warning-emphasis">Esta clase no tiene asistencia</div>
                                                        @endif
                                                        @if($slot['observaciones'])
                                                            <div class="small text-muted text-truncate" title="{{ $slot['observaciones'] }}">{{ $slot['observaciones'] }}</div>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <span class="small text-muted">Sin clase</span>
                                                @endforelse
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2 flex-wrap px-3 py-2 border-top small text-muted">
                    <span><span class="badge cat-green">Presente</span> Capturada</span>
                    <span><span class="badge cat-red">Ausente</span> Ausencia</span>
                    <span><span class="badge cat-orange">Sin asistencia</span> Pendiente</span>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Registros capturados</span>
            <small class="text-muted">
                {{ request('class_status') === 'SIN_ASIGNAR' ? 'Los pendientes se muestran en la cuadrícula semanal' : 'Detalle de asistencias registradas' }}
            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-cards">
                <thead><tr><th>Fecha</th><th>Registro</th><th>Docente</th><th>Grupo / carrera</th><th>Materia</th><th>Día</th><th>Sesión</th><th>Horario</th><th>Ubicación</th><th>Estado</th><th>Sección</th>
<th>Observaciones</th></tr></thead>
                <tbody>
                    @forelse ($classAttendances as $classAttendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($classAttendance->fecha)->locale('es')->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $classAttendance->created_at ? \Carbon\Carbon::parse($classAttendance->created_at)->locale('es')->isoFormat('D MMM YYYY HH:mm:ss') : '—' }}</td>
                            <td><strong>{{ trim(($classAttendance->profesor_paterno ?? '').' '.($classAttendance->profesor_materno ?? '').' '.($classAttendance->nombre_profesor ?? '')) ?: $classAttendance->clave_profesor }}</strong><small class="d-block text-muted">{{ $classAttendance->clave_profesor }}</small></td>
                            <td><strong>{{ $classAttendance->codigo_grupo }}</strong><small class="d-block text-muted">{{ $classAttendance->carrera ?? 'Carrera no definida' }}</small><small class="d-block text-muted">{{ $classAttendance->nivel ?? 'Nivel no definido' }} · {{ $classAttendance->turno ?? 'Turno no definido' }}</small></td>
                            <td>{{ $classAttendance->nombre_asignatura ?? $classAttendance->clave_asignatura }}</td>
                            <td>{{ [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'][$classAttendance->dia] ?? 'Día '.$classAttendance->dia }}</td>
                            <td>{{ $classAttendance->sesion }}</td>
                            <td>{{ $classAttendance->hora_inicio ? \Carbon\Carbon::parse($classAttendance->hora_inicio)->format('H:i') : '—' }} - {{ $classAttendance->hora_fin ? \Carbon\Carbon::parse($classAttendance->hora_fin)->format('H:i') : '—' }}</td>
                            <td>{{ $classAttendance->sede_nombre ?? $classAttendance->id_campus ?? 'Sede no definida' }}<small class="d-block text-muted">Edificio {{ $classAttendance->edificio ?? '—' }} · Aula {{ $classAttendance->aula ?? '—' }}</small><small class="d-block text-muted">Ciclo {{ $classAttendance->inicial }}-{{ $classAttendance->final }}-{{ $classAttendance->periodo }}</small></td>
                            <td><span class="badge bg-{{ $classAttendance->estado === 'PRESENTE' ? 'success' : ($classAttendance->estado === 'AUSENTE' ? 'danger' : 'warning') }}">{{ $classAttendance->estado }}</span></td>
                            <td>
                                @if ($classAttendance->has_horario)
                                    <span class="badge bg-success">Con sección</span>
                                @else
                                    <span class="badge bg-warning text-dark">Sin sección</span>
                                @endif
                            </td>
                            <td>{{ $classAttendance->observaciones ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="11">@include('partials.empty-state', ['icon' => 'bi-calendar-x', 'title' => 'Sin asistencia por clase', 'desc' => 'Las capturas docentes aparecerán aquí cuando se registren.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $classAttendances->links() }}</div>
</div>
</div>

@foreach ($attendances as $attendance)
    @php $attendanceObservationId = $attendance->observation_attendance_id ?? $attendance->id ?? uniqid('attendance-observation-'); @endphp
    <div class="modal fade" id="attendanceObservationModal-{{ $attendanceObservationId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('attendances.observations.store', ['attendance' => $attendance->observation_attendance_id ?? $attendance->id]) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Observación de asistencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="attendance-observation-kind-{{ $attendance->id }}" class="form-label">Tipo</label>
                            <select id="attendance-observation-kind-{{ $attendance->id }}" name="kind" class="form-select" required>
                                <option value="late">Retraso</option>
                                <option value="early">Llegada temprana</option>
                                <option value="note">Nota</option>
                                <option value="manual">Observación manual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="attendance-observation-message-{{ $attendance->id }}" class="form-label">Detalle</label>
                            <textarea id="attendance-observation-message-{{ $attendance->id }}" name="message" rows="4" class="form-control" maxlength="1000" placeholder="Describe la incidencia de la asistencia" required></textarea>
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
@endforeach

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

@endsection