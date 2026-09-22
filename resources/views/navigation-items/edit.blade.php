@extends('layouts.admin')

@section('title', 'Editar entrada de navegación')
@section('breadcrumb', 'Administración › Navegación › Editar')

@section('content')
<x-page-header title="Editar entrada de navegación" subtitle="Actualiza la ruta, sección, orden o permiso de la entrada." :hide-title="false">
    @slot('actions')
        <a href="{{ route('navigation-items.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    @endslot
</x-page-header>

<div class="card"><div class="card-body">
    <form action="{{ route('navigation-items.update', $navigationItem) }}" method="POST">
        @csrf
        @method('PUT')
        @include('navigation-items.form', ['item' => $navigationItem])
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('navigation-items.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
        </div>
    </form>
</div></div>
@endsection
