@extends('layouts.admin')

@section('title', 'Registrar dispositivo')
@section('breadcrumb', 'Operación › Dispositivos › Registrar dispositivo')

@section('content')
<x-page-header title="Registrar dispositivo" subtitle="Agrega un checador ZKTeco a la red biométrica." :hide-title="false">
    @slot('actions')
        <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endslot
</x-page-header>

<div class="card shadow-sm col-lg-6">
    <div class="card-body">
        <form action="{{ route('devices.store') }}" method="POST" novalidate>
            @csrf

            {{-- Section: Identificación --}}
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Identificación del dispositivo</legend>

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                           value="{{ old('name') }}" required aria-required="true"
                           placeholder="Ejemplo: Recepción Planta Baja">
                    @error('name') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                    <div class="form-text">Nombre descriptivo para identificar el checador en la red.</div>
                </div>
            </fieldset>

            {{-- Section: Conexión --}}
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Datos de conexión</legend>

                <div class="mb-3">
                    <label for="ip" class="form-label">Dirección IP <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                    <input type="text" class="form-control @error('ip') is-invalid @enderror" id="ip" name="ip"
                           value="{{ old('ip', env('ZKTECO_DEFAULT_IP')) }}" required aria-required="true"
                           placeholder="192.168.1.x">
                    @error('ip') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                    <div class="form-text">Deja vacío para usar la IP por defecto: {{ env('ZKTECO_DEFAULT_IP') }}</div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="port" class="form-label">Puerto <span class="text-tertiary-token" aria-hidden="true">*</span></label>
                        <input type="number" class="form-control @error('port') is-invalid @enderror" id="port" name="port"
                               value="{{ old('port', env('ZKTECO_DEFAULT_PORT', 4370)) }}" required aria-required="true">
                        @error('port') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label for="password" class="form-label">Contraseña / CLAVE</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                               name="password" value="{{ old('password', '') }}" placeholder="Vacía = 0">
                        @error('password') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                        <div class="form-text">Opcional. Si el checador no tiene clave, deja vacío.</div>
                    </div>
                </div>
            </fieldset>

            {{-- Section: Información adicional --}}
            <fieldset class="mb-4">
                <legend class="fs-6 fw-bold mb-3">Información adicional</legend>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                              name="description" rows="2" placeholder="Ubicación, notas, responsable...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback" role="alert">{{ $message }}</div> @enderror
                </div>
            </fieldset>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Guardar dispositivo
                </button>
                <a href="{{ route('devices.index') }}" class="btn btn-link">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
