@extends('layouts.admin')

@section('title', 'Editar puesto')
@section('breadcrumb', 'Catálogos RH › Puestos › Editar')

@section('content')
<x-page-header title="Editar puesto" subtitle="Actualiza la información del puesto." :hide-title="false">
    @slot('actions')
        <a href="{{ route('puestos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    @endslot
</x-page-header>

<div class="card">
    <div class="card-body">
        <form action="{{ route('puestos.update', $puesto) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="identificador">Identificador</label>
                    <input type="text" id="identificador" name="identificador" class="form-control" value="{{ old('identificador', $puesto->identificador) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control" value="{{ old('descripcion', $puesto->descripcion) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="area_id">Área</label>
                    <select id="area_id" name="area_id" class="form-select">
                        <option value="">Sin área</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ old('area_id', $puesto->area_id) == $area->id ? 'selected' : '' }}>
                                {{ $area->identificador }} - {{ $area->descripcion ?? 'Sin descripción' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('puestos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar puesto</button>
            </div>
        </form>
    </div>
</div>
@endsection
