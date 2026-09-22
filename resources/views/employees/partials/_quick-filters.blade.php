{{--
    Partial: employees/partials/_quick-filters.blade.php
     Chips de filtros rápidos: Sin huellas, Sin enrolar, Sobrantes.
     Variables: $baseUrl (string, URL base para los filtros)
               $activeParams (array, parámetros de filtro activos actuales)
     El partial se incluye dentro de un <form> o de forma independiente.
--}}
@php
    $baseUrl = $baseUrl ?? route('employees.index');
    $activeParams = $activeParams ?? request()->query();
@endphp
<div class="col-12 d-flex gap-2 flex-wrap pt-2 border-top">
    <span class="small text-tertiary-token me-1">Rápidos:</span>
    <a href="{{ $baseUrl . '?' . http_build_query(array_merge($activeParams, ['sin_huella' => 1])) }}"
       class="ref-chip" aria-label="Filtrar sin huellas">
        <i class="bi bi-fingerprint"></i> Sin huellas
    </a>
    <a href="{{ $baseUrl . '?' . http_build_query(array_merge($activeParams, ['sin_device' => 1])) }}"
       class="ref-chip" aria-label="Filtrar sin enrolar">
        <i class="bi bi-hdd-network"></i> Sin enrolar
    </a>
    <a href="{{ route('employees.sobrantes') }}" class="ref-chip" aria-label="Ver sobrantes">
        <i class="bi bi-exclamation-triangle"></i> Sobrantes
    </a>
</div>
