@extends('layouts.admin')

@section('title', 'Academia - Dashboard')
@section('breadcrumb', 'Academia › Dashboard')

@section('content')
{{-- ========== CICLO HEADER — Selector Institucional + Contexto ========== --}}
<section aria-labelledby="ciclo-heading" class="card shadow-sm border mb-4 dashboard-header-card">
  <div class="card-body p-3 p-md-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-lg-6">
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
          <span class="badge {{ $ciclo->activo ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} px-2 py-1">
            <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i>{{ $ciclo->activo ? 'Ciclo Activo' : 'Ciclo Inactivo' }}
          </span>
          <span class="badge bg-light text-secondary border px-2 py-1" style="font-family:'JetBrains Mono',monospace">
            <i class="bi bi-calendar-event me-1"></i>{{ $ciclo->fechaInicialFormateada }} &mdash; {{ $ciclo->fechaFinalFormateada }}
          </span>
        </div>
        <h1 id="ciclo-heading" class="h5 fw-bold mb-1 d-flex align-items-center gap-2" data-cycle-label>
          <i class="bi bi-mortarboard text-primary"></i>
          <span>{{ $ciclo->label }} &mdash; {{ $ciclo->descripcion ?: 'Ciclo Institucional' }}</span>
        </h1>
        <span class="visually-hidden">Dashboard de Académica</span>
<p class="small text-secondary mb-0">
          Supervisión integral de matrícula, oferta de cursos, planes de estudio y plantilla docente en este ciclo.
        </p>
      </div>

      <div class="col-12 col-lg-6">
        <form id="ciclo-switcher" method="GET" action="{{ route('academia.dashboard') }}" class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
          <div class="input-group input-group-sm" style="max-width: 320px;">
            <span class="input-group-text bg-surface-2 border-end-0">
              <i class="bi bi-calendar3 text-muted"></i>
            </span>
            <select name="ciclo_principal" data-cycle-select class="form-select form-select-sm border-start-0" aria-label="Seleccionar ciclo escolar" onchange="this.form.submit()">
              @foreach ($ciclosDisponibles as $c)
                <option value="{{ $c->label }}" {{ $ciclo->label === $c->label ? 'selected' : '' }}>
                  {{ $c->label }} · {{ Str::limit($c->descripcion ?: 'Sin descripción', 25) }} {{ $c->activo ? '(Activo)' : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="btn-group btn-group-sm">
            <a href="{{ route('academia.ciclos.show', $ciclo) }}" class="btn btn-outline-primary btn-sm" title="Ver detalles y configuración del ciclo">
              <i class="bi bi-info-circle me-1"></i> Detalle
            </a>
            <a href="{{ route('academia.ciclos.index') }}" class="btn btn-outline-secondary btn-sm" title="Administrar catálogo de ciclos">
              <i class="bi bi-sliders me-1"></i> Ciclos
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card-footer bg-surface-2 px-3 px-md-4 py-2 small text-tertiary-token d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-info-circle text-primary"></i>
      <span>Los indicadores de este panel corresponden a la programación académica del ciclo <strong>{{ $ciclo->label }}</strong>.</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('academia.grupos.index', ['ciclo_principal' => $ciclo->label]) }}" class="text-decoration-none small text-secondary hover-primary">
        <i class="bi bi-people me-1"></i>Grupos
      </a>
      <a href="{{ route('academia.alumnos.index', ['ciclo_principal' => $ciclo->label]) }}" class="text-decoration-none small text-secondary hover-primary">
        <i class="bi bi-mortarboard me-1"></i>Alumnos
      </a>
      <a href="{{ route('academia.profesores.index') }}" class="text-decoration-none small text-secondary hover-primary">
        <i class="bi bi-person-badge me-1"></i>Profesores
      </a>
      <a href="{{ route('academia.horarios.clase', ['ciclo_principal' => $ciclo->label]) }}" class="text-decoration-none small text-secondary hover-primary">
        <i class="bi bi-calendar-week me-1"></i>Horarios
      </a>
    </div>
  </div>
</section>

{{-- ========== ALERTA CICLO VACÍO ========== --}}
@if (($kpis['grupos'] ?? 0) === 0 && ($kpis['horarios'] ?? 0) === 0)
  <div class="alert alert-warning border-warning-subtle shadow-sm d-flex align-items-center gap-3 mb-4" role="status">
    <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
    <div class="flex-grow-1">
      <div class="fw-semibold">Ciclo sin programación académica registrada</div>
      <div class="small">
        El ciclo <strong>{{ $ciclo->label }}</strong> aún no contiene grupos ni horarios asignados. 
        Puede <a href="{{ route('academia.grupos.index', ['ciclo_principal' => $ciclo->label]) }}" class="alert-link">crear grupos</a> 
        o seleccionar otro ciclo en el selector superior.
      </div>
    </div>
  </div>
@endif

