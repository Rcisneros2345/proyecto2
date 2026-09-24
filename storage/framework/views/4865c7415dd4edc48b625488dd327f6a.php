

<?php $__env->startSection('title', 'Registrar incidencia'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Incidencias › Registrar'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Registrar incidencia','subtitle' => 'Captura una incidencia para un empleado o profesor.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Registrar incidencia','subtitle' => 'Captura una incidencia para un empleado o profesor.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('incidencias.index')); ?>" class="btn btn-outline-secondary">
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

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo e(route('incidencias.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="tipo_persona" class="form-label">Tipo de persona</label>
                    <select id="tipo_persona" name="tipo_persona" class="form-select <?php $__errorArgs = ['tipo_persona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <option value="empleado" <?php echo e(old('tipo_persona', 'empleado') === 'empleado' ? 'selected' : ''); ?>>Empleado</option>
                        <option value="profesor" <?php echo e(old('tipo_persona') === 'profesor' ? 'selected' : ''); ?>>Profesor</option>
                    </select>
                    <?php $__errorArgs = ['tipo_persona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4" data-person-type="empleado">
                    <label for="empleado_id" class="form-label">Empleado</label>
                    <select id="empleado_id" name="empleado_id" class="form-select <?php $__errorArgs = ['empleado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Selecciona un empleado</option>
                        <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($empleado->id); ?>"
                                    data-area="<?php echo e($empleado->area?->descripcion ?? 'Sin área'); ?>"
                                    data-puesto="<?php echo e($empleado->puesto?->descripcion ?? 'Sin puesto'); ?>"
                                    data-responsable="<?php echo e($empleado->area?->empleadoResponsable?->name ?? 'Sin responsable de área'); ?>"
                                    data-jefe="<?php echo e($empleado->area?->head?->name ?? 'Sin jefe de área'); ?>"
                                <?php echo e(old('empleado_id') == $empleado->id ? 'selected' : ''); ?>>
                                <?php echo e($empleado->name); ?> (<?php echo e($empleado->user_id); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['empleado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4 d-none" data-person-type="profesor">
                    <label for="profesor_clave" class="form-label">Profesor</label>
                    <select id="profesor_clave" name="profesor_clave" class="form-select <?php $__errorArgs = ['profesor_clave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Selecciona un profesor</option>
                        <?php $__currentLoopData = $profesores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profesor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($profesor->clave_profesor); ?>"
                                    data-area="<?php echo e($profesor->area?->descripcion ?? 'Sin área'); ?>"
                                    data-puesto="<?php echo e($profesor->puesto?->descripcion ?? 'Sin puesto'); ?>"
                                    data-responsable="<?php echo e($profesor->area?->empleadoResponsable?->name ?? 'Sin responsable de área'); ?>"
                                    data-jefe="<?php echo e($profesor->area?->head?->name ?? $profesor->director?->name ?? 'Sin jefe de área'); ?>"
                                <?php echo e(old('profesor_clave') == $profesor->clave_profesor ? 'selected' : ''); ?>>
                                <?php echo e(trim("{$profesor->paterno} {$profesor->materno} {$profesor->nombre_profesor}")); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['profesor_clave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4">
                    <label for="asunto" class="form-label">Asunto</label>
                    <input type="text" id="asunto" name="asunto" value="<?php echo e(old('asunto')); ?>" class="form-control <?php $__errorArgs = ['asunto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['asunto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4">
                    <label for="tipo_justificacion" class="form-label">Tipo de falta</label>
                    <input type="text" id="tipo_justificacion" name="tipo_justificacion" value="<?php echo e(old('tipo_justificacion')); ?>" class="form-control <?php $__errorArgs = ['tipo_justificacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['tipo_justificacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4">
                    <label for="fecha_falta_programada" class="form-label">Fecha de falta programada</label>
                    <input type="date" id="fecha_falta_programada" name="fecha_falta_programada" value="<?php echo e(old('fecha_falta_programada', now()->format('Y-m-d'))); ?>" class="form-control <?php $__errorArgs = ['fecha_falta_programada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__errorArgs = ['fecha_falta_programada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4">
                    <label for="tipo_duracion" class="form-label">Duración</label>
                    <select id="tipo_duracion" name="tipo_duracion" class="form-select <?php $__errorArgs = ['tipo_duracion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <option value="dia_completo" <?php if(old('tipo_duracion', 'dia_completo') === 'dia_completo'): echo 'selected'; endif; ?>>Día completo</option>
                        <option value="horario" <?php if(old('tipo_duracion') === 'horario'): echo 'selected'; endif; ?>>Horario</option>
                    </select>
                    <?php $__errorArgs = ['tipo_duracion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-2" data-duration="horario">
                    <label for="hora_inicio" class="form-label">Desde</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo e(old('hora_inicio')); ?>" class="form-control <?php $__errorArgs = ['hora_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['hora_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-2" data-duration="horario">
                    <label for="hora_fin" class="form-label">Hasta</label>
                    <input type="time" id="hora_fin" name="hora_fin" value="<?php echo e(old('hora_fin')); ?>" class="form-control <?php $__errorArgs = ['hora_fin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['hora_fin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_area" class="form-label">Área</label>
                    <input id="identificacion_area" class="form-control bg-body-secondary" value="Selecciona una persona" readonly>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_puesto" class="form-label">Puesto</label>
                    <input id="identificacion_puesto" class="form-control bg-body-secondary" value="Selecciona una persona" readonly>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_responsable" class="form-label">Responsable de área</label>
                    <input id="identificacion_responsable" class="form-control bg-body-secondary" value="Se determina por el área" readonly>
                </div>

                <div class="col-md-3">
                    <label for="identificacion_jefe" class="form-label">Jefe / director</label>
                    <input id="identificacion_jefe" class="form-control bg-body-secondary" value="Se determina por la ruta" readonly>
                </div>

                <div class="col-12">
                    <div class="form-text">El área, puesto, responsable y jefe/director se toman del perfil seleccionado y de la ruta de autorización. No se capturan manualmente.</div>
                </div>

                <div class="col-12">
                    <label for="motivo" class="form-label">Motivo</label>
                    <textarea id="motivo" name="motivo" rows="4" class="form-control <?php $__errorArgs = ['motivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('motivo')); ?></textarea>
                    <?php $__errorArgs = ['motivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6">
                    <label for="comentarios" class="form-label">Comentarios</label>
                    <textarea id="comentarios" name="comentarios" rows="3" class="form-control <?php $__errorArgs = ['comentarios'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('comentarios')); ?></textarea>
                    <?php $__errorArgs = ['comentarios'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6">
                    <label for="solicitud" class="form-label">Solicitud</label>
                    <textarea id="solicitud" name="solicitud" rows="3" class="form-control <?php $__errorArgs = ['solicitud'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('solicitud')); ?></textarea>
                    <?php $__errorArgs = ['solicitud'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?php echo e(route('incidencias.index')); ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar incidencia</button>
            </div>
        </form>
    </div>
</div>

<script>
    const tipoPersona = document.getElementById('tipo_persona');
    const tipoPersonaBlocks = document.querySelectorAll('[data-person-type]');
    const tipoDuracion = document.getElementById('tipo_duracion');
    const durationBlocks = document.querySelectorAll('[data-duration]');
    const identificationFields = {
        area: document.getElementById('identificacion_area'),
        puesto: document.getElementById('identificacion_puesto'),
        responsable: document.getElementById('identificacion_responsable'),
        jefe: document.getElementById('identificacion_jefe'),
    };

    function syncTipoPersona() {
        const value = tipoPersona.value;
        tipoPersonaBlocks.forEach((element) => {
            const show = element.dataset.personType === value;
            element.classList.toggle('d-none', !show);
            const field = element.querySelector('select');
            if (field) {
                field.disabled = !show;
                field.required = show;
            }
        });
    }

    tipoPersona.addEventListener('change', syncTipoPersona);
    syncTipoPersona();

    function syncIdentification() {
        const select = document.querySelector(`[data-person-type="${tipoPersona.value}"] select`);
        const option = select?.selectedOptions[0];
        identificationFields.area.value = option?.dataset.area || 'Selecciona una persona';
        identificationFields.puesto.value = option?.dataset.puesto || 'Selecciona una persona';
        identificationFields.responsable.value = option?.dataset.responsable || 'Se determina por el área';
        identificationFields.jefe.value = option?.dataset.jefe || 'Se determina por la ruta';
    }

    document.querySelectorAll('[data-person-type] select').forEach((select) => select.addEventListener('change', syncIdentification));
    syncIdentification();

    function syncDuration() {
        const isSchedule = tipoDuracion.value === 'horario';
        durationBlocks.forEach((element) => {
            element.classList.toggle('d-none', !isSchedule);
            const field = element.querySelector('input');
            if (field) field.required = isSchedule;
        });
    }

    tipoDuracion.addEventListener('change', syncDuration);
    syncDuration();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/incidencias/create.blade.php ENDPATH**/ ?>