@extends('layouts.admin')

@section('title', 'Nuevo permiso')
@section('breadcrumb', 'Administración › Permisos › Nuevo')

@section('content')
<x-page-header title="Nuevo permiso" subtitle="Crea una acción reutilizable por módulo." :hide-title="false">
    @slot('actions')
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">Volver</a>
    @endslot
</x-page-header>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="module_id" class="form-label">Módulo</label>
                    <select id="module_id" name="module_id" class="form-select" required>
                        <option value="">Selecciona un módulo</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="action" class="form-label">Acción</label>
                    <input type="text" id="action" name="action" class="form-control" value="{{ old('action', 'view') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar permiso</button>
            </div>
        </form>
    </div>
</div>
@endsection
