@php
    // Ambil dari config/sidebar.php, resolve 'route' jadi 'href' + hitung 'active'
    // di sini supaya sidebar-group / sidebar-item tetap dumb component.
    $resolveItem = function (array $item) use (&$resolveItem) {
        $item['href'] = \Illuminate\Support\Facades\Route::has($item['route'] ?? '')
            ? route($item['route'])
            : '#';
        $item['active'] = isset($item['route']) && request()->routeIs($item['route']);

        if (isset($item['submenu'])) {
            $item['submenu'] = array_map(
                fn ($sub) => [
                    'label' => $sub['label'],
                    'href' => \Illuminate\Support\Facades\Route::has($sub['route'] ?? '')
                        ? route($sub['route'])
                        : '#',
                ],
                $item['submenu'],
            );
        }

        return $item;
    };

    $menu = collect(config('sidebar'))->map(fn ($group) => [
        'title' => $group['title'],
        'items' => array_map($resolveItem, $group['items']),
    ]);
@endphp

<nav class="sidebar__menu">
    @foreach($menu as $group)
        <x-sidebar.sidebar-group :title="$group['title']" :items="$group['items']" />
    @endforeach
</nav>
