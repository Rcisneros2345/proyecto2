@extends('layouts.admin')

@section('title', 'Editar usuario')
@section('breadcrumb', 'Administracion > Usuarios > Editar')

@section('content')
<x-page-header title="Editar usuario" subtitle="Actualiza la identidad de acceso y los grupos de seguridad del usuario.">
    @slot('actions')
        <a href="{{ route('preferencia.usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endslot
</x-page-header>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header"><h2 class="h6 mb-0"><i class="bi bi-person-gear me-2"></i>Datos de acceso</h2></div>
            <div class="card-body">
                <form action="{{ route('preferencia.usuarios.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">Nombre completo</label>
                            <input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Usuario de login</label>
                            <input id="username" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror" autocomplete="username">
                            <div class="form-text">Puede iniciar sesión con este usuario o con su correo.</div>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rol global</label>
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="operator" @selected(old('role', $user->role?->value ?? $user->role) === 'operator')>Operador</option>
                                <option value="admin" @selected(old('role', $user->role?->value ?? $user->role) === 'admin')>Administrador</option>
                            </select>
                            <div class="form-text">Administrador omite las restricciones de módulos.</div>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Perfil vinculado</label>
                            <div class="form-control bg-body-secondary">
                                @if($user->employee)
                                    Empleado: {{ $user->employee->numero_empleado ?: $user->employee->user_id ?: $user->employee->name }}
                                @elseif($user->professor)
                                    Profesor: {{ $user->professor->clave_profesor }}
                                @else
                                    Sin empleado o profesor vinculado
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                            <div class="form-text">Déjala vacía para conservar la actual.</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('preferencia.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card">
            <div class="card-header"><h2 class="h6 mb-0"><i class="bi bi-shield-lock me-2"></i>Grupos de seguridad</h2></div>
            <div class="card-body">
                <p class="text-muted small">Los permisos se acumulan entre todos los grupos seleccionados. Los cambios se aplican al empleado o profesor vinculado.</p>
                <form action="{{ route('preferencia.usuarios.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="username" value="{{ $user->username }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="role" value="{{ $user->role?->value ?? $user->role }}">
                    @forelse($groups as $group)
                        <label class="d-flex align-items-start gap-2 border rounded p-3 mb-2">
                            <input class="form-check-input mt-1" type="checkbox" name="group_ids[]" value="{{ $group->id }}" @checked(in_array($group->id, old('group_ids', $assignedGroupIds), true))>
                            <span>
                                <strong>{{ $group->name }}</strong>
                                <small class="d-block text-muted">{{ $group->description ?: 'Sin descripción' }}</small>
                            </span>
                        </label>
                    @empty
                        <div class="alert alert-warning">No hay grupos configurados.</div>
                    @endforelse
                    <button class="btn btn-outline-primary w-100 mt-2"><i class="bi bi-shield-check me-1"></i>Guardar grupos</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="h6">Acceso efectivo</h2>
                <p class="text-muted small mb-2">Los módulos se habilitan desde los permisos contenidos en sus grupos.</p>
                <a href="{{ route('permission-groups.index') }}" class="btn btn-sm btn-outline-secondary">Administrar grupos y módulos</a>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="h6">Captura de asistencia de clase</h2>
                <p class="text-muted small mb-2">Define los niveles, sedes y ciclos que este usuario puede capturar.</p>
                <a href="{{ route('preferencia.usuarios.captura-asistencia.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-calendar2-check me-1"></i>Administrar asignaciones
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