{{-- ========== KPIs PRINCIPALES (Grid Simétrico de 4 Columnas) ========== --}}
<section aria-labelledby="extras-heading" class="mb-4">
  <h2 id="extras-heading" class="h6 fw-bold mb-3">Resumen Extra del Ciclo</h2>
  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="card h-100 shadow-sm border">
        <div class="card-body p-3">
          <h5 class="card-title mb-2">Total de Asignaciones</h5>
          <p class="fs-4 fw-bold text-primary">{{ number_format($totalAsignaciones) }}</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card h-100 shadow-sm border">
        <div class="card-body p-3">
          <h5 class="card-title mb-2">Turnos</h5>
          <ul class="list-unstyled mb-0">
            @foreach($turnosInfo as $turno)
              <li>{{ $turno['label'] }}: {{ number_format($turno['count']) }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card h-100 shadow-sm border">
        <div class="card-body p-3">
          <h5 class="card-title mb-2">Modalidades</h5>
          <ul class="list-unstyled mb-0">
            @foreach($modalidadInfo as $mod => $cnt)
              <li>{{ $mod }}: {{ number_format($cnt) }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

  <h2 id="kpis-heading" class="visually-hidden">Indicadores Clave de Desempeño</h2>
  
  <div class="row g-3" id="kpi-region" aria-live="polite" aria-busy="false" data-kpis-url="{{ route('academia.kpisJson') }}" data-cycle="{{ $ciclo->label }}">
    {{-- KPI 1: Alumnos --}}
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 shadow-sm border kpi-card" style="border-left: 4px solid var(--cat-blue) !important;">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <span class="text-uppercase small text-secondary fw-semibold" style="font-size:11px;letter-spacing:0.05em">Alumnos Inscritos</span>
              <div class="fs-3 fw-bold mt-1 text-primary" data-stat-value style="font-family:'JetBrains Mono',monospace">
                {{ number_format($kpis['alumnos']) }}
              </div>
            </div>
            <div class="kpi-icon rounded-3 d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(59,130,246,0.12);color:var(--cat-blue)">
              <i class="bi bi-mortarboard fs-5"></i>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
            <span class="text-secondary" data-kpi-caption>
              @if(isset($totales['alumnos']) && $totales['alumnos'] > 0)
                de {{ number_format($totales['alumnos']) }} activos
              @else
                en este ciclo
              @endif
            </span>
            <a href="{{ route('academia.alumnos.index', ['ciclo_principal' => $ciclo->label]) }}" class="text-primary text-decoration-none small" title="Ver lista de alumnos">
              Ver <i class="bi bi-chevron-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- KPI 2: Grupos --}}
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 shadow-sm border kpi-card" style="border-left: 4px solid var(--cat-purple) !important;">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <span class="text-uppercase small text-secondary fw-semibold" style="font-size:11px;letter-spacing:0.05em">Grupos Formados</span>
              <div class="fs-3 fw-bold mt-1" data-stat-value style="font-family:'JetBrains Mono',monospace;color:var(--cat-purple)">
                {{ number_format($kpis['grupos']) }}
              </div>
            </div>
            <div class="kpi-icon rounded-3 d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(147,51,234,0.12);color:var(--cat-purple)">
              <i class="bi bi-people fs-5"></i>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
            <span class="text-secondary" data-kpi-caption>
              @if(isset($totales['grupos']) && $totales['grupos'] > 0)
                de {{ number_format($totales['grupos']) }} en total
              @else
                en este ciclo
              @endif
            </span>
            <a href="{{ route('academia.grupos.index', ['ciclo_principal' => $ciclo->label]) }}" class="text-decoration-none small" style="color:var(--cat-purple)" title="Ver grupos">
              Ver <i class="bi bi-chevron-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- KPI 3: Profesores --}}
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 shadow-sm border kpi-card" style="border-left: 4px solid var(--cat-green) !important;">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <span class="text-uppercase small text-secondary fw-semibold" style="font-size:11px;letter-spacing:0.05em">Profesores Asignados</span>
              <div class="fs-3 fw-bold mt-1" data-stat-value style="font-family:'JetBrains Mono',monospace;color:var(--cat-green)">
                {{ number_format($kpis['profesores_ciclo'] ?? $kpis['profesores']) }}
              </div>
            </div>
            <div class="kpi-icon rounded-3 d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(16,185,129,0.12);color:var(--cat-green)">
              <i class="bi bi-person-badge fs-5"></i>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
            <span class="text-secondary" data-kpi-caption>
              @if(isset($totales['profesores']) && $totales['profesores'] > 0)
                de {{ number_format($totales['profesores']) }} registrados
              @else
                con carga académica
              @endif
            </span>
            <a href="{{ route('academia.profesores.index') }}" class="text-decoration-none small" style="color:var(--cat-green)" title="Ver profesores">
              Ver <i class="bi bi-chevron-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- KPI 4: Horarios --}}
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 shadow-sm border kpi-card" style="border-left: 4px solid var(--cat-orange) !important;">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <span class="text-uppercase small text-secondary fw-semibold" style="font-size:11px;letter-spacing:0.05em">Horarios Programados</span>
              <div class="fs-3 fw-bold mt-1" data-stat-value style="font-family:'JetBrains Mono',monospace;color:var(--cat-orange)">
                {{ number_format($kpis['horarios']) }}
              </div>
            </div>
            <div class="kpi-icon rounded-3 d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(249,115,22,0.12);color:var(--cat-orange)">
              <i class="bi bi-calendar-week fs-5"></i>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
            <span class="text-secondary" data-kpi-caption>
              @if(isset($totales['horarios']) && $totales['horarios'] > 0)
                de {{ number_format($totales['horarios']) }} totales
              @else
                sesiones semanales
              @endif
            </span>
            <a href="{{ route('academia.horarios.clase', ['ciclo_principal' => $ciclo->label]) }}" class="text-decoration-none small" style="color:var(--cat-orange)" title="Ver horarios">
              Ver <i class="bi bi-chevron-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Fila Secundaria de Resumen Operativo --}}
  <div class="row g-2 mt-1">
    <div class="col-6 col-md-3">
      <div class="card bg-surface-2 border py-2 px-3 d-flex flex-row align-items-center justify-content-between">
        <span class="small text-secondary"><i class="bi bi-book me-1 text-teal"></i>Cursos Ofertados:</span>
        <span class="fw-bold mono text-teal" style="font-family:'JetBrains Mono',monospace">{{ number_format($kpis['cursos']) }}</span>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card bg-surface-2 border py-2 px-3 d-flex flex-row align-items-center justify-content-between">
        <span class="small text-secondary"><i class="bi bi-collection me-1 text-purple"></i>Planes Activos:</span>
        <span class="fw-bold mono text-purple" style="font-family:'JetBrains Mono',monospace">{{ number_format($kpis['planes']) }}</span>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card bg-surface-2 border py-2 px-3 d-flex flex-row align-items-center justify-content-between">
        <span class="small text-secondary"><i class="bi bi-journal-bookmark me-1 text-amber"></i>Materias Ciclo:</span>
        <span class="fw-bold mono text-amber" style="font-family:'JetBrains Mono',monospace">{{ number_format($kpis['materias']) }}</span>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card bg-surface-2 border py-2 px-3 d-flex flex-row align-items-center justify-content-between">
        <span class="small text-secondary"><i class="bi bi-file-earmark-text me-1 text-pink"></i>Evaluaciones Kárdex:</span>
        <span class="fw-bold mono text-pink" style="font-family:'JetBrains Mono',monospace">{{ number_format($kpis['kardex'] ?? 0) }}</span>
      </div>
    </div>
  </div>
</section>

{{-- ========== RESUMEN ACADÉMICO (Gráficas / Indicadores) ========== --}}
<section aria-labelledby="resumen-heading" class="mb-4">
  <div class="section-heading mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h2 id="resumen-heading" class="h6 fw-bold mb-0">Resumen Académico &amp; Carga Horaria</h2>
      <span class="small text-secondary">Programación semanal de horarios y distribución por tipo de plaza</span>
    </div>
    <span class="badge bg-surface-2 text-secondary border">Ciclo {{ $ciclo->label }}</span>
  </div>

  <div class="row g-3">
    {{-- Gráfica de Horarios por Día --}}
    <div class="col-12 col-lg-8">
      <div class="card h-100 shadow-sm border" data-chart="horarios-dia" data-cycle="{{ $ciclo->label }}">
        <div class="card-header bg-surface-1 border-bottom d-flex justify-content-between align-items-center py-2 px-3">
          <span class="fw-bold small text-uppercase" style="letter-spacing:0.04em">
            <i class="bi bi-graph-up text-primary me-1"></i> Horarios Programados por Día
          </span>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle" data-chart-total>
            {{ number_format(array_sum($horariosPorDia)) }} asignaciones
          </span>
        </div>
        <div class="card-body p-3 position-relative" style="min-height: 180px;">
          @php
            $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
            $data = [];
            foreach ($dias as $d => $label) {
                $data[] = ['label' => $label, 'short' => substr($label, 0, 3), 'value' => $horariosPorDia[$d] ?? 0];
            }
            $points = array_column($data, 'value');
            $max = max(1, ...$points);
            $w = 700; $h = 130; $padX = 20; $padT = 16; $padB = 22;
            $n = count($points);
            $stepX = $n > 1 ? ($w - $padX * 2) / ($n - 1) : 0;
            $coords = [];
            foreach ($points as $i => $v) {
                $x = $n > 1 ? round($padX + $i * $stepX, 1) : $w / 2;
                $y = round($padT + ($h - $padT - $padB) * (1 - ($v / $max)), 1);
                $coords[] = [$x, $y];
            }
            $linePath = 'M ' . implode(' L ', array_map(fn ($c) => $c[0] . ',' . $c[1], $coords));
            $first = $coords[0];
            $last = $coords[$n - 1];
            $areaPath = $n > 1
                ? "M {$first[0]},{$first[1]} " . implode(' ', array_map(fn ($c) => "L {$c[0]},{$c[1]}", array_slice($coords, 1))) . " L {$last[0]}," . ($h - $padB) . " L {$first[0]}," . ($h - $padB) . " Z"
                : '';
          @endphp

          @if(array_sum($horariosPorDia) === 0)
            <div class="text-center py-4 text-secondary">
              <i class="bi bi-calendar-x fs-2 d-block mb-2 text-muted"></i>
              <div class="fw-semibold">Sin horarios programados en este ciclo</div>
              <div class="small text-muted mb-2">No se han registrado asignaciones de horario en el ciclo seleccionado.</div>
              <a href="{{ route('academia.horarios.clase', ['ciclo_principal' => $ciclo->label]) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-calendar-plus me-1"></i> Ir a Horarios
              </a>
            </div>
          @else
            <svg class="trend-chart w-100" viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="none" role="img" aria-label="Horarios por día en ciclo {{ $ciclo->label }}">
              <defs>
                <linearGradient id="trendGradientAcademia" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.32"/>
                  <stop offset="100%" stop-color="var(--primary)" stop-opacity="0.02"/>
                </linearGradient>
              </defs>
              {{-- Línea base horizontal --}}
              <line x1="{{ $padX }}" y1="{{ $h - $padB }}" x2="{{ $w - $padX }}" y2="{{ $h - $padB }}" stroke="var(--border)" stroke-width="1" stroke-dasharray="3 3"/>
              @if ($areaPath) 
                <path class="area-fill" d="{{ $areaPath }}" fill="url(#trendGradientAcademia)"/> 
              @endif
              <path class="area-line" d="{{ $linePath }}" stroke="var(--primary)" stroke-width="2.5" fill="none"/>
              @foreach ($coords as $i => [$x, $y])
                <circle class="area-dot" cx="{{ $x }}" cy="{{ $y }}" r="4.5" fill="var(--primary)" stroke="var(--surface-1)" stroke-width="1.5">
                  <title>{{ $data[$i]['label'] }}: {{ number_format($data[$i]['value']) }} horarios</title>
                </circle>
                <text x="{{ $x }}" y="{{ $h - 5 }}" font-size="11" font-weight="600" fill="var(--text-secondary)" text-anchor="middle">
                  {{ $data[$i]['short'] }} ({{ $data[$i]['value'] }})
                </text>
              @endforeach
            </svg>

            <table class="visually-hidden">
              <caption>Horarios programados por día de la semana</caption>
              <thead><tr><th scope="col">Día</th><th scope="col">Horarios</th></tr></thead>
              <tbody>
                @foreach($data as $d)
                  <tr><td>{{ $d['label'] }}</td><td>{{ $d['value'] }}</td></tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>

    {{-- Distribución por Origen / Tipo de Horario --}}
    <div class="col-12 col-lg-4">
      <div class="card h-100 shadow-sm border">
        <div class="card-header bg-surface-1 border-bottom d-flex justify-content-between align-items-center py-2 px-3">
          <span class="fw-bold small text-uppercase" style="letter-spacing:0.04em">
            <i class="bi bi-pie-chart text-primary me-1"></i> Tipo de Contratación
          </span>
          <span class="badge bg-secondary-subtle text-secondary">PA vs PTC</span>
        </div>
        <div class="card-body p-3 d-flex flex-column justify-content-between">
          @php
            $hdCount = $porOrigen['HD'] ?? 0;
            $caCount = $porOrigen['CA'] ?? 0;
            $sinDefCount = $porOrigen['SIN_DEFINIR'] ?? 0;
            $totalOrigen = $hdCount + $caCount + $sinDefCount;
            $hdPct = $totalOrigen > 0 ? round(($hdCount / $totalOrigen) * 100) : 0;
            $caPct = $totalOrigen > 0 ? round(($caCount / $totalOrigen) * 100) : 0;
          @endphp

          @if($totalOrigen === 0)
            <div class="text-center py-4 text-secondary">
              <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
              <div class="small">Sin registros de origen docente en este ciclo</div>
            </div>
          @else
            <div>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-secondary">Proporción de Asignación Docente</span>
                <span class="small fw-semibold">{{ number_format($totalOrigen) }} clases</span>
              </div>
              <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $hdPct }}%" aria-valuenow="{{ $hdPct }}" aria-valuemin="0" aria-valuemax="100" title="PTC: {{ $hdPct }}%"></div>
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $caPct }}%" aria-valuenow="{{ $caPct }}" aria-valuemin="0" aria-valuemax="100" title="PA: {{ $caPct }}%"></div>
              </div>

              <div class="list-group list-group-flush border rounded-3 mb-2">
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                  <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle me-1">PTC</span>
                    <span class="fw-semibold small">Tiempo Completo (HD)</span>
                  </div>
                  <div class="text-end">
                    <span class="fw-bold mono small" style="font-family:'JetBrains Mono',monospace">{{ number_format($hdCount) }}</span>
                    <span class="text-muted small">({{ $hdPct }}%)</span>
                  </div>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                  <div>
                    <span class="badge bg-info-subtle text-info border border-info-subtle me-1">PA</span>
                    <span class="fw-semibold small">Asignatura (CA)</span>
                  </div>
                  <div class="text-end">
                    <span class="fw-bold mono small" style="font-family:'JetBrains Mono',monospace">{{ number_format($caCount) }}</span>
                    <span class="text-muted small">({{ $caPct }}%)</span>
                  </div>
                </div>

                @if($sinDefCount > 0)
                  <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <div>
                      <span class="badge bg-secondary-subtle text-secondary me-1">N/D</span>
                      <span class="small text-secondary">Sin definir</span>
                    </div>
                    <div class="text-end">
                      <span class="fw-bold mono small">{{ number_format($sinDefCount) }}</span>
                    </div>
                  </div>
                @endif
              </div>
            </div>

            <div class="p-2 rounded bg-surface-2 border small text-secondary d-flex align-items-center gap-2">
              <i class="bi bi-shield-check text-success"></i>
              <span>Carga académica activa vinculada a la nómina institucional.</span>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ========== SECCIÓN MATERIAS Y PLANES (Tablas Principales Simétricas) ========== --}}
