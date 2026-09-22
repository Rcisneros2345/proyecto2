<?php
    $navigationItems = \App\Models\NavigationItem::query()
        ->with('module')
        ->where('active', true)
        ->orderBy('section')
        ->orderBy('sort_order')
        ->get()
        ->filter(fn ($item) => ! $item->admin_only || auth()->user()->isAdmin())
        ->filter(fn ($item) => ! $item->module || auth()->user()->canAccessModule($item->module->slug, $item->permission_action))
        ->groupBy('section');
?>

<?php $__currentLoopData = $navigationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="nav-group-title"><?php echo e($section); ?></div>
    <ul class="app-nav">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item">
                <a href="<?php echo e(route($item->route_name)); ?>"
                   data-tooltip="<?php echo e($item->label); ?>"
                   class="nav-link <?php echo e(request()->routeIs($item->route_name) || request()->routeIs($item->route_name . '.*') ? 'active' : ''); ?>"
                   title="<?php echo e($item->label); ?>">
                    <i class="bi <?php echo e($item->icon); ?>"></i><span class="nav-label"><?php echo e($item->label); ?></span>
                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\xampp\htdocs\proyecto2\resources\views/components/navigation-menu.blade.php ENDPATH**/ ?>