<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['headers', 'rows', 'actions', 'emptyMessage' => 'No hay datos', 'striped' => true, 'hover' => true, 'bordered' => true, 'pagination' => null, 'perPageOptions' => [10, 25, 50, 100]]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['headers', 'rows', 'actions', 'emptyMessage' => 'No hay datos', 'striped' => true, 'hover' => true, 'bordered' => true, 'pagination' => null, 'perPageOptions' => [10, 25, 50, 100]]); ?>
<?php foreach (array_filter((['headers', 'rows', 'actions', 'emptyMessage' => 'No hay datos', 'striped' => true, 'hover' => true, 'bordered' => true, 'pagination' => null, 'perPageOptions' => [10, 25, 50, 100]]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $hasHiddenHeaders = collect($headers)->contains(fn ($header) => isset($header['hideOn']));
    $extraColumnCount = ($actions || $hasHiddenHeaders) ? 1 : 0;
    $currentPerPage = method_exists($rows, 'perPage') ? (int) $rows->perPage() : 25;
?>

<div class="card data-table-shell">
    <div class="card-body p-0">
        <?php if($rows->isEmpty()): ?>
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-table fs-1 mb-2"></i>
                <p><?php echo e($emptyMessage); ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table <?php echo e($striped ? 'table-striped' : ''); ?> <?php echo e($hover ? 'table-hover' : ''); ?> <?php echo e($bordered ? 'table-bordered' : ''); ?> align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $headerLabel = $header['label'] ?? $header;
                                    $headerClass = isset($header['class']) ? $header['class'] : '';
                                    $headerWidth = isset($header['width']) ? 'width: ' . $header['width'] . ';' : '';
                                    if (isset($header['hideOn'])) {
                                        $headerClass = trim($headerClass . ' d-none d-' . $header['hideOn'] . '-table-cell');
                                    }
                                ?>
                                <th style="<?php echo e($headerWidth); ?>" class="<?php echo e($headerClass); ?>">
                                    <?php echo e($headerLabel); ?>

                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($actions): ?>
                                <th class="text-end" style="width: <?php echo e($hasHiddenHeaders ? '150px' : '120px'); ?>;">Acciones</th>
                            <?php elseif($hasHiddenHeaders): ?>
                                <th class="text-end" style="width: 92px;">Más</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $rowId = 'datatable-row-' . $loop->index;
                            ?>
                            <tr>
                                <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $cellClass = isset($header['class']) ? $header['class'] : '';
                                        if (isset($header['hideOn'])) {
                                            $cellClass = trim($cellClass . ' d-none d-' . $header['hideOn'] . '-table-cell');
                                        }
                                    ?>
                                    <td class="<?php echo e($cellClass); ?>">
                                        <?php if(isset($header['render'])): ?>
                                            <?php echo $header['render']($row); ?>

                                        <?php elseif(isset($header['field'])): ?>
                                            <?php echo e($row[$header['field']] ?? ''); ?>

                                        <?php else: ?>
                                            <?php echo e($row->{$header} ?? ''); ?>

                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($actions): ?>
                                    <td class="text-end">
                                        <?php if($hasHiddenHeaders): ?>
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm me-2"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#<?php echo e($rowId); ?>"
                                                    aria-expanded="false"
                                                    aria-controls="<?php echo e($rowId); ?>"
                                                    title="Ver más">
                                                <i class="bi bi-list"></i>
                                            </button>
                                        <?php endif; ?>
                                        <div class="btn-group btn-group-sm">
                                            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $actionType = $action['type'] ?? 'link';
                                                    $actionStyle = is_callable($action['style'] ?? null) ? ($action['style']($row)) : ($action['style'] ?? 'primary');
                                                    $actionTitle = is_callable($action['title'] ?? null) ? ($action['title']($row)) : ($action['title'] ?? '');
                                                    $actionIcon = is_callable($action['icon'] ?? null) ? ($action['icon']($row)) : ($action['icon'] ?? 'bi bi-eye');
                                                    $actionUrl = is_callable($action['url'] ?? null) ? ($action['url']($row)) : ($action['url'] ?? '#');
                                                    $actionOnclick = is_callable($action['onclick'] ?? null) ? ($action['onclick']($row)) : ($action['onclick'] ?? '');
                                                ?>
                                                <?php if($actionType === 'link'): ?>
                                                    <a href="<?php echo e($actionUrl); ?>" class="btn btn-outline-<?php echo e($actionStyle); ?> btn-sm" title="<?php echo e($actionTitle); ?>">
                                                        <i class="<?php echo e($actionIcon); ?>"></i>
                                                    </a>
                                                <?php elseif($actionType === 'button'): ?>
                                                    <button type="button" class="btn btn-outline-<?php echo e($actionStyle); ?> btn-sm" onclick="<?php echo e($actionOnclick); ?>" title="<?php echo e($actionTitle); ?>">
                                                        <i class="<?php echo e($actionIcon); ?>"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </td>
                                <?php elseif($hasHiddenHeaders): ?>
                                    <td class="text-end">
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#<?php echo e($rowId); ?>"
                                                aria-expanded="false"
                                                aria-controls="<?php echo e($rowId); ?>"
                                                title="Ver más">
                                            <i class="bi bi-list"></i>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>

                            <?php if($hasHiddenHeaders): ?>
                                <tr class="collapse" id="<?php echo e($rowId); ?>">
                                    <td colspan="<?php echo e(count($headers) + $extraColumnCount); ?>" class="bg-body-tertiary">
                                        <div class="row g-2 py-2">
                                            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(isset($header['hideOn'])): ?>
                                                    <div class="col-6 col-md-4">
                                                        <div class="small text-muted"><?php echo e($header['label'] ?? $header); ?></div>
                                                        <div class="small fw-semibold">
                                                            <?php if(isset($header['render'])): ?>
                                                                <?php echo $header['render']($row); ?>

                                                            <?php elseif(isset($header['field'])): ?>
                                                                <?php echo e($row[$header['field']] ?? ''); ?>

                                                            <?php else: ?>
                                                                <?php echo e($row->{$header} ?? ''); ?>

                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if($pagination): ?>
        <div class="data-table-footer">
            <div class="data-table-summary">
                <?php
                    $fromItem = $rows->total() > 0 ? $rows->firstItem() : 0;
                    $toItem = $rows->total() > 0 ? $rows->lastItem() : 0;
                ?>
                Mostrando <?php echo e($fromItem); ?>–<?php echo e($toItem); ?> de <?php echo e($rows->total()); ?> registros
            </div>

            <div class="data-table-controls">
                <span class="small text-tertiary-token">Filas</span>
                <div class="data-table-per-page">
                    <?php $__currentLoopData = $perPageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $query = request()->query();
                            unset($query['page']);
                            $query['per_page'] = $option;
                            $url = request()->url() . '?' . http_build_query($query);
                        ?>
                        <a href="<?php echo e($url); ?>" class="data-table-per-page-link <?php echo e($currentPerPage == $option ? 'active' : ''); ?>"><?php echo e($option); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="card-footer border-0 bg-transparent pt-0 pagination-footer">
            <?php echo e($pagination->links()); ?>

        </div>
    <?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/components/data-table.blade.php ENDPATH**/ ?>