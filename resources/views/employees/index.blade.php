@extends('layouts.admin')

@section('title', 'Empleados')
@section('breadcrumb', 'Operación › Empleados')

@section('content')
<x-page-header title="Empleados" subtitle="Consulta, administración y estado de los empleados en la red biométrica." :hide-title="false">
    @slot('actions')
        @if(auth()->user()->canAccessModule('dispositivos', 'view'))
            <a href="{{ route('employees.sobrantes') }}" class="btn btn-outline-secondary btn-sm" title="Ver sobrantes en dispositivos" aria-label="Ver sobrantes">
                <i class="bi bi-exclamation-triangle me-1"></i> Sobrantes
            </a>
        @endif
    @endslot
</x-page-header>

{{-- ═══ FILTROS ═══ --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-3 align-items-end" id="employees-filter-form">
            <div class="col-lg-4 col-12">
                <label class="form-label small mb-1" for="employeeSearch">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-tertiary-token"></i></span>
                    <input type="search" name="q" id="employeeSearch" value="{{ request('q') }}"
                           class="form-control" placeholder="Buscar por nombre, ID o puesto"
                           aria-label="Buscar empleados" autocomplete="off">
                </div>
            </div>

            @if(!empty($cargos) && count($cargos))
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small mb-1" for="filterCargo">Puesto</label>
                    <select name="cargo" id="filterCargo" class="form-select" aria-label="Filtrar por puesto">
                        <option value="">Todos</option>
                        @foreach($cargos as $c)
                            <option @selected(request('cargo') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(!empty($departamentos) && count($departamentos))
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small mb-1" for="filterDepto">Departamento</label>
                    <select name="departamento" id="filterDepto" class="form-select" aria-label="Filtrar por departamento">
                        <option value="">Todos</option>
                        @foreach($departamentos as $d)
                            <option @selected(request('departamento') === $d)>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(!empty($sedes) && count($sedes))
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small mb-1" for="filterSede">Sede</label>
                    <select name="id_campus" id="filterSede" class="form-select" aria-label="Filtrar por sede">
                        <option value="">Todas</option>
                        @foreach($sedes as $s)
                            <option value="{{ $s->id_campus }}" @selected(request('id_campus') == $s->id_campus)>{{ $s->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-lg-2 col-12 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['q', 'cargo', 'departamento', 'id_campus', 'sin_huella', 'sin_device']))
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                @endif
            </div>

            {{-- Filtros activos --}}
            @if(request()->hasAny(['q', 'cargo', 'departamento', 'id_campus', 'sin_huella', 'sin_device']))
                <div class="col-12 d-flex gap-2 flex-wrap align-items-center pt-2 border-top">
                    <span class="small text-tertiary-token me-1">Activos:</span>
                    @if(request('q'))
                        <span class="badge cat-blue d-inline-flex align-items-center gap-1">
                            <i class="bi bi-search"></i> {{ request('q') }}
                            <a href="{{ route('employees.index', request()->except('q')) }}" class="ms-1 text-decoration-none" aria-label="Quitar filtro búsqueda">&times;</a>
                        </span>
                    @endif
                    @if(request('cargo'))
                        <span class="badge cat-purple d-inline-flex align-items-center gap-1">
                            <i class="bi bi-briefcase"></i> {{ request('cargo') }}
                            <a href="{{ route('employees.index', request()->except('cargo')) }}" class="ms-1 text-decoration-none" aria-label="Quitar filtro puesto">&times;</a>
                        </span>
                    @endif
                    @if(request('departamento'))
                        <span class="badge cat-blue d-inline-flex align-items-center gap-1">
                            <i class="bi bi-building"></i> {{ request('departamento') }}
                            <a href="{{ route('employees.index', request()->except('departamento')) }}" class="ms-1 text-decoration-none" aria-label="Quitar filtro departamento">&times;</a>
                        </span>
                    @endif
                    @if(request('id_campus'))
                        <span class="badge cat-green d-inline-flex align-items-center gap-1">
                            <i class="bi bi-geo-alt"></i> Sede {{ request('id_campus') }}
                            <a href="{{ route('employees.index', request()->except('id_campus')) }}" class="ms-1 text-decoration-none" aria-label="Quitar filtro sede">&times;</a>
                        </span>
                    @endif
                    @if(request('sin_huella'))
                        <span class="badge cat-amber d-inline-flex align-items-center gap-1">
                            <i class="bi bi-fingerprint"></i> Sin huellas
                            <a href="{{ route('employees.index', request()->except('sin_huella')) }}" class="ms-1 text-decoration-none" aria-label="Quitar filtro sin huellas">&times;</a>
                        </span>
                    @endif
                    @if(request('sin_device'))
                        <span class="badge cat-amber d-inline-flex align-items-center gap-1">
                            <i class="bi bi-hdd-network"></i> Sin enrolar
                            <a href="{{ route('employees.index', request()->except('sin_device')) }}" class="ms-1 text-decoration-none" aria-label="Quitar filtro sin enrolar">&times;</a>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Chips rápidos --}}
            @include('employees.partials._quick-filters', [
                'baseUrl' => route('employees.index'),
                'activeParams' => request()->query(),
            ])
        </form>
    </div>
</div>

{{-- ═══ TABS POR ESTADO ═══ --}}
<ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status === 'todos' ? 'active' : '' }}"
           href="{{ route('employees.index', array_merge(request()->query(), ['status' => 'todos'])) }}"
           role="tab" aria-selected="{{ $status === 'todos' ? 'true' : 'false' }}">
            Todos
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status === 'activos' ? 'active' : '' }}"
           href="{{ route('employees.index', array_merge(request()->query(), ['status' => 'activos'])) }}"
           role="tab" aria-selected="{{ $status === 'activos' ? 'true' : 'false' }}">
            Activos
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status === 'bajas' ? 'active' : '' }}"
           href="{{ route('employees.index', array_merge(request()->query(), ['status' => 'bajas'])) }}"
           role="tab" aria-selected="{{ $status === 'bajas' ? 'true' : 'false' }}">
            Bajas
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" href="{{ route('employees.sobrantes') }}" role="tab" aria-selected="false">
            <i class="bi bi-exclamation-triangle"></i>
            Sobrantes
            @if(($sobrantesStats['total'] ?? 0) > 0)
                <span class="badge cat-amber ms-1">{{ $sobrantesStats['total'] }}</span>
            @endif
        </a>
    </li>
