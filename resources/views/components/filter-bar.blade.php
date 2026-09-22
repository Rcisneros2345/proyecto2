@props([
    'action' => null,
    'method' => 'GET',
    'clearUrl' => null,
    'clearLabel' => 'Limpiar',
    'submitLabel' => 'Filtrar',
    'showSubmit' => true,
])

<form method="{{ $method }}" action="{{ $action }}" class="row g-3 align-items-end">
    {{ $slot }}

    @if ($showSubmit)
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
                {{ $submitLabel }}
            </button>
        </div>
    @endif

    @if ($clearUrl)
        <div class="col-auto">
            <a href="{{ $clearUrl }}" class="btn btn-outline-secondary">
                {{ $clearLabel }}
            </a>
        </div>
    @endif
</form>