<section aria-labelledby="curricula-heading" class="mb-4">
  <div class="section-heading mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h2 id="curricula-heading" class="h6 fw-bold mb-0">Oferta Curricular: Materias y Planes de Estudio</h2>
      <span class="small text-secondary">Tablas institucionales con búsqueda dinámica, selector de registros y exportación</span>
    </div>
  </div>

  <div class="row g-4">
    {{-- Columna 1: Materias --}}
    <div class="col-12 col-xl-6" id="seccion-materias">
      <div class="card h-100 shadow-sm border dashboard-table-card">
        {{-- Header + Toolbar Materias --}}
        <div class="card-header bg-surface-1 border-bottom p-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <div class="p-2 rounded bg-amber-subtle text-amber d-inline-flex" style="background:rgba(245,158,11,0.12);color:var(--cat-amber)">
                <i class="bi bi-journal-bookmark fs-5"></i>
              </div>
              <div>
                <h3 class="h6 fw-bold mb-0">Materias</h3>
                <span class="small text-secondary">Asignaturas de este ciclo</span>
              </div>
            </div>
            <span class="badge bg-surface-2 text-secondary border px-2 py-1 js-table-count-badge">
              {{ number_format($materias->count()) }} registros
            </span>
          </div>

          {{-- Barra de Herramientas Materias --}}
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
            <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 200px;">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-surface-2 border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control form-control-sm border-start-0 js-table-search" data-table-id="materias-dashboard-table" placeholder="Buscar materia, clave o carrera..." aria-label="Buscar materia">
              </div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <div class="d-flex align-items-center gap-1">
                <label class="small text-muted mb-0 me-1 d-none d-sm-inline">Ver:</label>
                <select class="form-select form-select-sm js-table-size" data-table-id="materias-dashboard-table" style="width: 70px;" aria-label="Cantidad de filas">
                  <option value="20" selected>20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>

              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success js-export-excel" data-table-id="materias-dashboard-table" data-filename="materias-ciclo-{{ $ciclo->label }}" title="Exportar a Excel">
                  <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </button>
                <button type="button" class="btn btn-outline-primary js-export-csv" data-table-id="materias-dashboard-table" data-filename="materias-ciclo-{{ $ciclo->label }}" title="Exportar a CSV">
                  <i class="bi bi-file-earmark-text me-1"></i>CSV
                </button>
                <button type="button" class="btn btn-outline-danger js-export-pdf" data-table-id="materias-dashboard-table" data-filename="materias-ciclo-{{ $ciclo->label }}" title="Imprimir / Guardar como PDF">
                  <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- Tabla de Materias --}}
        <div class="table-responsive" style="min-height: 280px; max-height: 480px;">
          <table id="materias-dashboard-table" class="table table-hover table-sm align-middle mb-0 dashboard-data-table">
            <thead class="table-light sticky-top">
              <tr>
                <th scope="col" class="sortable text-nowrap" data-sort="string" style="cursor:pointer;" title="Ordenar por clave/materia">
                  Materia / Clave <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable" data-sort="string" style="cursor:pointer;" title="Ordenar por carrera">
                  Carrera / Plan <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-center" data-sort="string" style="cursor:pointer; width: 85px;" title="Ordenar por grado">
                  Grado <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-end" data-sort="number" style="cursor:pointer; width: 90px;" title="Ordenar por horas">
                  Horas <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="text-center" style="width: 80px;">Estado</th>
              </tr>
            </thead>
            <tbody>
              @forelse($materias as $materia)
                @php
                  $plan = $materia->plan;
                  $carrera = $plan?->nivelRel?->descripcion ?? $plan?->nivel ?? 'Sin plan asignado';
                  $horasTotales = $materia->horas_totales;
                @endphp
                <tr>
                  <td>
                    <div class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $materia->nombre_asignatura }}">
                      {{ $materia->nombre_asignatura }}
                    </div>
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;">
                      {{ $materia->clave_asignatura }}
                    </span>
                  </td>
                  <td>
                    <div class="small text-truncate" style="max-width: 180px;" title="{{ $carrera }}">
                      {{ $carrera }}
                    </div>
                    @if($plan)
                      <span class="text-muted small" style="font-size: 10px;">Plan: {{ $plan->nombre_plan }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($materia->semestre)
                      <span class="badge bg-surface-2 text-secondary border">{{ $materia->semestre }}°</span>
                    @else
                      <span class="text-muted small" title="Semestre no registrado en catálogo">N/D</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if($horasTotales > 0)
                      <span class="fw-semibold mono">{{ $horasTotales }} hrs</span>
                    @elseif($materia->horas_teoria > 0 || $materia->horas_practica > 0)
                      <span class="fw-semibold mono">{{ ($materia->horas_teoria ?? 0) + ($materia->horas_practica ?? 0) }} hrs</span>
                    @else
                      <span class="text-muted small">0 hrs</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $materia->activa ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }}">
                      {{ $materia->activa ? 'Activa' : 'Inactiva' }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-secondary">
                    <i class="bi bi-journal-x fs-3 d-block mb-2 text-muted"></i>
                    <div>Sin materias programadas para este ciclo</div>
                    <div class="small text-muted mt-1">Seleccione otro ciclo escolar para consultar asignaturas.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Footer con Contador y Paginación --}}
        <div class="card-footer bg-surface-1 border-top py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="small text-secondary js-table-info" data-table-id="materias-dashboard-table">
            Mostrando 1 a 20 de {{ number_format($materias->count()) }} materias
          </div>
          <div class="js-table-pagination" data-table-id="materias-dashboard-table"></div>
        </div>
      </div>
    </div>

    {{-- Columna 2: Planes de Estudio --}}
    <div class="col-12 col-xl-6" id="seccion-planes">
      <div class="card h-100 shadow-sm border dashboard-table-card">
        {{-- Header + Toolbar Planes --}}
        <div class="card-header bg-surface-1 border-bottom p-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <div class="p-2 rounded bg-purple-subtle text-purple d-inline-flex" style="background:rgba(147,51,234,0.12);color:var(--cat-purple)">
                <i class="bi bi-collection fs-5"></i>
              </div>
              <div>
                <h3 class="h6 fw-bold mb-0">Planes de Estudio</h3>
                <span class="small text-secondary">Programas educativos vinculados</span>
              </div>
            </div>
            <span class="badge bg-surface-2 text-secondary border px-2 py-1 js-table-count-badge">
              {{ number_format($planes->count()) }} registros
            </span>
          </div>

          {{-- Barra de Herramientas Planes --}}
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
            <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 200px;">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-surface-2 border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control form-control-sm border-start-0 js-table-search" data-table-id="planes-dashboard-table" placeholder="Buscar plan o nivel..." aria-label="Buscar plan de estudio">
              </div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <div class="d-flex align-items-center gap-1">
                <label class="small text-muted mb-0 me-1 d-none d-sm-inline">Ver:</label>
                <select class="form-select form-select-sm js-table-size" data-table-id="planes-dashboard-table" style="width: 70px;" aria-label="Cantidad de filas">
                  <option value="20" selected>20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>

              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success js-export-excel" data-table-id="planes-dashboard-table" data-filename="planes-ciclo-{{ $ciclo->label }}" title="Exportar a Excel">
                  <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </button>
                <button type="button" class="btn btn-outline-primary js-export-csv" data-table-id="planes-dashboard-table" data-filename="planes-ciclo-{{ $ciclo->label }}" title="Exportar a CSV">
                  <i class="bi bi-file-earmark-text me-1"></i>CSV
                </button>
                <button type="button" class="btn btn-outline-danger js-export-pdf" data-table-id="planes-dashboard-table" data-filename="planes-ciclo-{{ $ciclo->label }}" title="Imprimir / Guardar como PDF">
                  <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- Tabla de Planes --}}
        <div class="table-responsive" style="min-height: 280px; max-height: 480px;">
          <table id="planes-dashboard-table" class="table table-hover table-sm align-middle mb-0 dashboard-data-table">
            <thead class="table-light sticky-top">
              <tr>
                <th scope="col" class="sortable text-center" data-sort="number" style="cursor:pointer; width: 70px;" title="Ordenar por ID">
                  ID <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable" data-sort="string" style="cursor:pointer;" title="Ordenar por nombre de plan">
                  Plan de Estudio <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable" data-sort="string" style="cursor:pointer;" title="Ordenar por carrera">
                  Nivel / Carrera <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-center" data-sort="number" style="cursor:pointer; width: 100px;" title="Ordenar por materias">
                  Materias <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="text-center" style="width: 80px;">Estado</th>
                <th scope="col" class="text-end" style="width: 70px;">Acción</th>
              </tr>
            </thead>
            <tbody>
              @forelse($planes as $plan)
                <tr>
                  <td class="text-center font-monospace fw-semibold small">
                    {{ $plan->id_plan }}
                  </td>
                  <td>
                    <div class="fw-semibold text-truncate" style="max-width: 200px;" title="{{ $plan->nombre_plan }}">
                      {{ $plan->nombre_plan }}
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                      @if($plan->nivel)
                        <span class="badge bg-light text-secondary border font-monospace" style="font-size:10px;">{{ $plan->nivel }}</span>
                      @endif
                      <span class="small text-truncate" style="max-width: 170px;" title="{{ $plan->nivelRel?->descripcion ?? 'Sin descripción' }}">
                        {{ $plan->nivelRel?->descripcion ?? 'N/D' }}
                      </span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                      {{ $plan->materias_count }} mat.
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $plan->activo ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }}">
                      {{ $plan->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="text-end">
                    <a href="{{ route('academia.planes.show', $plan) }}" class="btn btn-outline-primary btn-sm p-1" title="Ver plan de estudio">
                      <i class="bi bi-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-secondary">
                    <i class="bi bi-collection fs-3 d-block mb-2 text-muted"></i>
                    <div>Sin planes de estudio registrados en este ciclo</div>
                    <div class="small text-muted mt-1">Seleccione otro ciclo o cree un nuevo plan.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Footer con Contador y Paginación --}}
        <div class="card-footer bg-surface-1 border-top py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="small text-secondary js-table-info" data-table-id="planes-dashboard-table">
            Mostrando 1 a 20 de {{ number_format($planes->count()) }} planes
          </div>
          <div class="js-table-pagination" data-table-id="planes-dashboard-table"></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ========== DESGLOSE OPERATIVO INSTITUCIONAL (Sección 8) ========== --}}
