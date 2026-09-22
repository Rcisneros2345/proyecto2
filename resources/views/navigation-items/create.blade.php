@extends('layouts.admin')

@section('title', 'Nueva entrada de navegación')
@section('breadcrumb', 'Administración › Navegación › Nueva')

@section('content')
<x-page-header title="Nueva entrada de navegación" subtitle="Define una opción del menú, su ruta y el permiso requerido." :hide-title="false">
    @slot('actions')
        <a href="{{ route('navigation-items.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    @endslot
</x-page-header>

<div class="card"><div class="card-body">
    <form action="{{ route('navigation-items.store') }}" method="POST">
        @csrf
        @include('navigation-items.form')
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('navigation-items.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary" type="submit">Guardar entrada</button>
        </div>
    </form>
</div></div>
@endsection
