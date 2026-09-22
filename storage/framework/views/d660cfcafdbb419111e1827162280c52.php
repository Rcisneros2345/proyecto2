<?php $__env->startSection('title', 'Horarios por Profesor'); ?>
<?php $__env->startSection('breadcrumb', 'Academia › Horarios › Por Profesor'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Horarios por Profesor','subtitle' => 'Consulta la carga horaria de los profesores del ciclo seleccionado.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Horarios por Profesor','subtitle' => 'Consulta la carga horaria de los profesores del ciclo seleccionado.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('academia.ciclos.index')); ?>" class="btn btn-outline-secondary btn-sm">
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

<div class="card">
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Buscar profesor</label>
                <select class="form-select" id="profesorSelect">
                    <option value="">-- Seleccionar profesor --</option>
                    <?php $__currentLoopData = \App\Models\Academia\Profesor::activo()
                        ->whereHas('horarios', fn ($q) => $q->where('inicial', $ciclo->inicial)
                            ->where('final', $ciclo->final)
                            ->where('periodo', $ciclo->periodo))
                        ->orderBy('paterno')->orderBy('materno')->orderBy('nombre_profesor')
                        ->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->clave_profesor); ?>"><?php echo e($p->nombre_completo); ?> (<?php echo e($p->clave_profesor); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div id="horarioContainer" class="d-none">
            <h4 class="mb-3">Horario de <span id="profesorNombre" class="fw-semibold"></span> (<?php echo e($ciclo->label); ?>)</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Sesión</th>
                            <?php $__currentLoopData = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-center">
                                    <span class="badge <?php echo e(in_array($d, [6,7]) ? 'bg-purple' : 'bg-primary'); ?> me-1"><?php echo e($label); ?></span>
                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody id="horarioBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const select = document.getElementById('profesorSelect');
const container = document.getElementById('horarioContainer');
const body = document.getElementById('horarioBody');
const nombreEl = document.getElementById('profesorNombre');

select.addEventListener('change', function() {
    const clave = this.value;
    if (!clave) {
        container.classList.add('d-none');
        return;
    }
    nombreEl.textContent = this.options[this.selectedIndex].text;
    container.classList.remove('d-none');
    
    fetch('/api/academia/grupo-detalle?profesor=' + clave + '&ciclo=<?php echo e($ciclo->label); ?>')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            renderHorario(data.data.horarios);
        });
});

function renderHorario(horarios) {
    const grid = {};
    horarios.forEach(h => {
        const key = h.dia + '-' + h.sesion;
        if (!grid[key]) grid[key] = [];
        grid[key].push(h);
    });
    
    let html = '';
    for (let sesion = 1; sesion <= 12; sesion++) {
        let row = '<tr><td class="text-nowrap small text-muted"><div class="fw-semibold">Ses. ' + sesion + '</div></td>';
        for (let d = 1; d <= 7; d++) {
            const key = d + '-' + sesion;
            if (grid[key]) {
                row += '<td class="align-middle">';
                grid[key].forEach(c => {
                    row += "<div class='mb-2 p-2 rounded bg-light border'>";
                    row += "<div class='fw-semibold small'>" + (c.materia?.label || '') + "</div>";
                    row += "<div class='small text-muted'>" + c.grupo + ' · ' + c.aula + "</div>";
                    row += "<span class='badge " + (c.tipo === 'PTC' ? 'bg-purple' : 'bg-info') + "'>" + c.tipo + "</span>";
                    row += "</div>";
                });
                row += '</td>';
            } else {
                row += '<td class="align-middle"><span class="text-muted">—</span></td>';
            }
        }
        row += '</tr>';
        body.innerHTML += row;
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\academia\horarios\profesor.blade.php ENDPATH**/ ?>