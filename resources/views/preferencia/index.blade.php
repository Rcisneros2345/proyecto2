@extends('layouts.admin')

@section('title', 'Usuarios con preferencia')
@section('breadcrumb', 'Administración › Usuarios')

@section('content')
<div class="row g-4">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Usuarios del sistema</h5>
                    <p class="text-muted small mb-0">Administra credenciales, perfiles y grupos de seguridad.</p>
                </div>
                <a href="{{ route('preferencia.usuarios.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Crear usuario
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="q" class="visually-hidden">Buscar usuario</label>
                        <input id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre, usuario o correo">
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="visually-hidden">Tipo</label>
                        <select id="type" name="type" class="form-select">
                            <option value="">Todos los perfiles</option>
                            <option value="employee" @selected($type === 'employee')>Empleados</option>
                            <option value="professor" @selected($type === 'professor')>Profesores</option>
                            <option value="unassigned" @selected($type === 'unassigned')>Sin perfil</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-outline-primary flex-grow-1"><i class="bi bi-search me-1"></i>Buscar</button>
                        <a href="{{ route('preferencia.usuarios.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    </div>
                </form>
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Correo</th>
                                    <th>Tipo</th>
                                    <th>Perfil y grupos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td><code>{{ $user->username ?: 'Sin username' }}</code></td>
                                    <td>{{ e($user->email) }}</td>
                                    <td>
                                        @if($user->employee)
                                            <span class="badge bg-primary">Empleado</span>
                                        @elseif($user->professor)
                                            <span class="badge bg-secondary">Profesor</span>
                                        @else
                                            <span class="badge bg-secondary">Sin perfil</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->employee)
                                            <div>{{ $user->employee->numero_empleado ?: $user->employee->user_id ?: 'Sin número' }}</div>
                                            <small class="text-muted">{{ $user->employee->permissionGroups->pluck('name')->join(', ') ?: 'Sin grupos' }}</small>
                                        @elseif($user->professor)
                                            <div>{{ $user->professor->clave_profesor }}</div>
                                            <small class="text-muted">{{ $user->professor->permissionGroups->pluck('name')->join(', ') ?: 'Sin grupos' }}</small>
                                        @else
                                            <span class="text-muted">Sin perfil asignado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('preferencia.usuarios.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar usuario">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($user->employee)
                                            <a href="{{ route('employees.edit', $user->employee) }}" class="btn btn-sm btn-outline-secondary" title="Ver empleado">
                                                <i class="bi bi-person-badge"></i>
                                            </a>
                                        @elseif($user->professor)
                                            <a href="{{ route('academia.profesores.show', $user->professor) }}" class="btn btn-sm btn-outline-secondary" title="Ver profesor">
                                                <i class="bi bi-mortarboard"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No hay usuarios registrados con preferencia.
                        <br><small>Crea tu primer usuario usando el botón de arriba.</small>
                    </div>
                @endif
                <div class="mt-3">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection