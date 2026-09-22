@extends('layouts.admin')

@section('title', 'Asignar empleados al grupo')
@section('breadcrumb', 'Administración › Grupos de permisos › Empleados')

@section('content')
<x-page-header title="Asignar empleados" subtitle="Grupo: {{ $permissionGroup->name }}" :hide-title="false">
    @slot('actions')
        <a href="{{ route('permission-groups.index') }}" class="btn btn-outline-secondary">Volver</a>
    @endslot
</x-page-header>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('permission-groups.save-employees', $permissionGroup) }}" method="POST">
            @csrf

            <div class="table-responsive">
                <table class="table table-hover align-middle table-cards">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="select-all-employees" class="form-check-input"></th>
                            <th>Nombre</th>
                            <th>ID</th>
                            <th>Área</th>
                            <th>Puesto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td data-label="">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="form-check-input employee-checkbox" {{ in_array($employee->id, $selectedEmployeeIds, true) ? 'checked' : '' }}>
                                </td>
                                <td data-label="Nombre">{{ $employee->name }}</td>
                                <td data-label="ID">{{ $employee->user_id }}</td>
                                <td data-label="Área">{{ $employee->area?->identificador ?? '—' }}</td>
                                <td data-label="Puesto">{{ $employee->puesto?->identificador ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay empleados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Guardar asignación</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    document.getElementById('select-all-employees')?.addEventListener('change', function () {
        document.querySelectorAll('.employee-checkbox').forEach(function (checkbox) {
            checkbox.checked = this.checked;
        }, this);
    });
</script>
@endsection
@endsection
