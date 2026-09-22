@props([
    'title',
    'subtitle' => null,
    'actions' => null,
    'hideTitle' => false,
])

<div class="page-header">
    <div class="page-header__copy">
        @unless($hideTitle || $__env->hasSection('title'))
            <h1 class="page-header__title">{{ $title }}</h1>
        @endunless
        @if ($subtitle)
            <p class="page-header__subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if (!empty($actions))
        <div class="page-header__actions">
            {{ $actions }}
        </div>
    @endif
</div>
