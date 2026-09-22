@php
    $navigationItems = \App\Models\NavigationItem::query()
        ->with('module')
        ->where('active', true)
        ->orderBy('section')
        ->orderBy('sort_order')
        ->get()
        ->filter(fn ($item) => ! $item->admin_only || auth()->user()->isAdmin())
        ->filter(fn ($item) => ! $item->module || auth()->user()->canAccessModule($item->module->slug, $item->permission_action))
        ->groupBy('section');
@endphp

@foreach ($navigationItems as $section => $items)
    <div class="nav-group-title">{{ $section }}</div>
    <ul class="app-nav">
        @foreach ($items as $item)
            <li class="nav-item">
                <a href="{{ route($item->route_name) }}"
                   data-tooltip="{{ $item->label }}"
                   class="nav-link {{ request()->routeIs($item->route_name) || request()->routeIs($item->route_name . '.*') ? 'active' : '' }}"
                   title="{{ $item->label }}">
                    <i class="bi {{ $item->icon }}"></i><span class="nav-label">{{ $item->label }}</span>
                </a>
            </li>
        @endforeach
    </ul>
@endforeach