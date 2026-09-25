<?php $__env->startSection('title', 'Grupos'); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Grupos'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Grupos','subtitle' => 'Visión del catálogo de grupos por nivel, turno y sede.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Grupos','subtitle' => 'Visión del catálogo de grupos por nivel, turno y sede.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('academia.ciclos.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-calendar me-1"></i> Cambiar ciclo
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

<div class="card mb-4">
    <div class="card-body">
        <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['action' => route('academia.grupos.index'),'clearUrl' => route('academia.grupos.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('academia.grupos.index')),'clear-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('academia.grupos.index'))]); ?>
            <div class="col-md-3">
                <label class="form-label">Nivel</label>
                <select name="nivel" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = \App\Models\Academia\Nivel::activo()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($n->nivel); ?>" <?php echo e(request('nivel') == $n->nivel ? 'selected' : ''); ?>><?php echo e($n->descripcion); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Turno</label>
                <select name="turno" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = \App\Models\Academia\Turno::activo()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->turno); ?>" <?php echo e(request('turno') == $t->turno ? 'selected' : ''); ?>><?php echo e($t->descripcion); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sede</label>
                <select name="sede" class="form-select">
                    <option value="">Todas</option>
                    <?php $__currentLoopData = \App\Models\Academia\Sede::activo()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id_campus); ?>" <?php echo e(request('sede') == $s->id_campus ? 'selected' : ''); ?>><?php echo e($s->descripcion); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale9f22847d79d6273acb27aff60f1f678)): ?>
<?php $attributes = $__attributesOriginale9f22847d79d6273acb27aff60f1f678; ?>
<?php unset($__attributesOriginale9f22847d79d6273acb27aff60f1f678); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale9f22847d79d6273acb27aff60f1f678)): ?>
<?php $component = $__componentOriginale9f22847d79d6273acb27aff60f1f678; ?>
<?php unset($__componentOriginale9f22847d79d6273acb27aff60f1f678); ?>
<?php endif; ?>
    </div>
</div>

<?php
    $headers = [
        ['label' => 'Grupo', 'field' => 'codigo_grupo', 'class' => 'fw-semibold'],
        ['label' => 'Nivel', 'render' => fn ($grupo) => e($grupo->nivelRel?->descripcion ?? $grupo->nivel)],
        ['label' => 'Turno', 'render' => fn ($grupo) => e($grupo->turno_nombre)],
        ['label' => 'Grado', 'render' => fn ($grupo) => e($grupo->grado . '°')],
        ['label' => 'Modalidad', 'render' => function ($grupo) {
            $html = e($grupo->modalidad_nombre);

            if ($grupo->codigo_grupo_partes['nivel_superior']) {
                $html .= '<small class="d-block text-muted">Ingeniería/Licenciatura</small>';
            }

            return $html;
        }],
        ['label' => 'Inscritos', 'field' => 'inscritos'],
        ['label' => 'Sede', 'render' => fn ($grupo) => e($grupo->sede?->descripcion ?? $grupo->id_campus)],
        ['label' => 'Estado', 'render' => fn ($grupo) => '<span class="badge badge--status ' . ($grupo->activo ? 'badge--active' : 'badge--inactive') . '">' . ($grupo->activo ? 'Activo' : 'Inactivo') . '</span>'],
    ];

    $actions = [
        ['type' => 'link', 'url' => fn ($grupo) => route('academia.grupos.show', $grupo), 'style' => 'primary', 'title' => 'Ver', 'icon' => 'bi bi-eye'],
    ];
?>

<?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => $headers,'rows' => $grupos,'actions' => $actions,'pagination' => $grupos,'emptyMessage' => 'No se encontraron grupos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($headers),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($grupos),'actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actions),'pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($grupos),'empty-message' => 'No se encontraron grupos']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/academia/grupos/index.blade.php ENDPATH**/ ?>