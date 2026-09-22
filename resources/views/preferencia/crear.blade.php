@extends('layouts.admin')

@section('title', 'Crear usuario con preferencia')
@section('breadcrumb', 'Administración › Usuarios › Crear usuario')

@section('content')
<x-page-header title="Crear usuario con preferencia" subtitle="Registra un nuevo usuario y define si será Empleado (biométrico) o Profesor (académico)." :hide-title="false">
    @slot('actions')
        <a href="{{ route('preferencia.usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
    @endslot
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Datos del usuario</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('preferencia.usuarios.store') }}" method="POST" novalidate>
                    @csrf

                    {{-- Tipo de preferencia (radio group) --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tipo de usuario <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="preference_type" id="type_employee" value="employee"
                                        @checked(old('preference_type', 'employee') === 'employee')
                                        autocomplete="off">
                                    <label class="form-check-label d-flex flex-column p-3 border rounded h-100 cursor-pointer
                                        @if(old('preference_type', 'employee') === 'employee') border-primary bg-primary-soft @else border-secondary-subtle @endif"
                                        for="type_employee">
                                        <i class="bi bi-people fs-3 text-primary mb-2"></i>
                                        <strong>Empleado (Biométrico)</strong>
                                        <small class="text-secondary">Acceso a checadores ZKTeco, control de asistencia, huellas, tarjetas RFID y PIN.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="preference_type" id="type_professor" value="professor"
                                        @checked(old('preference_type') === 'professor')
                                        autocomplete="off">
                                    <label class="form-check-label d-flex flex-column p-3 border rounded h-100 cursor-pointer
                                        @if(old('preference_type') === 'professor') border-primary bg-primary-soft @else border-secondary-subtle @endif"
                                        for="type_professor">
                                        <i class="bi bi-mortarboard fs-3 text-primary mb-2"></i>
                                        <strong>Profesor (Académico)</strong>
                                        <small class="text-secondary">Acceso a módulo Academia: horarios, grupos, alumnos, kardex, planes de estudio.</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('preference_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2">Selecciona el tipo de usuario. Esto determina a qué módulos tendrá acceso y qué perfil se creará automáticamente.</div>
                    </div>

                    <hr class="my-4">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            maxlength="100"
                            required
                            autocomplete="name"
                            placeholder="Ej. Juan Pérez López">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario de acceso <span class="text-muted">(opcional)</span></label>
                        <input type="text" id="username" name="username"
                            class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" maxlength="100" autocomplete="username"
                            placeholder="Se genera automáticamente con el nombre y apellido">
                        <div class="form-text">Si lo dejas vacío, se genera un usuario único usando los datos capturados.</div>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            maxlength="150"
                            required
                            autocomplete="email"
                            placeholder="ejemplo@institucion.edu.mx">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Debe ser único en el sistema. Se usará para login y notificaciones.</div>
                    </div>

                    {{-- Contraseña (opcional si se usa SSO/LDAP) --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-muted">(opcional si usa SSO/LDAP)</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                value="{{ old('password') }}"
                                maxlength="72"
                                autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres"
                                data-toggle="password">
                            <button class="btn btn-outline-secondary" type="button" data-toggle-target="#password" aria-label="Mostrar/ocultar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">
                            Si se deja vacía, el usuario deberá autenticarse vía SSO/LDAP (si está configurado).
                            Mínimo 8 caracteres. Se recomienda usar frase de paso.
                        </div>
                    </div>

                    {{-- Confirmación de contraseña --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            value="{{ old('password_confirmation') }}"
                            autocomplete="new-password"
                            placeholder="Repite la contraseña">
                        @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Información adicional según tipo --}}
                    <div id="employee-fields" class="@if(old('preference_type', 'employee') !== 'employee') d-none @endif">
                        <h6 class="mb-3 text-primary"><i class="bi bi-gear me-1"></i> Datos de Empleado (Biométrico)</h6>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_id" class="form-label">ID de checador (Badge/PIN) <span class="text-danger">*</span></label>
                                <input type="text" id="user_id" name="user_id"
                                    class="form-control @error('user_id') is-invalid @enderror"
                                    value="{{ old('user_id') }}"
                                    maxlength="9"
                                    inputmode="numeric"
                                    pattern="[0-9]{1,9}"
                                    placeholder="Ej. 2089"
                                    @if(old('preference_type', 'employee') === 'employee') required @endif>
                                @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Solo números, máximo 9 dígitos. Identificador único en dispositivos ZKTeco.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="employee_type" class="form-label">Tipo de empleado</label>
                                <select id="employee_type" name="employee_type" class="form-select @error('employee_type') is-invalid @enderror">
                                    <option value="biometric" @selected(old('employee_type', 'biometric') === 'biometric')>Biométrico (ZKTeco)</option>
                                    <option value="admin" @selected(old('employee_type') === 'admin')>Administrativo</option>
                                </select>
                                @error('employee_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="area_id" class="form-label">Área</label>
                                <select id="area_id" name="area_id" class="form-select @error('area_id') is-invalid @enderror">
                                    <option value="">Sin área asignada</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>
                                            {{ $area->identificador }} - {{ $area->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="puesto_id" class="form-label">Puesto</label>
                                <select id="puesto_id" name="puesto_id" class="form-select @error('puesto_id') is-invalid @enderror">
                                    <option value="">Sin puesto asignado</option>
                                    @foreach ($puestos as $puesto)
                                        <option value="{{ $puesto->id }}" @selected(old('puesto_id') == $puesto->id)>
                                            {{ $puesto->identificador }} - {{ $puesto->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('puesto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="card_number" class="form-label">Código de tarjeta RFID</label>
                                <input type="text" id="card_number" name="card_number"
                                    class="form-control @error('card_number') is-invalid @enderror"
                                    value="{{ old('card_number') }}"
                                    maxlength="20"
                                    inputmode="numeric"
                                    placeholder="Opcional">
                                @error('card_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Número de tarjeta de proximidad. Debe ser único por institución.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="device_pin" class="form-label">PIN del dispositivo</label>
                                <input type="text" id="device_pin" name="device_pin"
                                    class="form-control @error('device_pin') is-invalid @enderror"
                                    value="{{ old('device_pin') }}"
                                    maxlength="8"
                                    inputmode="numeric"
                                    pattern="[0-9]{1,8}"
                                    placeholder="Opcional, ej. 1234">
                                @error('device_pin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">PIN numérico corto configurado directamente en el checador (no es la contraseña del sistema).</div>
                            </div>
                        </div>
                    </div>

                    <div id="professor-fields" class="@if(old('preference_type') !== 'professor') d-none @endif">
                        <h6 class="mb-3 text-primary"><i class="bi bi-gear me-1"></i> Datos de Profesor (Académico)</h6>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="clave_profesor" class="form-label">Clave de profesor <span class="text-danger">*</span></label>
                                <input type="text" id="clave_profesor" name="clave_profesor"
                                    class="form-control @error('clave_profesor') is-invalid @enderror"
                                    value="{{ old('clave_profesor') }}"
                                    maxlength="20"
                                    @if(old('preference_type') === 'professor') required @endif
                                    placeholder="Ej. PROF-2024-001">
                                @error('clave_profesor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Identificador único del profesor en el sistema académico.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="profesor_email" class="form-label">Email institucional del profesor</label>
                                <input type="email" id="profesor_email" name="profesor_email"
                                    class="form-control @error('profesor_email') is-invalid @enderror"
                                    value="{{ old('profesor_email') }}"
                                    maxlength="150"
                                    placeholder="profesor@institucion.edu.mx">
                                @error('profesor_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Puede ser distinto al email de usuario del sistema.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <input type="text" id="departamento" name="departamento"
                                    class="form-control @error('departamento') is-invalid @enderror"
                                    value="{{ old('departamento') }}"
                                    maxlength="100"
                                    placeholder="Ej. Ingeniería en Sistemas">
                                @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="profesor_area_id" class="form-label">Área académica</label>
                                <select id="profesor_area_id" name="profesor_area_id" class="form-select @error('profesor_area_id') is-invalid @enderror">
                                    <option value="">Sin área</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" @selected(old('profesor_area_id') == $area->id)>
                                            {{ $area->identificador }} - {{ $area->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('profesor_area_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="profesor_puesto_id" class="form-label">Puesto académico</label>
                                <select id="profesor_puesto_id" name="profesor_puesto_id" class="form-select @error('profesor_puesto_id') is-invalid @enderror">
                                    <option value="">Sin puesto</option>
                                    @foreach ($puestos as $puesto)
                                        <option value="{{ $puesto->id }}" @selected(old('profesor_puesto_id') == $puesto->id)>
                                            {{ $puesto->identificador }} - {{ $puesto->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('profesor_puesto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="contrato" class="form-label">Tipo de contrato</label>
                                <input type="text" id="contrato" name="contrato"
                                    class="form-control @error('contrato') is-invalid @enderror"
                                    value="{{ old('contrato') }}"
                                    maxlength="50"
                                    placeholder="Ej. PTC, PA, Base">
                                @error('contrato') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

{{-- Selección de empleado/profesor existente --}}
    @if(old('preference_type') === 'employee' || !old('preference_type'))
    <div class="mb-4">
        <label class="form-label fw-semibold">Empleado Existente</label>
        <p class="form-text text-secondary mb-3">Seleccione un empleado que no tenga usuario asignado, o deje vacío para crear uno nuevo.</p>
        <select name="empleado_existente_id" class="form-select @error('empleado_existente_id') is-invalid @enderror">
            <option value="">-- Seleccionar empleado existente --</option>
@foreach($empleadosDisponibles as $emp)
    <option value="{{ $emp->id }}" {{ old('empleado_existente_id') == $emp->id ? 'selected' : '' }}>
        @if($emp->user)
            {{ $emp->user->name }} ({{ $emp->type }})
        @else
            {{ 'Empleado ID: ' . $emp->id . ' - ' . $emp->type }}
        @endif
    </option>
@endforeach
        </select>
        @error('empleado_existente_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endif

    {{-- Selección de profesor existente --}}
    @if(old('preference_type') === 'professor' || !old('preference_type'))
    <div class="mb-4">
        <label class="form-label fw-semibold">Profesor Existente</label>
        <p class="form-text text-secondary mb-3">Seleccione un profesor que no tenga usuario asignado, o deje vacío para crear uno nuevo.</p>
        <select name="profesor_existente_id" class="form-select @error('profesor_existente_id') is-invalid @enderror">
            <option value="">-- Seleccionar profesor existente --</option>
@foreach($profesoresDisponibles as $prof)
    <option value="{{ $prof->clave_profesor }}" {{ old('profesor_existente_id') == $prof->clave_profesor ? 'selected' : '' }}>
        {{ $prof->nombre_completo }} · Clave {{ $prof->clave_profesor }} · {{ $prof->departamento ?: 'Sin departamento' }}
    </option>
@endforeach
        </select>
        @error('profesor_existente_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endif

                    {{-- Acciones --}}
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('preferencia.usuarios.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i> Crear usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Mensaje de éxito (se muestra via session flash) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('[data-toggle-target]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.querySelector(btn.dataset.toggleTarget);
            if (!target) return;
            const type = target.type === 'password' ? 'text' : 'password';
            target.type = type;
            btn.querySelector('i').classList.toggle('bi-eye');
            btn.querySelector('i').classList.toggle('bi-eye-slash');
        });
    });

    // Toggle employee/professor fields based on radio selection
    const typeRadios = document.querySelectorAll('input[name="preference_type"]');
    const employeeFields = document.getElementById('employee-fields');
    const professorFields = document.getElementById('professor-fields');

    function toggleFields() {
        const selected = document.querySelector('input[name="preference_type"]:checked')?.value;
        if (employeeFields) employeeFields.classList.toggle('d-none', selected !== 'employee');
        if (professorFields) professorFields.classList.toggle('d-none', selected !== 'professor');
    }

    typeRadios.forEach(radio => radio.addEventListener('change', toggleFields));
    // Initialize on load
    toggleFields();
</script>
@endpush