<section aria-labelledby="desglose-heading" class="mb-4">
  <div class="section-heading mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h2 id="desglose-heading" class="h6 fw-bold mb-0">Desglose Operativo Institucional</h2>
      <span class="small text-secondary">Distribución de matrícula, oferta por sede y programación por docente</span>
    </div>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Operaciones del Ciclo</span>
  </div>

  {{-- Métricas de Síntesis Operativa (4 Mini Cards) --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="card h-100 shadow-sm border p-3">
        <div class="small text-secondary fw-semibold text-uppercase" style="font-size:10px;letter-spacing:0.05em">Matrícula por Grupos</div>
        <div class="h5 fw-bold mb-1 text-primary mono mt-1">{{ number_format($kpis['alumnos']) }} alumnos</div>
        <div class="small text-muted">{{ $dashboardSummary['alumnosPorGrupo']->count() }} combinaciones grado/sede</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card h-100 shadow-sm border p-3">
        <div class="small text-secondary fw-semibold text-uppercase" style="font-size:10px;letter-spacing:0.05em">Campus y Sedes</div>
        <div class="h5 fw-bold mb-1 text-success mono mt-1">{{ $dashboardSummary['cursosPorSede']->count() }} sedes activas</div>
        <div class="small text-muted">{{ $dashboardSummary['cursosPorSede']->sum('cursos') }} cursos distribuidos</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card h-100 shadow-sm border p-3">
        <div class="small text-secondary fw-semibold text-uppercase" style="font-size:10px;letter-spacing:0.05em">Niveles &amp; Turnos</div>
        <div class="h5 fw-bold mb-1 text-purple mono mt-1">{{ $dashboardSummary['niveles']->count() }} niveles</div>
        <div class="small text-muted">{{ $dashboardSummary['turnos']->count() }} turnos de operación</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card h-100 shadow-sm border p-3">
        <div class="small text-secondary fw-semibold text-uppercase" style="font-size:10px;letter-spacing:0.05em">Carga Horaria</div>
        <div class="h5 fw-bold mb-1 text-orange mono mt-1">{{ number_format($dashboardSummary['horasPorOrigen']->sum('clases')) }} clases</div>
        <div class="small text-muted">{{ number_format($dashboardSummary['profesoresPorOrigen']->sum('profesores')) }} profesores asignados</div>
      </div>
    </div>
  </div>

  {{-- Tablas de Detalle Operativo en Grid Simétrico --}}
  <div class="row g-4">
    {{-- Tabla 1: Alumnos por Grado, Modalidad y Sede --}}
    <div class="col-12 col-lg-7">
      <div class="card h-100 shadow-sm border dashboard-table-card">
        <div class="card-header bg-surface-1 border-bottom p-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div>
              <h3 class="h6 fw-bold mb-0">Alumnos por Grado, Modalidad y Sede</h3>
              <span class="small text-secondary">Distribución de la matrícula institucional</span>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 js-table-count-badge">
              {{ number_format($dashboardSummary['alumnosPorGrupo']->count()) }} grupos
            </span>
          </div>

          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
            <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 180px;">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-surface-2 border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control form-control-sm border-start-0 js-table-search" data-table-id="students-breakdown-table" placeholder="Filtrar grado, modalidad o sede..." aria-label="Filtrar distribución de alumnos">
              </div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <select class="form-select form-select-sm js-table-size" data-table-id="students-breakdown-table" style="width: 70px;" aria-label="Cantidad de filas">
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>

              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success js-export-excel" data-table-id="students-breakdown-table" data-filename="alumnos-distribucion-{{ $ciclo->label }}" title="Exportar a Excel">
                  <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </button>
                <button type="button" class="btn btn-outline-primary js-export-csv" data-table-id="students-breakdown-table" data-filename="alumnos-distribucion-{{ $ciclo->label }}" title="Exportar a CSV">
                  <i class="bi bi-file-earmark-text me-1"></i>CSV
                </button>
                <button type="button" class="btn btn-outline-danger js-export-pdf" data-table-id="students-breakdown-table" data-filename="alumnos-distribucion-{{ $ciclo->label }}" title="Imprimir / PDF">
                  <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive" style="min-height: 260px; max-height: 420px;">
          <table id="students-breakdown-table" class="table table-hover table-sm align-middle mb-0 dashboard-data-table">
            <thead class="table-light sticky-top">
              <tr>
                <th scope="col" class="sortable text-center" data-sort="number" style="cursor:pointer; width: 80px;" title="Ordenar por grado">
                  Grado <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable" data-sort="string" style="cursor:pointer;" title="Ordenar por modalidad">
                  Modalidad <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-center" data-sort="string" style="cursor:pointer; width: 100px;" title="Ordenar por sede">
                  Sede <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-end" data-sort="number" style="cursor:pointer; width: 110px;" title="Ordenar por alumnos">
                  Alumnos <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="text-end" style="width: 100px;">% Matrícula</th>
              </tr>
            </thead>
            <tbody>
              @php $totalAlumnosCalc = max(1, $kpis['alumnos'] ?? 1); @endphp
              @forelse($dashboardSummary['alumnosPorGrupo'] as $row)
                @php $pct = round(($row->alumnos / $totalAlumnosCalc) * 100, 1); @endphp
                <tr>
                  <td class="text-center font-monospace fw-semibold">{{ $row->grado }}°</td>
                  <td>
                    <span class="badge bg-light text-secondary border font-monospace">{{ $row->tipo_grupo ?: 'TR' }}</span>
                    <span class="small text-secondary ms-1">
                      {{ $row->tipo_grupo === 'TR' ? 'Tradicional' : ($row->tipo_grupo ?: 'Estándar') }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-surface-2 text-secondary border">Sede {{ $row->id_campus ?: '1' }}</span>
                  </td>
                  <td class="text-end fw-semibold mono" style="font-family:'JetBrains Mono',monospace">
                    {{ number_format($row->alumnos) }}
                  </td>
                  <td class="text-end">
                    <div class="d-flex align-items-center justify-content-end gap-1">
                      <div class="progress flex-grow-1" style="height: 6px; max-width: 45px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $pct * 3) }}%"></div>
                      </div>
                      <span class="small text-muted mono" style="font-size: 11px;">{{ $pct }}%</span>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-secondary">
                    <i class="bi bi-people fs-3 d-block mb-2 text-muted"></i>
                    Sin alumnos inscritos registrados en este ciclo
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer bg-surface-1 border-top py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="small text-secondary js-table-info" data-table-id="students-breakdown-table">
            Mostrando 1 a 20 de {{ number_format($dashboardSummary['alumnosPorGrupo']->count()) }} grupos
          </div>
          <div class="js-table-pagination" data-table-id="students-breakdown-table"></div>
        </div>
      </div>
    </div>

    {{-- Tabla 2: Oferta de Cursos por Sede y Catálogos --}}
    <div class="col-12 col-lg-5">
      <div class="card h-100 shadow-sm border dashboard-table-card">
        <div class="card-header bg-surface-1 border-bottom p-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <h3 class="h6 fw-bold mb-0">Oferta Académica por Sede</h3>
              <span class="small text-secondary">Distribución de cursos y planes</span>
            </div>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-outline-success js-export-excel" data-table-id="courses-campus-table" data-filename="oferta-sedes-{{ $ciclo->label }}" title="Exportar a Excel">
                <i class="bi bi-file-earmark-excel"></i>
              </button>
              <button type="button" class="btn btn-outline-primary js-export-csv" data-table-id="courses-campus-table" data-filename="oferta-sedes-{{ $ciclo->label }}" title="Exportar a CSV">
                <i class="bi bi-file-earmark-text"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table id="courses-campus-table" class="table table-hover table-sm align-middle mb-0 dashboard-data-table">
            <thead class="table-light">
              <tr>
                <th scope="col">Sede / Campus</th>
                <th scope="col" class="text-end">Cursos</th>
                <th scope="col" class="text-end">Planes</th>
                <th scope="col" class="text-end">Materias</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dashboardSummary['cursosPorSede'] as $row)
                <tr>
                  <td>
                    <span class="badge bg-surface-2 text-secondary border me-1">ID {{ $row->id_campus ?: '1' }}</span>
                    <span class="fw-semibold small">Campus Principal {{ $row->id_campus ?: '1' }}</span>
                  </td>
                  <td class="text-end fw-semibold mono" style="font-family:'JetBrains Mono',monospace">{{ number_format($row->cursos) }}</td>
                  <td class="text-end mono" style="font-family:'JetBrains Mono',monospace">{{ number_format($row->planes) }}</td>
                  <td class="text-end mono" style="font-family:'JetBrains Mono',monospace">{{ number_format($row->materias) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-secondary">Sin cursos registrados por sede</td>
                </tr>
              @endforelse
            </tbody>
            @if($dashboardSummary['cursosPorSede']->isNotEmpty())
              <tfoot class="table-light border-top">
                <tr class="fw-bold">
                  <td>Total</td>
                  <td class="text-end mono" style="font-family:'JetBrains Mono',monospace">{{ number_format($dashboardSummary['cursosPorSede']->sum('cursos')) }}</td>
                  <td class="text-end mono" style="font-family:'JetBrains Mono',monospace">{{ number_format($dashboardSummary['cursosPorSede']->sum('planes')) }}</td>
                  <td class="text-end mono" style="font-family:'JetBrains Mono',monospace">{{ number_format($dashboardSummary['cursosPorSede']->sum('materias')) }}</td>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>

        {{-- Resumen de Catálogos (Niveles y Turnos) --}}
        <div class="card-footer bg-surface-1 border-top p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small fw-semibold text-secondary">Catálogos Operativos del Ciclo</span>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <div class="p-2 border rounded bg-surface-2">
                <div class="small text-muted" style="font-size:11px;">Niveles Educativos:</div>
                <div class="fw-bold fs-6 mono text-primary" style="font-family:'JetBrains Mono',monospace">
                  {{ number_format($dashboardSummary['niveles']->count()) }} programas
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="p-2 border rounded bg-surface-2">
                <div class="small text-muted" style="font-size:11px;">Turnos Activos:</div>
                <div class="fw-bold fs-6 mono text-success" style="font-family:'JetBrains Mono',monospace">
                  {{ number_format($dashboardSummary['turnos']->count()) }} turnos
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Tabla 3: Cursos y Programación por Profesor (Ancho Completo) --}}
    <div class="col-12">
      <div class="card shadow-sm border dashboard-table-card">
        <div class="card-header bg-surface-1 border-bottom p-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div>
              <h3 class="h6 fw-bold mb-0">Cursos y Asignaciones por Profesor</h3>
              <span class="small text-secondary">Detalle de oferta académica y sesiones asignadas</span>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 js-table-count-badge">
              {{ number_format($dashboardSummary['cursosPorOrigen']->count()) }} asignaciones
            </span>
          </div>

          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
            <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 200px;">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-surface-2 border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control form-control-sm border-start-0 js-table-search" data-table-id="courses-origin-table" placeholder="Buscar curso, profesor o sede..." aria-label="Buscar curso o profesor">
              </div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <select class="form-select form-select-sm js-table-size" data-table-id="courses-origin-table" style="width: 70px;" aria-label="Cantidad de filas">
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>

              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success js-export-excel" data-table-id="courses-origin-table" data-filename="cursos-profesores-{{ $ciclo->label }}" title="Exportar a Excel">
                  <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </button>
                <button type="button" class="btn btn-outline-primary js-export-csv" data-table-id="courses-origin-table" data-filename="cursos-profesores-{{ $ciclo->label }}" title="Exportar a CSV">
                  <i class="bi bi-file-earmark-text me-1"></i>CSV
                </button>
                <button type="button" class="btn btn-outline-danger js-export-pdf" data-table-id="courses-origin-table" data-filename="cursos-profesores-{{ $ciclo->label }}" title="Imprimir / PDF">
                  <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive" style="min-height: 260px; max-height: 440px;">
          <table id="courses-origin-table" class="table table-hover table-sm align-middle mb-0 dashboard-data-table">
            <thead class="table-light sticky-top">
              <tr>
                <th scope="col" class="sortable text-nowrap" data-sort="string" style="cursor:pointer; width: 180px;" title="Ordenar por clave de curso">
                  Clave de Curso <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable" data-sort="string" style="cursor:pointer;" title="Ordenar por profesor/asignación">
                  Profesor / Asignación <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-center" data-sort="string" style="cursor:pointer; width: 100px;" title="Ordenar por tipo de contrato">
                  Tipo <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-center" data-sort="string" style="cursor:pointer; width: 110px;" title="Ordenar por sede">
                  Sede <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
                <th scope="col" class="sortable text-end" data-sort="number" style="cursor:pointer; width: 100px;" title="Ordenar por sesiones">
                  Sesiones <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size:10px;"></i>
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse($dashboardSummary['cursosPorOrigen'] as $row)
                @php 
                  $originLabel = match($row->origen) { 'CA' => 'PA', 'HD' => 'PTC', default => $row->origen };
                  $originBadge = match($originLabel) {
                    'PTC' => 'bg-success-subtle text-success border border-success-subtle',
                    'PA' => 'bg-info-subtle text-info border border-info-subtle',
                    default => 'bg-secondary-subtle text-secondary border'
                  };
                @endphp
                <tr>
                  <td>
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">
                      {{ $row->clave_curso }}
                    </span>
                  </td>
                  <td>
                    <div class="fw-semibold text-truncate" style="max-width: 320px;" title="{{ $row->nombre_curso ?: 'Sin profesor asignado' }}">
                      {{ $row->nombre_curso ?: 'Sin profesor asignado' }}
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $originBadge }}">
                      {{ $originLabel }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-surface-2 text-secondary border">Sede {{ $row->id_campus ?: '1' }}</span>
                  </td>
                  <td class="text-end fw-semibold mono" style="font-family:'JetBrains Mono',monospace">
                    {{ number_format($row->sesiones) }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-secondary">
                    <i class="bi bi-book fs-3 d-block mb-2 text-muted"></i>
                    Sin cursos registrados en este ciclo
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer bg-surface-1 border-top py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="small text-secondary js-table-info" data-table-id="courses-origin-table">
            Mostrando 1 a 20 de {{ number_format($dashboardSummary['cursosPorOrigen']->count()) }} asignaciones
          </div>
          <div class="js-table-pagination" data-table-id="courses-origin-table"></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ========== PARTIALS DE RETENCIÓN DE CONTRATO (Invisibles para mantener compatibilidad) ========== --}}
