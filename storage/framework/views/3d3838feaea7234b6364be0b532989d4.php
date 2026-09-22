<?php $__env->startSection('title', 'Cola de sincronización'); ?>
<?php $__env->startSection('breadcrumb', 'Operación › Cola de sincronización'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Cola de sincronización','subtitle' => 'Consulta qué operaciones están pendientes, en proceso o terminadas.','hideTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cola de sincronización','subtitle' => 'Consulta qué operaciones están pendientes, en proceso o terminadas.','hide-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    <?php $__env->slot('actions'); ?>
        <a href="<?php echo e(route('devices.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-hdd-network me-1"></i> Ver dispositivos
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

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['action' => route('operations.queue'),'clearUrl' => route('operations.queue')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('operations.queue')),'clear-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('operations.queue'))]); ?>
            <div class="col-md-2">
                <label class="form-label small mb-1" for="type">Tipo</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Todos</option>
                    <option value="firebird" <?php if(request('type') === 'firebird'): echo 'selected'; endif; ?>>Firebird</option>
                    <option value="device" <?php if(request('type') === 'device'): echo 'selected'; endif; ?>>Dispositivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1" for="status">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = ['queued' => 'En cola', 'running' => 'Procesando', 'completed' => 'Completada', 'failed' => 'Fallida']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('status') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1" for="operation">Operación</label>
                <select class="form-select" id="operation" name="operation">
                    <option value="">Todas</option>
                    <?php $__currentLoopData = ['sync_full' => 'Empleado completo', 'all' => 'Todo', 'users' => 'Usuarios', 'attendances' => 'Asistencias', 'fingerprints' => 'Huellas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('operation') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
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
<div class="d-flex gap-2 mb-2">
    <a href="<?php echo e(route('firebird.index')); ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-database me-1"></i>Ir a Firebird</a>
    <a href="<?php echo e(route('operations.notifications')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-bell me-1"></i>Notificaciones</a>
    <span class="ms-auto small text-muted align-self-center">Centro de Operaciones — cola unificada (Firebird + Dispositivos)</span>
</div>

<div class="card shadow-sm" data-sync-queue
    data-url="<?php echo e(route('operations.queue.data.unified', request()->query())); ?>"
    data-cancel-url="<?php echo e(url('/sync-queue')); ?>"
    data-legacy-url="<?php echo e(route('operations.queue.data', request()->query())); ?>">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-cards">
            <thead><tr><th>Tipo</th><th>Origen</th><th>Operación</th><th>Estado</th><th>Progreso</th><th>Resultado</th><th>Fecha</th><th>Acciones</th></tr></thead>
            <tbody data-sync-rows>
            <?php $__empty_1 = true; $__currentLoopData = $syncs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sync): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $percent = $sync->total ? min(100, (int) round(($sync->processed / $sync->total) * 100)) : ($sync->status === 'completed' ? 100 : 0);
                    $statusLabels = ['queued' => 'En cola', 'running' => 'Procesando', 'completed' => 'Completada', 'failed' => 'Fallida'];
                    $statusClass = ['queued' => 'cat-orange', 'running' => 'cat-blue', 'completed' => 'cat-green', 'failed' => 'cat-red'][$sync->status] ?? 'cat-gray';
                ?>
                <tr data-sync-id="<?php echo e($sync->id); ?>">
                    <td data-label="Tipo"><span class="badge bg-secondary">Dispositivo</span></td>
                    <td data-label="Origen"><a href="<?php echo e(route('devices.show', $sync->device)); ?>" class="fw-semibold text-decoration-none"><?php echo e($sync->device->name); ?></a></td>
                    <td data-label="Operación">
                        <?php echo e($sync->operation_label); ?>

                        <?php if($sync->operation === 'all' && in_array($sync->stage, ['usuarios', 'asistencias', 'huellas'], true)): ?>
                            <div class="small text-tertiary-token"><i class="bi bi-arrow-right-short"></i><?php echo e(ucfirst($sync->stage)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td data-label="Estado">
                        <span class="badge badge-with-dot <?php echo e($statusClass); ?>">
                            <?php if($sync->status === 'completed'): ?><i class="bi bi-check-circle-fill me-1"></i><?php endif; ?>
                            <?php echo e($statusLabels[$sync->status] ?? $sync->status); ?>

                        </span>
                    </td>
                    <td data-label="Progreso" style="min-width:180px"><div class="progress" role="progressbar" aria-valuenow="<?php echo e($percent); ?>" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar <?php echo e($sync->status === 'running' ? 'progress-bar-striped progress-bar-animated' : ''); ?>" style="width:<?php echo e($percent); ?>%"><?php echo e($percent); ?>%</div></div><small class="text-muted"><?php echo e($sync->processed); ?> de <?php echo e($sync->total ?: '—'); ?></small></td>
                    <td data-label="Resultado">
                        <?php echo e($sync->created_count); ?> creados · <?php echo e($sync->updated_count); ?> actualizados
                        <?php if($sync->operation === 'sync_full' && $sync->employee): ?>
                            <div class="small text-tertiary-token"><?php echo e($sync->employee->name); ?></div>
                        <?php endif; ?>
                        <?php if($sync->error_message): ?><div class="text-danger small"><?php echo e($sync->error_message); ?></div><?php endif; ?>
                    </td>
                    <td data-label="Fecha" class="small text-muted"><?php echo e($sync->created_at->format('d/m/Y H:i')); ?></td>
                    <td data-label="Acciones" class="text-end">
                        <?php if($sync->status === 'failed' && $sync->operation === 'sync_full'): ?>
                            <form action="<?php echo e(route('operations.retry', $sync)); ?>" method="POST" class="d-inline" data-sync-retry>
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-outline-warning" type="submit" title="Reintentar solo este dispositivo" aria-label="Reintentar sincronización"><i class="bi bi-arrow-repeat"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay operaciones en la cola.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?php echo e($syncs->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (() => {
        const queue = document.querySelector('[data-sync-queue]');
        const rows = queue?.querySelector('[data-sync-rows]');
        if (!queue || !rows) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';
        const labels = { queued: 'En cola', running: 'Procesando', completed: 'Completada', failed: 'Fallida', cancelled: 'Cancelada' };
        const classes = { queued: 'cat-orange', running: 'cat-blue', completed: 'cat-green', failed: 'cat-red', cancelled: 'cat-gray' };
        const esc = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
        const render = (syncs) => {
            if (!syncs.length) {
                rows.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay operaciones en la cola.</td></tr>';
                return;
            }
            rows.innerHTML = syncs.map((sync) => {
                const percent = sync.total ? Math.min(100, Math.round((sync.processed / sync.total) * 100)) : (sync.status === 'completed' ? 100 : 0);
                const active = ['queued', 'running'].includes(sync.status);
                const typeBadge = sync.type === 'firebird' ? '<span class="badge bg-info-subtle text-info">Firebird</span>' : (sync.type === 'device' ? '<span class="badge bg-secondary-subtle text-secondary">Dispositivo</span>' : '');
                // Usa URLs específicas si vienen del backend unificado, si no fallback a cancelUrl/id
                const cancelUrl = sync.cancel_url || (active ? `${queue.dataset.cancelUrl}/${sync.id}/cancel` : null);
                const retryUrl = sync.retry_url || null;
                const deleteUrl = sync.delete_url || (!active ? `${queue.dataset.cancelUrl}/${sync.id}` : null);
                let action = '';
                if (active && cancelUrl) action = `<button class="btn btn-sm btn-outline-warning" data-cancel-url="${esc(cancelUrl)}" title="Cancelar"><i class="bi bi-stop-circle"></i></button>`;
                else if (retryUrl) action = `<button class="btn btn-sm btn-outline-warning" data-retry-url="${esc(retryUrl)}" title="Reintentar"><i class="bi bi-arrow-repeat"></i></button>`;
                else if (deleteUrl) action = `<button class="btn btn-sm btn-icon-danger" data-delete-url="${esc(deleteUrl)}" title="Eliminar"><i class="bi bi-trash"></i></button>`;
                else if (sync.type === 'firebird') action = `<a href="${esc(sync.device_url)}" class="btn btn-sm btn-outline-primary" title="Ver detalle"><i class="bi bi-eye"></i></a>`;
                const stageNames = { usuarios: 'Usuarios', asistencias: 'Asistencias', huellas: 'Huellas' };
                const subStage = (sync.operation === 'all' && stageNames[sync.stage])
                    ? `<div class="small text-tertiary-token"><i class="bi bi-arrow-right-short"></i>${stageNames[sync.stage]}</div>`
                    : (sync.type === 'firebird' && sync.stage ? `<div class="small text-tertiary-token">${esc(sync.stage)}</div>` : '');
                const originCell = sync.device_url ? `<a href="${esc(sync.device_url)}" class="fw-semibold text-decoration-none">${esc(sync.device)}</a>` : esc(sync.device);
                return `<tr data-sync-id="${sync.id}" data-sync-type="${esc(sync.type || '')}">
                    <td data-label="Tipo">${typeBadge}</td>
                    <td data-label="Origen">${originCell}</td>
                    <td data-label="Operación">${esc(sync.operation_label)}${subStage}</td>
                    <td data-label="Estado"><span class="badge badge-with-dot ${classes[sync.status] || 'cat-gray'}">${sync.status === 'completed' ? '<i class="bi bi-check-circle-fill me-1"></i>' : ''}${labels[sync.status] || esc(sync.status)}</span></td>
                    <td data-label="Progreso" style="min-width:180px"><div class="progress" role="progressbar" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar ${sync.status === 'running' ? 'progress-bar-striped progress-bar-animated' : ''}" style="width:${percent}%">${percent}%</div></div><small class="text-muted">${sync.processed} de ${sync.total || '—'}</small></td>
                    <td data-label="Resultado">${sync.created} creados · ${sync.updated} actualizados${sync.items?.length ? `<div class="small text-tertiary-token">${sync.items.map((item) => `${esc(item.label)}: ${esc(item.status)}`).join(' · ')}</div>` : ''}${sync.error ? `<div class="text-danger small">${esc(sync.error)}</div>` : ''}</td>
                    <td data-label="Fecha" class="small text-muted">${esc(sync.created_at)}</td>
                    <td data-label="Acciones" class="text-end">${action}</td>
                </tr>`;
            }).join('');
        };
        const request = async (url, method = 'GET') => {
            const response = await fetch(url, { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'No se pudo completar la operación.');
            return data;
        };
        const refresh = async () => {
            try {
                const data = await request(queue.dataset.url);
                render(data.syncs);
                if (data.syncs.some((sync) => ['queued', 'running'].includes(sync.status))) setTimeout(refresh, 3000);
            } catch (error) {
                window.dashToast?.({ type: 'error', title: 'No se pudo actualizar la cola', message: error.message });
                setTimeout(refresh, 8000);
            }
        };
        rows.addEventListener('click', async (event) => {
            const cancelBtn = event.target.closest('[data-cancel-url]');
            const deleteBtn = event.target.closest('[data-delete-url]');
            const retryBtn = event.target.closest('[data-retry-url]');
            // Fallback legacy id-based
            const cancelLegacy = event.target.closest('[data-cancel]');
            const removeLegacy = event.target.closest('[data-delete]');
            const retryLegacy = event.target.closest('[data-retry]');
            const legacyId = cancelLegacy?.dataset.cancel || removeLegacy?.dataset.delete || retryLegacy?.dataset.retry;
            const targetUrl = cancelBtn?.dataset.cancelUrl || deleteBtn?.dataset.deleteUrl || retryBtn?.dataset.retryUrl;
            const isCancel = !!cancelBtn || !!cancelLegacy;
            const isDelete = !!deleteBtn || !!removeLegacy;
            const isRetry = !!retryBtn || !!retryLegacy;
            if (!targetUrl && !legacyId) return;
            if (isCancel && !window.confirm('¿Cancelar esta sincronización?')) return;
            if (isDelete && !window.confirm('¿Eliminar este registro de la cola?')) return;
            if (isRetry && !window.confirm('¿Reintentar?')) return;
            try {
                let url = targetUrl;
                let method = isDelete ? 'DELETE' : 'POST';
                if (!url && legacyId) {
                    url = `${queue.dataset.cancelUrl}/${legacyId}${isCancel ? '/cancel' : (isRetry ? '/retry' : '')}`;
                }
                const data = await request(url, method);
                window.dashToast?.({ type: 'success', title: isCancel ? 'Sincronización cancelada' : (isRetry ? 'Reintento enviado' : 'Registro eliminado'), message: data.message });
                refresh();
            } catch (error) {
                window.dashToast?.({ type: 'error', title: 'No se pudo completar la acción', message: error.message });
            }
        });
        refresh();
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views\operations\queue.blade.php ENDPATH**/ ?>