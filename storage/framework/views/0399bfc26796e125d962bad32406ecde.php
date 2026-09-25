<?php $__env->startSection('title', 'Alumnos'); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Alumnos'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Alumnos','subtitle' => 'Catálogo académico y estado general de los alumnos por ciclo y turno.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alumnos','subtitle' => 'Catálogo académico y estado general de los alumnos por ciclo y turno.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <?php if (isset($component)) { $__componentOriginal50f7720e882b68836720a7a50217df1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal50f7720e882b68836720a7a50217df1d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.academia.ciclo-selector','data' => ['ciclo' => $ciclo,'ciclos' => \App\Models\Academia\Ciclo::orderByDesc('inicial')->orderByDesc('final')->orderByDesc('periodo')->get()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('academia.ciclo-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['ciclo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ciclo),'ciclos' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Models\Academia\Ciclo::orderByDesc('inicial')->orderByDesc('final')->orderByDesc('periodo')->get())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal50f7720e882b68836720a7a50217df1d)): ?>
<?php $attributes = $__attributesOriginal50f7720e882b68836720a7a50217df1d; ?>
<?php unset($__attributesOriginal50f7720e882b68836720a7a50217df1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal50f7720e882b68836720a7a50217df1d)): ?>
<?php $component = $__componentOriginal50f7720e882b68836720a7a50217df1d; ?>
<?php unset($__componentOriginal50f7720e882b68836720a7a50217df1d); ?>
<?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['action' => route('academia.alumnos.index'),'clearUrl' => route('academia.alumnos.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('academia.alumnos.index')),'clear-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('academia.alumnos.index'))]); ?>
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" class="form-control" placeholder="Control, nombre, CURP..." value="<?php echo e(request('buscar')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Estatus</label>
                <select name="estatus" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = ['ACTIVO','BAJA','EGRESADO','TITULADO','IRREGULAR']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($e); ?>" <?php echo e(request('estatus') == $e ? 'selected' : ''); ?>><?php echo e($e); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nivel</label>
                <select name="nivel" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = \App\Models\Academia\Nivel::activo()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($n->nivel); ?>" <?php echo e(request('nivel') == $n->nivel ? 'selected' : ''); ?>><?php echo e($n->descripcion); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Turno</label>
                <select name="turno" class="form-select">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = \App\Models\Academia\Turno::activo()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->turno); ?>" <?php echo e(request('turno') == $t->turno ? 'selected' : ''); ?>><?php echo e($t->descripcion); ?></option>
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
        ['label' => 'Control', 'field' => 'numero_alumno', 'class' => 'fw-semibold'],
        ['label' => 'Nombre', 'render' => fn ($alumno) => '<a href="' . route('academia.alumnos.show', $alumno) . '" class="text-decoration-none fw-semibold">' . e($alumno->nombre_completo) . '</a>'],
        ['label' => 'Grupo', 'render' => function ($alumno) {
            $inscripcion = $alumno->inscripciones->first();

            return $inscripcion && $inscripcion->grupo
                ? '<span class="badge cat-blue">' . e($inscripcion->grupo->codigo_grupo) . '</span>'
                : '<span class="text-muted">—</span>';
        }],
        ['label' => 'CURP', 'field' => 'curp', 'class' => 'small'],
        ['label' => 'Nivel', 'field' => 'nivel'],
        ['label' => 'Turno', 'render' => fn ($alumno) => '<span class="badge ' . ($alumno->turnoRel && str_starts_with($alumno->turnoRel->descripcion_corta, 'V') ? 'bg-purple' : 'bg-warning') . '">' . e($alumno->turnoRel?->descripcion_corta ?? $alumno->turno) . '</span>'],
        ['label' => 'Sede', 'render' => fn ($alumno) => e($alumno->sede?->descripcion ?? '')],
        ['label' => 'Estatus', 'render' => fn ($alumno) => '<span class="badge badge--status ' . (in_array($alumno->estatus, ['ACTIVO', 'REINSCRITO']) ? 'badge--active' : 'badge--inactive') . '">' . e($alumno->estatus) . '</span>'],
    ];

    $actions = [
        ['type' => 'link', 'url' => fn ($alumno) => route('academia.alumnos.show', $alumno), 'style' => 'primary', 'title' => 'Ver', 'icon' => 'bi bi-eye'],
        ['type' => 'link', 'url' => fn ($alumno) => route('academia.alumnos.kardex', $alumno), 'style' => 'success', 'title' => 'Kardex', 'icon' => 'bi bi-file-earmark-text'],
        ['type' => 'link', 'url' => fn ($alumno) => route('academia.alumnos.historial', $alumno), 'style' => 'info', 'title' => 'Historial', 'icon' => 'bi bi-clock-history'],
    ];
?>

<?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => $headers,'rows' => $alumnos,'actions' => $actions,'pagination' => $alumnos,'emptyMessage' => 'No se encontraron alumnos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($headers),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alumnos),'actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actions),'pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alumnos),'empty-message' => 'No se encontraron alumnos']); ?>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/academia/alumnos/index.blade.php ENDPATH**/ ?>