<div class="visually-hidden" aria-hidden="true">
  @include('academia.dashboard._materias-module')
  @include('academia.dashboard._planes-module')
</div>

@endsection

{{-- ========== ESTILOS PROFESIONALES ========== --}}
@push('styles')
<style>
  /* Header Card Styling */
  .dashboard-header-card {
    border-left: 5px solid var(--primary) !important;
    background: var(--surface-1);
  }

  /* Table Design System */
  .dashboard-table-card {
    background: var(--surface-1);
    border-radius: var(--radius-md, 8px);
    overflow: hidden;
  }
  .dashboard-table-card .card-header {
    background: var(--surface-1);
  }
  .dashboard-data-table {
    font-size: 13px;
  }
  .dashboard-data-table th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    background-color: var(--surface-2) !important;
    border-bottom: 2px solid var(--border);
    padding: 8px 12px;
  }
  .dashboard-data-table td {
    padding: 8px 12px;
    color: var(--text);
    border-bottom: 1px solid var(--border);
  }
  .dashboard-data-table tbody tr:hover {
    background-color: color-mix(in srgb, var(--primary) 4%, transparent) !important;
  }
  .dashboard-data-table th.sortable:hover {
    color: var(--primary);
  }
  .dashboard-data-table th.sorted-asc,
  .dashboard-data-table th.sorted-desc {
    color: var(--primary);
    background-color: color-mix(in srgb, var(--primary) 8%, var(--surface-2)) !important;
  }

  /* KPI Cards */
  .kpi-card {
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.06) !important;
  }
  html[data-theme="dark"] .kpi-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.3) !important;
  }

  /* Flash animation on KPI AJAX update */
  .kpi-flash {
    animation: kpiFlashAnim .8s ease-out 1;
  }
  @keyframes kpiFlashAnim {
    0% { transform: scale(1.1); color: var(--primary); }
    100% { transform: scale(1); }
  }

  /* Interactive SVG Chart */
  .trend-chart .area-dot {
    transition: r .2s, fill .2s;
    cursor: pointer;
  }
  .trend-chart .area-dot:hover {
    r: 7;
    fill: var(--primary);
  }

  /* Quick links hover */
  .hover-primary:hover {
    color: var(--primary) !important;
  }

  /* Print Stylesheet for PDF Export */
  @media print {
    body * { visibility: hidden; }
    .print-active-area, .print-active-area * { visibility: visible; }
    .print-active-area { position: absolute; left: 0; top: 0; width: 100%; }
    .btn, .input-group, .form-select, .js-table-pagination { display: none !important; }
  }
