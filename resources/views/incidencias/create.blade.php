@extends('layouts.admin')

@section('title', 'Registrar incidencia')
@section('breadcrumb', 'Operación › Incidencias › Registrar')

@section('content')
<x-page-header title="Registrar incidencia" subtitle="Captura una incidencia para un empleado o profesor." :hide-title="false">
    @slot('actions')
        <a href="{{ route('incidencias.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endslot
</x-page-header>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('incidencias.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="tipo_persona" class="form-label">Tipo de persona</label>
                    <select id="tipo_persona" name="tipo_persona" class="form-select @error('tipo_persona') is-invalid @enderror" required>
                        <option value="empleado" {{ old('tipo_persona', 'empleado') === 'empleado' ? 'selected' : '' }}>Empleado</option>
                        <option value="profesor" {{ old('tipo_persona') === 'profesor' ? 'selected' : '' }}>Profesor</option>
                    </select>
                    @error('tipo_persona') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4" data-person-type="empleado">
                    <label for="empleado_id" class="form-label">Empleado</label>
                    <select id="empleado_id" name="empleado_id" class="form-select @error('empleado_id') is-invalid @enderror">
                        <option value="">Selecciona un empleado</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->id }}"
                                    data-area="{{ $empleado->area?->descripcion ?? 'Sin área' }}"
                                    data-puesto="{{ $empleado->puesto?->descripcion ?? 'Sin puesto' }}"
                                    data-responsable="{{ $empleado->area?->empleadoResponsable?->name ?? 'Sin responsable de área' }}"
                                    data-jefe="{{ $empleado->area?->head?->name ?? 'Sin jefe de área' }}"
                                {{ old('empleado_id') == $empleado->id ? 'selected' : '' }}>
                                {{ $empleado->name }} ({{ $empleado->user_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('empleado_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 d-none" data-person-type="profesor">
                    <label for="profesor_clave" class="form-label">Profesor</label>
                    <select id="profesor_clave" name="profesor_clave" class="form-select @error('profesor_clave') is-invalid @enderror">
                        <option value="">Selecciona un profesor</option>
                        @foreach ($profesores as $profesor)
                            <option value="{{ $profesor->clave_profesor }}"
                                    data-area="{{ $profesor->area?->descripcion ?? 'Sin área' }}"
                                    data-puesto="{{ $profesor->puesto?->descripcion ?? 'Sin puesto' }}"
                                    data-responsable="{{ $profesor->area?->empleadoResponsable?->name ?? 'Sin responsable de área' }}"
                                    data-jefe="{{ $profesor->area?->head?->name ?? $profesor->director?->name ?? 'Sin jefe de área' }}"
                                {{ old('profesor_clave') == $profesor->clave_profesor ? 'selected' : '' }}>
                                {{ trim("{$profesor->paterno} {$profesor->materno} {$profesor->nombre_profesor}") }}
                            </option>
                        @endforeach
                    </select>
                    @error('profesor_clave') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="asunto" class="form-label">Asunto</label>
                    <input type="text" id="asunto" name="asunto" value="{{ old('asunto') }}" class="form-control @error('asunto') is-invalid @enderror" required>
                    @error('asunto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="tipo_justificacion" class="form-label">Tipo de falta</label>
                    <input type="text" id="tipo_justificacion" name="tipo_justificacion" value="{{ old('tipo_justificacion') }}" class="form-control @error('tipo_justificacion') is-invalid @enderror" required>
                    @error('tipo_justificacion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="fecha_falta_programada" class="form-label">Fecha de falta programada</label>
                    <input type="date" id="fecha_falta_programada" name="fecha_falta_programada" value="{{ old('fecha_falta_programada', now()->format('Y-m-d')) }}" class="form-control @error('fecha_falta_programada') is-invalid @enderror" required>
                    @error('fecha_falta_programada') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="tipo_duracion" class="form-label">Duración</label>
                    <select id="tipo_duracion" name="tipo_duracion" class="form-select @error('tipo_duracion') is-invalid @enderror" required>
                        <option value="dia_completo" @selected(old('tipo_duracion', 'dia_completo') === 'dia_completo')>Día completo</option>
                        <option value="horario" @selected(old('tipo_duracion') === 'horario')>Horario</option>
                    </select>
                    @error('tipo_duracion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2" data-duration="horario">
                    <label for="hora_inicio" class="form-label">Desde</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}" class="form-control @error('hora_inicio') is-invalid @enderror">
                    @error('hora_inicio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2" data-duration="horario">
                    <label for="hora_fin" class="form-label">Hasta</label>
                    <input type="time" id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}" class="form-control @error('hora_fin') is-invalid @enderror">
                    @error('hora_fin') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="identificacion_area" class="form-label">Área</label>
                    <input id="identificacion_area" class="form-control bg-body-secondary" value="Selecciona una persona" readonly>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_puesto" class="form-label">Puesto</label>
                    <input id="identificacion_puesto" class="form-control bg-body-secondary" value="Selecciona una persona" readonly>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_responsable" class="form-label">Responsable de área</label>
                    <input id="identificacion_responsable" class="form-control bg-body-secondary" value="Se determina por el área" readonly>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_jefe" class="form-label">Jefe / director</label>
                    <input id="identificacion_jefe" class="form-control bg-body-secondary" value="Se determina por la ruta" readonly>
                </div>

                <div class="col-12">
                    <div class="form-text">El área, puesto, responsable y jefe/director se toman del perfil seleccionado y de la ruta de autorización. No se capturan manualmente.</div>
                </div>

                <div class="col-12">
                    <label for="motivo" class="form-label">Motivo</label>
                    <textarea id="motivo" name="motivo" rows="4" class="form-control @error('motivo') is-invalid @enderror" required>{{ old('motivo') }}</textarea>
                    @error('motivo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="comentarios" class="form-label">Comentarios</label>
                    <textarea id="comentarios" name="comentarios" rows="3" class="form-control @error('comentarios') is-invalid @enderror">{{ old('comentarios') }}</textarea>
                    @error('comentarios') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="solicitud" class="form-label">Solicitud</label>
                    <textarea id="solicitud" name="solicitud" rows="3" class="form-control @error('solicitud') is-invalid @enderror">{{ old('solicitud') }}</textarea>
                    @error('solicitud') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar incidencia</button>
            </div>
        </form>
    </div>
</div>

<script>
    const tipoPersona = document.getElementById('tipo_persona');
    const tipoPersonaBlocks = document.querySelectorAll('[data-person-type]');
    const tipoDuracion = document.getElementById('tipo_duracion');
    const durationBlocks = document.querySelectorAll('[data-duration]');
    const identificationFields = {
        area: document.getElementById('identificacion_area'),
        puesto: document.getElementById('identificacion_puesto'),
        responsable: document.getElementById('identificacion_responsable'),
        jefe: document.getElementById('identificacion_jefe'),
    };

    function syncTipoPersona() {
        const value = tipoPersona.value;
        tipoPersonaBlocks.forEach((element) => {
            const show = element.dataset.personType === value;
            element.classList.toggle('d-none', !show);
            const field = element.querySelector('select');
            if (field) {
                field.disabled = !show;
                field.required = show;
            }
        });
    }

    tipoPersona.addEventListener('change', syncTipoPersona);
    syncTipoPersona();

    function syncIdentification() {
        const select = document.querySelector(`[data-person-type="${tipoPersona.value}"] select`);
        const option = select?.selectedOptions[0];
        identificationFields.area.value = option?.dataset.area || 'Selecciona una persona';
        identificationFields.puesto.value = option?.dataset.puesto || 'Selecciona una persona';
        identificationFields.responsable.value = option?.dataset.responsable || 'Se determina por el área';
        identificationFields.jefe.value = option?.dataset.jefe || 'Se determina por la ruta';
    }

    document.querySelectorAll('[data-person-type] select').forEach((select) => select.addEventListener('change', syncIdentification));
    syncIdentification();

    function syncDuration() {
        const isSchedule = tipoDuracion.value === 'horario';
        durationBlocks.forEach((element) => {
            element.classList.toggle('d-none', !isSchedule);
            const field = element.querySelector('input');
            if (field) field.required = isSchedule;
        });
    }

    tipoDuracion.addEventListener('change', syncDuration);
    syncDuration();
</script>
@endsection
