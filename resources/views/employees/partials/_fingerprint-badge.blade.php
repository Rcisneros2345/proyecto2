{{--
    Partial: employees/partials/_fingerprint-badge.blade.php
     Badge semántico para conteo de huellas con indicador visual de puntos.
     Regla centralizada: 0 → gray, <3 → amber, ≥3 → green.
     Variables: $fpCount (int), $fpMax (int, opcional — total esperado de dedos únicos)
     El formato ●●○ N/M se muestra cuando $fpMax se provee; si no, solo N.
--}}
@php
    $fpCount = $fpCount ?? 0;
    $fpMax = $fpMax ?? null;
    $fpColor = $fpCount === 0 ? 'gray' : ($fpCount < 3 ? 'amber' : 'green');
@endphp
<span class="badge cat-{{ $fpColor }}"
      title="{{ $fpCount }} huella{{ $fpCount !== 1 ? 's' : '' }} guardada{{ $fpCount !== 1 ? 's' : '' }}">
    {{-- Dot indicator --}}
    @if($fpMax !== null && $fpMax > 0)
        <span class="dot-indicator" aria-hidden="true">
            @for($i = 0; $i < min($fpMax, 5); $i++)
                <span class="dot {{ $i < $fpCount ? "filled cat-{$fpColor}" : '' }}"></span>
            @endfor
        </span>
    @else
        <i class="bi bi-fingerprint me-1"></i>
    @endif
    {{ $fpCount }}@if($fpMax !== null)/{{ $fpMax }}@endif
</span>
