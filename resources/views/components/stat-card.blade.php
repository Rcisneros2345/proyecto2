@props(['icon', 'label', 'value', 'color'])

@php
    // Map color names to --cat-* token references for consistent theming
    $catTokenMap = [
        'blue'    => 'var(--cat-blue)',
        'orange'  => 'var(--cat-orange)',
        'purple'  => 'var(--cat-purple)',
        'pink'    => 'var(--cat-pink)',
        'red'     => 'var(--cat-red)',
        'green'   => 'var(--cat-green)',
        'amber'   => 'var(--cat-amber)',
        'lavender'=> 'var(--cat-lavender)',
        'gray'    => 'var(--cat-gray)',
        // Bootstrap semantic aliases → closest cat token
        'success' => 'var(--cat-green)',
        'danger'  => 'var(--cat-red)',
        'warning' => 'var(--cat-orange)',
        'info'    => 'var(--cat-blue)',
    ];
    $catToken = $catTokenMap[$color] ?? null;

    // Fallback for colors without a cat token (e.g. teal)
    $iconBg = $catToken ? "color-mix(in srgb, {$catToken} 15%, transparent)" : "var(--bs-{$color}-subtle, rgba(148,163,184,.14))";
    $iconColor = $catToken ?? "var(--cat-{$color}, var(--text-secondary))";
    $valueColor = $catToken ?? "var(--cat-{$color}, var(--text))";
@endphp

<div class="kpi-card card h-100 border-0 shadow-sm" style="--bs-card-border-color: {{ $iconBg }};">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="kpi-icon rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: {{ $iconBg }};">
                        <i class="{{ $icon }} fs-4" style="color: {{ $iconColor }};"></i>
                    </div>
                    <h6 class="mb-0 text-muted fw-semibold">{{ $label }}</h6>
                </div>
                <div class="kpi-value fs-2 fw-bold" style="color: {{ $valueColor }};" data-stat-value>{{ $value }}</div>
            </div>
            {{ $slot ?? '' }}
        </div>
    </div>
</div>
