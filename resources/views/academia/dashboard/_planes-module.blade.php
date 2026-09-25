@php
    $ciclo = $ciclo ?? app('App\Services\CicloActualService')->resolve(request());
    $summary = app('App\Services\AcademiaDashboardService')->build($ciclo);
    $planesCount = $summary['kpis']['planes'] ?? 0;
@endphp

@if($planesCount > 0)
  <a href="{{ route('academia.planes.index') }}" class="card card-link h-100 p-3 text-decoration-none" style="border-left:3px solid var(--cat-lavender);"
     aria-label="Planes: {{ $planesCount }} registros, Planes del ciclo">
    <div class="d-flex align-items-start gap-3">
      <span class="kpi-icon lavender" style="width:42px;height:42px;flex:0 0 42px"><i class="bi bi-collection"></i></span>
      <div class="flex-grow-1 min-w-0">
        <div class="fw-bold" style="font-size:13px">Planes</div>
        <div class="d-flex align-items-baseline gap-2">
          <span class="fw-bold" style="font-family:'JetBrains Mono',monospace;font-size:22px" data-mod-ciclo="Planes">{{ $planesCount }}</span>
          @if($planesCount !== null)
            <span class="small text-tertiary-token">en este ciclo</span>
          @endif
        </div>
        <div class="small text-secondary-token mt-1">Ver y gestionar planes <i class="bi bi-arrow-right ms-1"></i></div>
      </div>
    </div>
  </a>
@else
  <a href="#" class="card card-link h-100 p-3 text-decoration-none opacity-50" aria-label="Planes: Sin datos">
    <div class="d-flex align-items-start gap-3">
      <span class="kpi-icon" style="width:42px;height:42px;flex:0 0 42px"><i class="bi bi-collection"></i></span>
      <div class="flex-grow-1 min-w-0">
        <div class="fw-bold" style="font-size:13px">Planes</div>
        <div class="small text-tertiary-text">Sin planes en este ciclo</div>
      </div>
    </div>
  </a>
@endif