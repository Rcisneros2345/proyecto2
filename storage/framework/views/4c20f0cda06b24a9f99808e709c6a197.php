<?php $__env->startSection('title', 'Usuarios con preferencia'); ?>
<?php $__env->startSection('breadcrumb', 'Administración › Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-12">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Usuarios del sistema</h5>
                    <p class="text-muted small mb-0">Administra credenciales, perfiles y grupos de seguridad.</p>
                </div>
                <a href="<?php echo e(route('preferencia.usuarios.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Crear usuario
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="q" class="visually-hidden">Buscar usuario</label>
                        <input id="q" name="q" value="<?php echo e($search); ?>" class="form-control" placeholder="Buscar por nombre, usuario o correo">
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="visually-hidden">Tipo</label>
                        <select id="type" name="type" class="form-select">
                            <option value="">Todos los perfiles</option>
                            <option value="employee" <?php if($type === 'employee'): echo 'selected'; endif; ?>>Empleados</option>
                            <option value="professor" <?php if($type === 'professor'): echo 'selected'; endif; ?>>Profesores</option>
                            <option value="unassigned" <?php if($type === 'unassigned'): echo 'selected'; endif; ?>>Sin perfil</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-outline-primary flex-grow-1"><i class="bi bi-search me-1"></i>Buscar</button>
                        <a href="<?php echo e(route('preferencia.usuarios.index')); ?>" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    </div>
                </form>
                <?php if($users->count() > 0): ?>
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
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($users->firstItem() + $loop->index); ?></td>
                                    <td class="fw-semibold"><?php echo e($user->name); ?></td>
                                    <td><code><?php echo e($user->username ?: 'Sin username'); ?></code></td>
                                    <td><?php echo e(e($user->email)); ?></td>
                                    <td>
                                        <?php if($user->employee): ?>
                                            <span class="badge bg-primary">Empleado</span>
                                        <?php elseif($user->professor): ?>
                                            <span class="badge bg-secondary">Profesor</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin perfil</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($user->employee): ?>
                                            <div><?php echo e($user->employee->numero_empleado ?: $user->employee->user_id ?: 'Sin número'); ?></div>
                                            <small class="text-muted"><?php echo e($user->employee->permissionGroups->pluck('name')->join(', ') ?: 'Sin grupos'); ?></small>
                                        <?php elseif($user->professor): ?>
                                            <div><?php echo e($user->professor->clave_profesor); ?></div>
                                            <small class="text-muted"><?php echo e($user->professor->permissionGroups->pluck('name')->join(', ') ?: 'Sin grupos'); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Sin perfil asignado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('preferencia.usuarios.edit', $user)); ?>" class="btn btn-sm btn-outline-primary" title="Editar usuario">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <?php if($user->employee): ?>
                                            <a href="<?php echo e(route('employees.edit', $user->employee)); ?>" class="btn btn-sm btn-outline-secondary" title="Ver empleado">
                                                <i class="bi bi-person-badge"></i>
                                            </a>
                                        <?php elseif($user->professor): ?>
                                            <a href="<?php echo e(route('academia.profesores.show', $user->professor)); ?>" class="btn btn-sm btn-outline-secondary" title="Ver profesor">
                                                <i class="bi bi-mortarboard"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No hay usuarios registrados con preferencia.
                        <br><small>Crea tu primer usuario usando el botón de arriba.</small>
                    </div>
                <?php endif; ?>
                <div class="mt-3"><?php echo e($users->links()); ?></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/preferencia/index.blade.php ENDPATH**/ ?>