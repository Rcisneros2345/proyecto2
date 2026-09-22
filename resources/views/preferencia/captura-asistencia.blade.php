@extends('layouts.admin')

@section('title', 'Asignaciones de captura')
@section('breadcrumb', 'Administración > Usuarios > Captura de asistencia')

@section('content')
<x-page-header title="Asignaciones de captura" subtitle="Define los niveles y sedes donde este usuario puede capturar asistencia de clase.">
    @slot('actions')
        <a href="{{ route('preferencia.usuarios.edit', $user) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al usuario
        </a>
    @endslot
</x-page-header>

<div class="card">
    <div class="card-header">
        <strong>{{ $user->name }}</strong>
        <span class="text-muted"> · {{ $user->email }}</span>
    </div>
    <div class="card-body">
        <p class="text-muted small">Una fila autoriza la combinación indicada. Deja nivel o sede vacío para usarlo como comodín dentro del ciclo seleccionado.</p>
        <form method="POST" action="{{ route('preferencia.usuarios.captura-asistencia.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Sede</th>
                            <th>Ciclo</th>
                            <th>Activa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rows = $assignments->values()->all(); @endphp
                        @php $defaultCycle = $ciclos->first(); @endphp
                        @for ($index = 0; $index < max(count($rows) + 2, 3); $index++)
                            @php $assignment = $rows[$index] ?? null; @endphp
                            @php $cycle = $assignment ?: $defaultCycle; @endphp
                            @php $isActive = $assignment ? (bool) $assignment->active : false; @endphp
                            <tr>
                                <td>
                                    <select name="assignments[{{ $index }}][nivel]" class="form-select">
                                        <option value="">Todos los niveles</option>
                                        @foreach ($niveles as $nivel)
                                            <option value="{{ $nivel->nivel }}" @selected(old("assignments.$index.nivel", $assignment?->nivel) === $nivel->nivel)>{{ $nivel->label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="assignments[{{ $index }}][id_campus]" class="form-select">
                                        <option value="">Todas las sedes</option>
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id_campus }}" @selected((string) old("assignments.$index.id_campus", $assignment?->id_campus) === (string) $sede->id_campus)>{{ $sede->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="assignments[{{ $index }}][ciclo]" class="form-select" data-cycle-select="{{ $index }}">
                                        <option value="">Todos los ciclos</option>
                                        @foreach ($ciclos as $ciclo)
                                            @php $cycleValue = implode('-', [$ciclo->inicial, $ciclo->final, $ciclo->periodo]); @endphp
                                            <option value="{{ $cycleValue }}" @selected($cycle && $cycle->inicial === $ciclo->inicial && $cycle->final === $ciclo->final && $cycle->periodo === $ciclo->periodo)>{{ $ciclo->label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="assignments[{{ $index }}][inicial]" value="{{ $cycle?->inicial }}">
                                    <input type="hidden" name="assignments[{{ $index }}][final]" value="{{ $cycle?->final }}">
                                    <input type="hidden" name="assignments[{{ $index }}][periodo]" value="{{ $cycle?->periodo }}">
                                </td>
                                <td>
                                    <input type="hidden" name="assignments[{{ $index }}][active]" value="0">
                                    <input type="checkbox" name="assignments[{{ $index }}][active]" value="1" class="form-check-input" @checked(old("assignments.$index.active", $isActive))>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('preferencia.usuarios.edit', $user) }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar asignaciones</button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('[data-cycle-select]').forEach((select) => {
    select.addEventListener('change', () => {
        const row = select.closest('tr');
        const values = (select.value || '').split('-');
        ['inicial', 'final', 'periodo'].forEach((field, index) => {
            row.querySelector(`[name$="[${field}]"]`).value = values[index] || '';
        });
    });
});
</script>
@endsection