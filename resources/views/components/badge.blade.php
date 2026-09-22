@props(['variant' => 'default', 'size' => 'md', 'label' => null, 'color' => null, 'dot' => false, 'icon' => null, 'class' => ''])

@php
    // Semantic variant → badge--active / badge--inactive (status badges)
    $variantClasses = [
        'default' => 'badge--inactive',
        'primary' => 'badge--active',
        'success' => 'badge--active',
        'warning' => 'badge--inactive',
        'danger'  => 'badge--inactive',
        'info'    => 'badge--inactive',
        'purple'  => 'badge--inactive',
        'pink'    => 'badge--inactive',
        'teal'    => 'badge--inactive',
        'orange'  => 'badge--inactive',
    ];

    // Data color → cat-* classes (decorative / grouping badges)
    $colorClasses = [
        'blue'    => 'cat-blue',
        'orange'  => 'cat-orange',
        'purple'  => 'cat-purple',
        'pink'    => 'cat-pink',
        'red'     => 'cat-red',
        'green'   => 'cat-green',
        'amber'   => 'cat-amber',
        'lavender'=> 'cat-lavender',
        'gray'    => 'cat-gray',
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5',
        'md' => 'px-2.5 py-0.5',
        'lg' => 'px-3 py-1',
    ];

    $baseClass = 'badge badge--status';
    $variantClass = $color ? '' : ($variantClasses[$variant] ?? $variantClasses['default']);
    $catClass = $colorClasses[$color] ?? '';
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $dotClass = $dot ? 'badge-with-dot' : '';
@endphp

<span class="{{ $baseClass }} {{ $variantClass }} {{ $catClass }} {{ $sizeClass }} {{ $dotClass }} {{ $class }}">
    @if($icon)<i class="{{ $icon }} me-1"></i>@endif
    {{ $label }}{{ $slot }}
</span>