</ul>

{{-- ═══ TOOLBAR: contador + CTA ═══ --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div id="employees-counter" class="small text-tertiary-token" aria-live="polite">
        <i class="bi bi-people me-1"></i> {{ $employees->total() }} empleados
        @if(request()->hasAny(['q', 'cargo', 'departamento', 'id_campus']))
            · filtrado por
            <span class="text-secondary-token">
                {{ request('q') ?: request('cargo') ?: request('departamento') ?: ('sede ' . request('id_campus')) }}
            </span>
        @endif
    </div>
    @if(auth()->user()->canAccessModule('empleados', 'create'))
        <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm" title="Agregar nuevo empleado" aria-label="Agregar empleado">
            <i class="bi bi-plus-lg me-1"></i> Agregar empleado
        </a>
    @endif
</div>

{{-- ═══ TABLA ═══ --}}
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
                <tbody data-is-admin="{{ auth()->user()->isAdmin() ? '1' : '0' }}">
                    @forelse ($employees as $employee)
                        @php
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
                        @endphp
                        <tr class="{{ $fpCount === 0 ? 'row-attention-none' : ($fpCount < 3 ? 'row-attention-fp' : '') }}">
                            {{-- A — Empleado --}}
                            <td data-label="Empleado">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="avatar is-sm flex-shrink-0">{{ strtoupper(mb_substr($employee->name, 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <a href="{{ route('employees.edit', $employee) }}" class="fw-semibold text-decoration-none d-block text-truncate" title="{{ $employee->name }}">
                                            {{ $employee->name }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <code class="small" title="ID: {{ $employee->user_id }}">{{ $employee->user_id }}</code>
                                            @if($employee->numero_empleado && $employee->numero_empleado !== $employee->user_id)
                                                <span class="small text-tertiary-token">No. {{ $employee->numero_empleado }}</span>
                                            @endif
                                            @if(!empty($employee->fecha_ingreso))
                                                <span class="mono small text-tertiary-token" title="Fecha ingreso {{ \Carbon\Carbon::parse($employee->fecha_ingreso)->format('d/m/Y') }}">
                                                    {{ \Carbon\Carbon::parse($employee->fecha_ingreso)->locale('es')->isoFormat('MMM YYYY') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- B — Datos personales --}}
                            <td data-label="Datos personales">
                                <div class="small fw-semibold"><i class="bi bi-person-vcard me-1"></i>{{ $employee->sexo_label }}</div>
                                @if($employee->fecha_nacimiento)
                                    <div class="small text-secondary-token">Nacimiento: {{ $employee->fecha_nacimiento->format('d/m/Y') }}</div>
                                @endif
                                @if($employee->nacionalidad || $employee->estado_civil)
                                    <div class="small text-secondary-token">
                                        {{ $employee->nacionalidad ?: 'Nacionalidad no indicada' }}
                                        @if($employee->estado_civil) · {{ $employee->estado_civil }} @endif
                                    </div>
                                @endif
                                @if($employee->telefono || $employee->celular || $employee->email)
                                    <div class="small text-secondary-token text-truncate" title="{{ $employee->email }}">
                                        {{ $employee->telefono ?: $employee->celular ?: $employee->email }}
                                    </div>
                                @endif
                            </td>

                            {{-- C — Puesto --}}
                            <td data-label="Puesto">
                                @php
                                    $puestoLabel = $employee->puesto?->descripcion ?? $employee->cargo;
                                    $areaLabel = $employee->area?->descripcion ?? $employee->departamento;
                                @endphp
                                @if(!empty($puestoLabel))
                                    <div class="fw-semibold small text-truncate" title="{{ $puestoLabel }}">
                                        <i class="bi bi-briefcase me-1 text-tertiary-token"></i>{{ $puestoLabel }}
                                    </div>
                                    <div class="small text-secondary-token text-truncate" title="{{ $areaLabel }}">
                                        @if(!empty($areaLabel))
                                            <i class="bi bi-building me-1"></i>{{ $areaLabel }}
                                        @endif
                                    </div>
                                @else
                                    <span class="small text-tertiary-token">—</span>
                                    @if(!empty($areaLabel))
                                        <div class="small text-secondary-token text-truncate" title="{{ $areaLabel }}">
                                            <i class="bi bi-building me-1"></i>{{ $areaLabel }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            {{-- C — Adscripción --}}
                            <td data-label="Sede">
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    @if($sedeLabel !== '—')
                                        <span class="badge cat-blue" title="{{ $employee->id_campus ? 'ID_CAMPUS '.$employee->id_campus : '' }}">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $sedeLabel }}
                                        </span>
                                    @else
                                        <span class="small text-tertiary-token">—</span>
                                    @endif
                                    @if(!empty($employee->contrato))
                                        <span class="badge cat-gray">{{ $employee->contrato }}</span>
                                    @endif
                                    @if(!empty($employee->nivel))
                                        <span class="badge cat-purple" title="Nivel {{ $employee->nivel }}">{{ $employee->nivel }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- D — Horario contratado --}}
                            <td data-label="Horario contratado">
                                @forelse($employee->horariosLaborales as $horario)
                                    <div class="small text-nowrap">
                                        <span class="fw-semibold">{{ mb_substr($horario->diaNombre(), 0, 3) }}</span>
                                        {{ $horario->hora_entrada?->format('H:i') }}–{{ $horario->hora_salida?->format('H:i') }}
                                    </div>
                                @empty
                                    <span class="small text-tertiary-token">Sin horario registrado</span>
                                @endforelse
                            </td>

                            {{-- E — Hardware (huellas primero + dispositivos + sync) --}}
                            <td data-label="Hardware">
                                {{-- 1. HUELLAS — prioridad visual máxima --}}
                                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                    @include('employees.partials._fingerprint-badge', [
                                        'fpCount' => $fpCount,
                                        'fpMax' => $employee->fingerprints->unique('finger')->count() ?: null,
                                    ])
                                    {{-- Tarjeta RFID icon --}}
                                    @if($employee->devices->contains(fn($d) => filled($d->pivot->card_number)))
                                        <span class="small text-tertiary-token" title="Con tarjeta RFID">
                                            <i class="bi bi-credit-card"></i>
                                        </span>
                                    @endif
                                </div>
                                {{-- 2. DISPOSITIVOS — enrolamiento --}}
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    @forelse($employee->devices->take(2) as $device)
                                        <a href="{{ route('devices.show', $device) }}" class="ref-chip"
                                           title="UID {{ $device->pivot->device_uid }} · {{ $device->pivot->roleLabel() }} · {{ $device->pivot->card_number ? 'Tarjeta '.$device->pivot->card_number : 'Sin tarjeta' }}">
                                            <i class="bi bi-hdd-network"></i>{{ $device->name }}
                                        </a>
                                    @empty
                                        <span class="badge cat-gray">Sin enrolar</span>
                                    @endforelse
                                    @if($devicesCount > 2)
                                        <span class="badge cat-gray" title="{{ $employee->devices->slice(2)->pluck('name')->join(', ') }}">
                                            +{{ $devicesCount - 2 }}
                                        </span>
                                    @endif
                                </div>
                                {{-- 3. SINCRONIZACIÓN — último sync compacto --}}
                                @if($lastSync)
                                    @php
                                        $scMap = ['completed' => 'green', 'failed' => 'red', 'running' => 'amber', 'queued' => 'gray'];
                                        $sc = $scMap[$lastSync->status] ?? 'gray';
                                        $syncIcon = match($lastSync->status) {
                                            'completed' => 'bi-check-circle-fill',
                                            'failed' => 'bi-x-circle-fill',
                                            'running', 'queued' => 'bi-hourglass-split',
                                            default => 'bi-dash-circle',
                                        };
                                    @endphp
                                    <div class="small mono text-tertiary-token mt-1 text-truncate"
                                         title="{{ $lastSync->stage ?? '' }}{{ $lastSync->error_message ? ' · '.$lastSync->error_message : '' }}">
                                        <i class="{{ $syncIcon }} me-1" style="color: var(--cat-{{ $sc }});"></i>
                                        <span style="color: var(--cat-{{ $sc }});">{{ ucfirst($lastSync->status) }}</span>
                                        {{ $lastSync->finished_at?->format('d/m H:i') ?? $lastSync->created_at?->format('d/m H:i') }}
                                    </div>
                                @endif
                            </td>

                            {{-- G — Estado compuesto --}}
                            <td data-label="Estado">
                                @if($isBaja)
                                    <x-badge color="gray" label="Baja" />
                                @else
                                    <x-badge color="green" label="Activo" />
                                @endif
                                <div class="small mt-1">
                                    @if(!$enrolled)
                                        <x-badge color="gray" label="Sin enrolar" />
                                    @elseif($anyActive)
                                        <x-badge color="green" label="Enrolado" />
                                    @else
                                        <x-badge color="amber" label="Inactivo" />
                                    @endif
                                </div>
                            </td>

                            {{-- H — Acciones --}}
                            <td data-label="">
                                <div class="table-row-actions justify-content-end">
                                    @if(auth()->user()->canAccessModule('empleados', 'update'))
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-ghost"
                                           title="Editar empleado" aria-label="Editar {{ $employee->name }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($enrolled)
                                            <form action="{{ route('employees.sync-devices', $employee) }}" method="POST" class="d-inline" data-sync>
                                                @csrf
                                                @foreach($employee->devices as $d)
                                                    <input type="hidden" name="device_ids[]" value="{{ $d->id }}">
                                                @endforeach
                                                <button class="btn btn-sm btn-ghost"
                                                        title="Re-sincronizar en {{ $devicesCount }} checador(es)"
                                                        aria-label="Sincronizar {{ $employee->name }}">
                                                    <i class="bi bi-cloud-arrow-up"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                              data-confirm
                                              data-confirm-danger
                                              data-confirm-title="¿Quitar a {{ $employee->name }}?"
                                              data-confirm-message="Se dará de baja en todos sus checadores y, si no queda enrolado en ninguno, también del catálogo. Sus checadas históricas se conservan.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-icon-danger" title="Dar de baja" aria-label="Dar de baja {{ $employee->name }}">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </form>
                                    @else
                                        @foreach($employee->devices as $device)
                                            <a href="{{ route('devices.show', $device) }}" class="btn btn-sm btn-ghost"
                                               title="Ver {{ $device->name }}" aria-label="Ver dispositivo {{ $device->name }}">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty state: con filtros --}}
                        <tr id="empty-with-filters" style="{{ request()->hasAny(['q','cargo','departamento','id_campus','sin_huella','sin_device']) ? '' : 'display:none' }}">
                            <td colspan="6">
                                @include('partials.empty-state', [
                                    'icon'     => 'bi-search',
                                    'title'    => 'Sin resultados para tu filtro',
                                    'desc'     => 'Prueba con otro nombre, ID, puesto o departamento, o limpia los filtros.',
                                    'cta'      => ['label' => 'Limpiar filtros', 'url' => route('employees.index')],
                                    'ctaLink'  => true,
                                ])
                            </td>
                        </tr>
                        {{-- Empty state: sin filtros --}}
                        <tr id="empty-no-filters" style="{{ request()->hasAny(['q','cargo','departamento','id_campus','sin_huella','sin_device']) ? 'display:none' : '' }}">
                            <td colspan="6">
                                @include('partials.empty-state', [
                                    'icon'     => 'bi-people',
                                    'title'    => 'No hay empleados',
                                    'desc'     => 'Vacía los checadores con «Traer usuarios» o sincroniza Firebird EMPLEADOS para poblar el catálogo.',
                                    'cta'      => auth()->user()->canAccessModule('empleados', 'create')
                                        ? ['label' => 'Agregar empleado', 'url' => route('employees.create')]
                                        : null,
                                    'ctaLink'  => true,
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Skeleton rows (hidden, shown during AJAX search) --}}
    <div id="employees-skeleton" style="display:none">
        @for($i = 0; $i < 5; $i++)
            <div class="skeleton skeleton-row mb-1"></div>
        @endfor
    </div>

    {{-- Error panel (hidden, shown on fetch failure) --}}
    <div id="employees-error" class="panel-error" style="display:none" role="alert">
        <i class="bi bi-wifi-off"></i>
        <div class="pe-title">No se pudo cargar la información de empleados.</div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i> Reintentar
        </button>
    </div>

    {{-- Footer: Mostrando X–Y de Z + per_page + paginación --}}
    @if($employees->hasPages())
        <div class="data-table-footer">
            <div class="data-table-summary">
                @php
                    $fromItem = $employees->total() > 0 ? $employees->firstItem() : 0;
                    $toItem = $employees->total() > 0 ? $employees->lastItem() : 0;
                @endphp
                Mostrando {{ $fromItem }}–{{ $toItem }} de {{ $employees->total() }} registros
            </div>

            <div class="data-table-controls">
                <span class="small text-tertiary-token">Filas</span>
                <div class="data-table-per-page">
                    @foreach([25, 50, 100] as $option)
                        @php
                            $query = request()->query();
                            unset($query['page']);
                            $query['per_page'] = $option;
                            $url = request()->url() . '?' . http_build_query($query);
                        @endphp
                        <a href="{{ $url }}" class="data-table-per-page-link {{ (int) request('per_page', 25) == $option ? 'active' : '' }}">{{ $option }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-footer border-0 bg-transparent pt-0 pagination-footer">
            {{ $employees->links() }}
        </div>
    @endif
</div>

@endsection

{{-- Inline search script removed: employees-index.js handles search via AJAX with debounce + skeleton --}}