</style>
@endpush

{{-- ========== MOTOR JAVASCRIPT DE TABLAS Y AJAX ========== --}}
@push('scripts')
<script>
(function() {
  'use strict';

  /**
   * AcademiaTableEngine
   * Motor de gestión de tablas: Búsqueda dinámica, Ordenamiento, Paginación y Exportaciones (Excel, CSV, PDF)
   */
  function AcademiaTableEngine(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.tBodies[0];
    if (!tbody) return;

    const originalRows = Array.from(tbody.querySelectorAll('tr:not(.empty-row)'));
    if (originalRows.length === 0) return;

    const searchInput = document.querySelector(`.js-table-search[data-table-id="${tableId}"]`);
    const sizeSelect = document.querySelector(`.js-table-size[data-table-id="${tableId}"]`);
    const paginationContainer = document.querySelector(`.js-table-pagination[data-table-id="${tableId}"]`);
    const infoContainer = document.querySelector(`.js-table-info[data-table-id="${tableId}"]`);
    const countBadge = document.querySelector(`.dashboard-table-card:has(#${tableId}) .js-table-count-badge`);

    let filteredRows = [...originalRows];
    let currentPage = 1;
    let pageSize = sizeSelect ? parseInt(sizeSelect.value, 10) || 20 : 20;
    let sortColumnIndex = -1;
    let sortDirection = 1; // 1 = asc, -1 = desc

    function render() {
      const totalFiltered = filteredRows.length;
      const totalPages = Math.ceil(totalFiltered / pageSize) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      const startIndex = (currentPage - 1) * pageSize;
      const endIndex = Math.min(startIndex + pageSize, totalFiltered);

      // Limpiar tbody
      tbody.innerHTML = '';

      if (totalFiltered === 0) {
        const colCount = table.querySelectorAll('thead th').length || 5;
        const emptyRow = document.createElement('tr');
        emptyRow.className = 'empty-search-row';
        emptyRow.innerHTML = `<td colspan="${colCount}" class="text-center py-4 text-secondary">
          <i class="bi bi-search fs-4 d-block mb-2 text-muted"></i>
          <div>No se encontraron registros que coincidan con la búsqueda.</div>
          <button type="button" class="btn btn-sm btn-outline-secondary mt-2 js-clear-search">Limpiar filtro</button>
        </td>`;
        emptyRow.querySelector('.js-clear-search').onclick = function() {
          if (searchInput) { searchInput.value = ''; searchInput.dispatchEvent(new Event('input')); }
        };
        tbody.appendChild(emptyRow);
      } else {
        for (let i = startIndex; i < endIndex; i++) {
          tbody.appendChild(filteredRows[i]);
        }
      }

      // Actualizar info
      if (infoContainer) {
        if (totalFiltered === 0) {
          infoContainer.textContent = '0 registros encontrados';
        } else {
          infoContainer.textContent = `Mostrando ${startIndex + 1} a ${endIndex} de ${totalFiltered} registros` + 
            (totalFiltered !== originalRows.length ? ` (filtrados de ${originalRows.length})` : '');
        }
      }

      // Actualizar badge
      if (countBadge) {
        countBadge.textContent = `${totalFiltered} registros`;
      }

      // Renderizar paginador
      renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
      if (!paginationContainer) return;
      if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
      }

      let html = '<div class="btn-group btn-group-sm" role="navigation" aria-label="Paginación">';
      
      // Botón anterior
      html += `<button type="button" class="btn btn-outline-secondary ${currentPage === 1 ? 'disabled' : ''}" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''} aria-label="Página anterior">
        <i class="bi bi-chevron-left"></i>
      </button>`;

      // Botones numéricos (máximo 5 botones)
      const maxButtons = 5;
      let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
      let endPage = Math.min(totalPages, startPage + maxButtons - 1);
      if (endPage - startPage + 1 < maxButtons) {
        startPage = Math.max(1, endPage - maxButtons + 1);
      }

      if (startPage > 1) {
        html += `<button type="button" class="btn btn-outline-secondary" data-page="1">1</button>`;
        if (startPage > 2) html += `<span class="btn btn-outline-secondary disabled">…</span>`;
      }

      for (let p = startPage; p <= endPage; p++) {
        html += `<button type="button" class="btn ${p === currentPage ? 'btn-primary active' : 'btn-outline-secondary'}" data-page="${p}">${p}</button>`;
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) html += `<span class="btn btn-outline-secondary disabled">…</span>`;
        html += `<button type="button" class="btn btn-outline-secondary" data-page="${totalPages}">${totalPages}</button>`;
      }

      // Botón siguiente
      html += `<button type="button" class="btn btn-outline-secondary ${currentPage === totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''} aria-label="Página siguiente">
        <i class="bi bi-chevron-right"></i>
      </button>`;

      html += '</div>';
      paginationContainer.innerHTML = html;

      paginationContainer.querySelectorAll('button[data-page]').forEach(function(btn) {
        btn.onclick = function() {
          const target = parseInt(btn.getAttribute('data-page'), 10);
          if (target >= 1 && target <= totalPages && target !== currentPage) {
            currentPage = target;
            render();
          }
        };
      });
    }

    // Evento de búsqueda instantánea
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase().trim();
        if (!query) {
          filteredRows = [...originalRows];
        } else {
          filteredRows = originalRows.filter(function(row) {
            return row.innerText.toLowerCase().includes(query);
          });
        }
        currentPage = 1;
        render();
      });
    }

    // Selector de tamaño de página
    if (sizeSelect) {
      sizeSelect.addEventListener('change', function() {
        pageSize = parseInt(sizeSelect.value, 10) || 20;
        currentPage = 1;
        render();
      });
    }

    // Ordenamiento por encabezados
    table.querySelectorAll('thead th.sortable').forEach(function(th, index) {
      th.addEventListener('click', function() {
        const type = th.getAttribute('data-sort') || 'string';
        if (sortColumnIndex === index) {
          sortDirection = -sortDirection;
        } else {
          sortColumnIndex = index;
          sortDirection = 1;
        }

        // Limpiar clases de sort
        table.querySelectorAll('thead th.sortable').forEach(function(header) {
          header.classList.remove('sorted-asc', 'sorted-desc');
          const icon = header.querySelector('i');
          if (icon) icon.className = 'bi bi-arrow-down-up text-muted ms-1';
        });

        th.classList.add(sortDirection === 1 ? 'sorted-asc' : 'sorted-desc');
        const icon = th.querySelector('i');
        if (icon) {
          icon.className = sortDirection === 1 ? 'bi bi-sort-down-alt text-primary ms-1' : 'bi bi-sort-up text-primary ms-1';
        }

        filteredRows.sort(function(a, b) {
          const aText = (a.cells[index]?.innerText || '').trim();
          const bText = (b.cells[index]?.innerText || '').trim();

          if (type === 'number') {
            const aNum = parseFloat(aText.replace(/[^0-9.-]+/g, '')) || 0;
            const bNum = parseFloat(bText.replace(/[^0-9.-]+/g, '')) || 0;
            return (aNum - bNum) * sortDirection;
          } else {
            return aText.localeCompare(bText, 'es', { numeric: true }) * sortDirection;
          }
        });

        currentPage = 1;
        render();
      });
    });

    // Inicializar render
    render();
  }

  // Inicializar todos los motores de tabla en la página
  document.addEventListener('DOMContentLoaded', function() {
    const tableIds = ['materias-dashboard-table', 'planes-dashboard-table', 'students-breakdown-table', 'courses-origin-table'];
    tableIds.forEach(function(id) {
      AcademiaTableEngine(id);
    });

    // ── Exportación a CSV con UTF-8 BOM (Soporta acentos para Excel) ──
    document.querySelectorAll('.js-export-csv').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const tableId = btn.getAttribute('data-table-id');
        const table = document.getElementById(tableId);
        if (!table) return;

        const filename = (btn.getAttribute('data-filename') || tableId) + '.csv';
        let csvContent = '\uFEFF'; // BOM para que Excel respete acentos y caracteres UTF-8

        // Encabezados
        const headers = Array.from(table.querySelectorAll('thead tr th'))
          .filter(th => !th.classList.contains('text-end') || th.innerText.toLowerCase() !== 'acción')
          .map(th => `"${th.innerText.replace(/"/g, '""').replace(/\s+/g, ' ').trim()}"`);
        csvContent += headers.join(',') + '\r\n';

        // Filas
        Array.from(table.querySelectorAll('tbody tr:not(.empty-search-row)')).forEach(function(row) {
          const cells = Array.from(row.cells)
            .filter((cell, idx) => headers[idx] !== undefined)
            .map(cell => `"${cell.innerText.replace(/"/g, '""').replace(/\s+/g, ' ').trim()}"`);
          csvContent += cells.join(',') + '\r\n';
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
      });
    });

    // ── Exportación a Excel (.xls nativo legible) ──
    document.querySelectorAll('.js-export-excel').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const tableId = btn.getAttribute('data-table-id');
        const table = document.getElementById(tableId);
        if (!table) return;

        const filename = (btn.getAttribute('data-filename') || tableId) + '.xls';
        const title = btn.getAttribute('data-filename') || 'Exportación Academia';

        let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"/><title>${title}</title>
        <style>
          table { border-collapse:collapse; width:100%; font-family:sans-serif; }
          th { background-color:#2563eb; color:#ffffff; font-weight:bold; border:1px solid #1d4ed8; padding:8px; }
          td { border:1px solid #e2e8f0; padding:6px; font-size:12px; }
          .mono { font-family:monospace; }
        </style></head><body>
        <h3>${title}</h3>
        <table border="1">`;

        // Thead
        html += '<thead><tr>';
        table.querySelectorAll('thead tr th').forEach(function(th) {
          if (th.innerText.trim().toLowerCase() !== 'acción') {
            html += `<th>${th.innerText.replace(/\s+/g, ' ').trim()}</th>`;
          }
        });
        html += '</tr></thead><tbody>';

        // Tbody
        table.querySelectorAll('tbody tr:not(.empty-search-row)').forEach(function(row) {
          html += '<tr>';
          Array.from(row.cells).forEach(function(cell, idx) {
            html += `<td>${cell.innerText.replace(/\s+/g, ' ').trim()}</td>`;
          });
          html += '</tr>';
        });

        html += '</tbody></table></body></html>';

        const blob = new Blob(['\uFEFF' + html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
      });
    });

    // ── Exportación / Impresión a PDF ──
    document.querySelectorAll('.js-export-pdf').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const tableId = btn.getAttribute('data-table-id');
        const card = document.querySelector(`.dashboard-table-card:has(#${tableId})`);
        if (!card) return;

        const originalTitle = document.title;
        document.title = (btn.getAttribute('data-filename') || 'Reporte-Academia') + ' — ' + new Date().toLocaleDateString();

        card.classList.add('print-active-area');
        window.print();
        card.classList.remove('print-active-area');
        document.title = originalTitle;
      });
    });
  });
})();
</script>
@endpush
