

<?php $__env->startSection('title', 'Editar usuario'); ?>
<?php $__env->startSection('breadcrumb', 'Administracion > Usuarios > Editar'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Editar usuario','subtitle' => 'Actualiza la identidad de acceso y los grupos de seguridad del usuario.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar usuario','subtitle' => 'Actualiza la identidad de acceso y los grupos de seguridad del usuario.']); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('preferencia.usuarios.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
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

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header"><h2 class="h6 mb-0"><i class="bi bi-person-gear me-2"></i>Datos de acceso</h2></div>
            <div class="card-body">
                <form action="<?php echo e(route('preferencia.usuarios.update', $user)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">Nombre completo</label>
                            <input id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Usuario de login</label>
                            <input id="username" name="username" value="<?php echo e(old('username', $user->username)); ?>" class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="username">
                            <div class="form-text">Puede iniciar sesión con este usuario o con su correo.</div>
                            <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" name="email" type="email" value="<?php echo e(old('email', $user->email)); ?>" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rol global</label>
                            <select id="role" name="role" class="form-select <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="operator" <?php if(old('role', $user->role?->value ?? $user->role) === 'operator'): echo 'selected'; endif; ?>>Operador</option>
                                <option value="admin" <?php if(old('role', $user->role?->value ?? $user->role) === 'admin'): echo 'selected'; endif; ?>>Administrador</option>
                            </select>
                            <div class="form-text">Administrador omite las restricciones de módulos.</div>
                            <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Perfil vinculado</label>
                            <div class="form-control bg-body-secondary">
                                <?php if($user->employee): ?>
                                    Empleado: <?php echo e($user->employee->numero_empleado ?: $user->employee->user_id ?: $user->employee->name); ?>

                                <?php elseif($user->professor): ?>
                                    Profesor: <?php echo e($user->professor->clave_profesor); ?>

                                <?php else: ?>
                                    Sin empleado o profesor vinculado
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input id="password" name="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="new-password">
                            <div class="form-text">Déjala vacía para conservar la actual.</div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?php echo e(route('preferencia.usuarios.index')); ?>" class="btn btn-outline-secondary">Cancelar</a>
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
                <form action="<?php echo e(route('preferencia.usuarios.update', $user)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="name" value="<?php echo e($user->name); ?>">
                    <input type="hidden" name="username" value="<?php echo e($user->username); ?>">
                    <input type="hidden" name="email" value="<?php echo e($user->email); ?>">
                    <input type="hidden" name="role" value="<?php echo e($user->role?->value ?? $user->role); ?>">
                    <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="d-flex align-items-start gap-2 border rounded p-3 mb-2">
                            <input class="form-check-input mt-1" type="checkbox" name="group_ids[]" value="<?php echo e($group->id); ?>" <?php if(in_array($group->id, old('group_ids', $assignedGroupIds), true)): echo 'checked'; endif; ?>>
                            <span>
                                <strong><?php echo e($group->name); ?></strong>
                                <small class="d-block text-muted"><?php echo e($group->description ?: 'Sin descripción'); ?></small>
                            </span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="alert alert-warning">No hay grupos configurados.</div>
                    <?php endif; ?>
                    <button class="btn btn-outline-primary w-100 mt-2"><i class="bi bi-shield-check me-1"></i>Guardar grupos</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="h6">Acceso efectivo</h2>
                <p class="text-muted small mb-2">Los módulos se habilitan desde los permisos contenidos en sus grupos.</p>
                <a href="<?php echo e(route('permission-groups.index')); ?>" class="btn btn-sm btn-outline-secondary">Administrar grupos y módulos</a>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="h6">Captura de asistencia de clase</h2>
                <p class="text-muted small mb-2">Define los niveles, sedes y ciclos que este usuario puede capturar.</p>
                <a href="<?php echo e(route('preferencia.usuarios.captura-asistencia.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-calendar2-check me-1"></i>Administrar asignaciones
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/preferencia/editar.blade.php ENDPATH**/ ?>