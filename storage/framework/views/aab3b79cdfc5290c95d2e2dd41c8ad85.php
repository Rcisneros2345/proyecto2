<?php $__env->startSection('title', 'Crear usuario con preferencia'); ?>
<?php $__env->startSection('breadcrumb', 'Administración › Usuarios › Crear usuario'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Crear usuario con preferencia','subtitle' => 'Registra un nuevo usuario y define si será Empleado (biométrico) o Profesor (académico).','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Crear usuario con preferencia','subtitle' => 'Registra un nuevo usuario y define si será Empleado (biométrico) o Profesor (académico).','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('preferencia.usuarios.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
    <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Datos del usuario</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('preferencia.usuarios.store')); ?>" method="POST" novalidate>
                    <?php echo csrf_field(); ?>

                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tipo de usuario <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="preference_type" id="type_employee" value="employee"
                                        <?php if(old('preference_type', 'employee') === 'employee'): echo 'checked'; endif; ?>
                                        autocomplete="off">
                                    <label class="form-check-label d-flex flex-column p-3 border rounded h-100 cursor-pointer
                                        <?php if(old('preference_type', 'employee') === 'employee'): ?> border-primary bg-primary-soft <?php else: ?> border-secondary-subtle <?php endif; ?>"
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
                                        <?php if(old('preference_type') === 'professor'): echo 'checked'; endif; ?>
                                        autocomplete="off">
                                    <label class="form-check-label d-flex flex-column p-3 border rounded h-100 cursor-pointer
                                        <?php if(old('preference_type') === 'professor'): ?> border-primary bg-primary-soft <?php else: ?> border-secondary-subtle <?php endif; ?>"
                                        for="type_professor">
                                        <i class="bi bi-mortarboard fs-3 text-primary mb-2"></i>
                                        <strong>Profesor (Académico)</strong>
                                        <small class="text-secondary">Acceso a módulo Academia: horarios, grupos, alumnos, kardex, planes de estudio.</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php $__errorArgs = ['preference_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text mt-2">Selecciona el tipo de usuario. Esto determina a qué módulos tendrá acceso y qué perfil se creará automáticamente.</div>
                    </div>

                    <hr class="my-4">

                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                            class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('name')); ?>"
                            maxlength="100"
                            required
                            autocomplete="name"
                            placeholder="Ej. Juan Pérez López">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario de acceso <span class="text-muted">(opcional)</span></label>
                        <input type="text" id="username" name="username"
                            class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('username')); ?>" maxlength="100" autocomplete="username"
                            placeholder="Se genera automáticamente con el nombre y apellido">
                        <div class="form-text">Si lo dejas vacío, se genera un usuario único usando los datos capturados.</div>
                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email"
                            class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('email')); ?>"
                            maxlength="150"
                            required
                            autocomplete="email"
                            placeholder="ejemplo@institucion.edu.mx">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Debe ser único en el sistema. Se usará para login y notificaciones.</div>
                    </div>

                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-muted">(opcional si usa SSO/LDAP)</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('password')); ?>"
                                maxlength="72"
                                autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres"
                                data-toggle="password">
                            <button class="btn btn-outline-secondary" type="button" data-toggle-target="#password" aria-label="Mostrar/ocultar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">
                            Si se deja vacía, el usuario deberá autenticarse vía SSO/LDAP (si está configurado).
                            Mínimo 8 caracteres. Se recomienda usar frase de paso.
                        </div>
                    </div>

                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('password_confirmation')); ?>"
                            autocomplete="new-password"
                            placeholder="Repite la contraseña">
                        <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div id="employee-fields" class="<?php if(old('preference_type', 'employee') !== 'employee'): ?> d-none <?php endif; ?>">
                        <h6 class="mb-3 text-primary"><i class="bi bi-gear me-1"></i> Datos de Empleado (Biométrico)</h6>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_id" class="form-label">ID de checador (Badge/PIN) <span class="text-danger">*</span></label>
                                <input type="text" id="user_id" name="user_id"
                                    class="form-control <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('user_id')); ?>"
                                    maxlength="9"
                                    inputmode="numeric"
                                    pattern="[0-9]{1,9}"
                                    placeholder="Ej. 2089"
                                    <?php if(old('preference_type', 'employee') === 'employee'): ?> required <?php endif; ?>>
                                <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Solo números, máximo 9 dígitos. Identificador único en dispositivos ZKTeco.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="employee_type" class="form-label">Tipo de empleado</label>
                                <select id="employee_type" name="employee_type" class="form-select <?php $__errorArgs = ['employee_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="biometric" <?php if(old('employee_type', 'biometric') === 'biometric'): echo 'selected'; endif; ?>>Biométrico (ZKTeco)</option>
                                    <option value="admin" <?php if(old('employee_type') === 'admin'): echo 'selected'; endif; ?>>Administrativo</option>
                                </select>
                                <?php $__errorArgs = ['employee_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="area_id" class="form-label">Área</label>
                                <select id="area_id" name="area_id" class="form-select <?php $__errorArgs = ['area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Sin área asignada</option>
                                    <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($area->id); ?>" <?php if(old('area_id') == $area->id): echo 'selected'; endif; ?>>
                                            <?php echo e($area->identificador); ?> - <?php echo e($area->descripcion); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="puesto_id" class="form-label">Puesto</label>
                                <select id="puesto_id" name="puesto_id" class="form-select <?php $__errorArgs = ['puesto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Sin puesto asignado</option>
                                    <?php $__currentLoopData = $puestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($puesto->id); ?>" <?php if(old('puesto_id') == $puesto->id): echo 'selected'; endif; ?>>
                                            <?php echo e($puesto->identificador); ?> - <?php echo e($puesto->descripcion); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['puesto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="card_number" class="form-label">Código de tarjeta RFID</label>
                                <input type="text" id="card_number" name="card_number"
                                    class="form-control <?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('card_number')); ?>"
                                    maxlength="20"
                                    inputmode="numeric"
                                    placeholder="Opcional">
                                <?php $__errorArgs = ['card_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Número de tarjeta de proximidad. Debe ser único por institución.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="device_pin" class="form-label">PIN del dispositivo</label>
                                <input type="text" id="device_pin" name="device_pin"
                                    class="form-control <?php $__errorArgs = ['device_pin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('device_pin')); ?>"
                                    maxlength="8"
                                    inputmode="numeric"
                                    pattern="[0-9]{1,8}"
                                    placeholder="Opcional, ej. 1234">
                                <?php $__errorArgs = ['device_pin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">PIN numérico corto configurado directamente en el checador (no es la contraseña del sistema).</div>
                            </div>
                        </div>
                    </div>

                    <div id="professor-fields" class="<?php if(old('preference_type') !== 'professor'): ?> d-none <?php endif; ?>">
                        <h6 class="mb-3 text-primary"><i class="bi bi-gear me-1"></i> Datos de Profesor (Académico)</h6>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="clave_profesor" class="form-label">Clave de profesor <span class="text-danger">*</span></label>
                                <input type="text" id="clave_profesor" name="clave_profesor"
                                    class="form-control <?php $__errorArgs = ['clave_profesor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('clave_profesor')); ?>"
                                    maxlength="20"
                                    <?php if(old('preference_type') === 'professor'): ?> required <?php endif; ?>
                                    placeholder="Ej. PROF-2024-001">
                                <?php $__errorArgs = ['clave_profesor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Identificador único del profesor en el sistema académico.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="profesor_email" class="form-label">Email institucional del profesor</label>
                                <input type="email" id="profesor_email" name="profesor_email"
                                    class="form-control <?php $__errorArgs = ['profesor_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('profesor_email')); ?>"
                                    maxlength="150"
                                    placeholder="profesor@institucion.edu.mx">
                                <?php $__errorArgs = ['profesor_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Puede ser distinto al email de usuario del sistema.</div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <input type="text" id="departamento" name="departamento"
                                    class="form-control <?php $__errorArgs = ['departamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('departamento')); ?>"
                                    maxlength="100"
                                    placeholder="Ej. Ingeniería en Sistemas">
                                <?php $__errorArgs = ['departamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="profesor_area_id" class="form-label">Área académica</label>
                                <select id="profesor_area_id" name="profesor_area_id" class="form-select <?php $__errorArgs = ['profesor_area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Sin área</option>
                                    <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($area->id); ?>" <?php if(old('profesor_area_id') == $area->id): echo 'selected'; endif; ?>>
                                            <?php echo e($area->identificador); ?> - <?php echo e($area->descripcion); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['profesor_area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="profesor_puesto_id" class="form-label">Puesto académico</label>
                                <select id="profesor_puesto_id" name="profesor_puesto_id" class="form-select <?php $__errorArgs = ['profesor_puesto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Sin puesto</option>
                                    <?php $__currentLoopData = $puestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($puesto->id); ?>" <?php if(old('profesor_puesto_id') == $puesto->id): echo 'selected'; endif; ?>>
                                            <?php echo e($puesto->identificador); ?> - <?php echo e($puesto->descripcion); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['profesor_puesto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="contrato" class="form-label">Tipo de contrato</label>
                                <input type="text" id="contrato" name="contrato"
                                    class="form-control <?php $__errorArgs = ['contrato'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('contrato')); ?>"
                                    maxlength="50"
                                    placeholder="Ej. PTC, PA, Base">
                                <?php $__errorArgs = ['contrato'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>


    <?php if(old('preference_type') === 'employee' || !old('preference_type')): ?>
    <div class="mb-4">
        <label class="form-label fw-semibold">Empleado Existente</label>
        <p class="form-text text-secondary mb-3">Seleccione un empleado que no tenga usuario asignado, o deje vacío para crear uno nuevo.</p>
        <select name="empleado_existente_id" class="form-select <?php $__errorArgs = ['empleado_existente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">-- Seleccionar empleado existente --</option>
<?php $__currentLoopData = $empleadosDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($emp->id); ?>" <?php echo e(old('empleado_existente_id') == $emp->id ? 'selected' : ''); ?>>
        <?php if($emp->user): ?>
            <?php echo e($emp->user->name); ?> (<?php echo e($emp->type); ?>)
        <?php else: ?>
            <?php echo e('Empleado ID: ' . $emp->id . ' - ' . $emp->type); ?>

        <?php endif; ?>
    </option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['empleado_existente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php endif; ?>

    
    <?php if(old('preference_type') === 'professor' || !old('preference_type')): ?>
    <div class="mb-4">
        <label class="form-label fw-semibold">Profesor Existente</label>
        <p class="form-text text-secondary mb-3">Seleccione un profesor que no tenga usuario asignado, o deje vacío para crear uno nuevo.</p>
        <select name="profesor_existente_id" class="form-select <?php $__errorArgs = ['profesor_existente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <option value="">-- Seleccionar profesor existente --</option>
<?php $__currentLoopData = $profesoresDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prof): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($prof->clave_profesor); ?>" <?php echo e(old('profesor_existente_id') == $prof->clave_profesor ? 'selected' : ''); ?>>
        <?php echo e($prof->nombre_completo); ?> · Clave <?php echo e($prof->clave_profesor); ?> · <?php echo e($prof->departamento ?: 'Sin departamento'); ?>

    </option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['profesor_existente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php endif; ?>

                    
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="<?php echo e(route('preferencia.usuarios.index')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i> Crear usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\preferencia\crear.blade.php ENDPATH**/ ?>