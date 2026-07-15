{{--
    Satu item menu sidebar. Support 3 varian:
    - item biasa: <x-sidebar.sidebar-item label="Dashboard" icon="dashboard-line" href="/" active />
    - item + badge: <x-sidebar.sidebar-item label="Orders" icon="shopping-bag-3-line" href="/orders" :badge="8" />
    - item + submenu: <x-sidebar.sidebar-item label="Reports" icon="bar-chart-2-line" href="/reports" :submenu="[...]" />
      submenu = array of ['label' => ..., 'href' => ...]
--}}
@props([
    'href' => '#',
    'icon' => null,
    'label',
    'active' => false,
    'badge' => null,
    'submenu' => null,
    'id' => null,
])

@php($submenuId = $id ?? 'nav-' . \Illuminate\Support\Str::slug($label))

<li class="sidebar__item" @if($submenu) data-state="closed" @endif>
    <button
        class="sidebar__button"
        href="{{ $href }}"
        data-stisla-sidebar-submenu-toggle
        aria-expanded="false"
        aria-controls="{{ $submenuId }}"
        @if($active) aria-current="page" @endif>
        @if($icon)
            <x-ui.icon :name="$icon" />
        @endif
        <span>{{ $label }}</span>
        @if($submenu)
        <span class="sidebar__caret"></span>
        @endif
    </button>

    @if($badge)
        <span class="sidebar__item-action">
            <span class="badge badge--primary">{{ $badge }}</span>
        </span>
    @endif

    @if($submenu)
        <div class="sidebar__submenu" id="{{ $submenuId }}">
            <ul class="sidebar__list">
                @foreach($submenu as $sub)
                    <li class="sidebar__item">
                        <a class="sidebar__button" href="{{ $sub['href'] }}">
                            <span>{{ $sub['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</li>
