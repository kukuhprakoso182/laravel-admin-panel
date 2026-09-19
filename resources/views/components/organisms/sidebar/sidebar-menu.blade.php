@php
    $resolveItem = function (array $item) use (&$resolveItem) {
        $item['href'] = \Illuminate\Support\Facades\Route::has($item['route'] ?? '')
            ? route($item['route'])
            : '#';
        $item['active'] = isset($item['route']) && request()->routeIs($item['route']);

        if (!empty($item['submenu'])) {
            $item['submenu'] = array_map($resolveItem, $item['submenu']);
            $item['hasActiveChild'] = collect($item['submenu'])->contains(
                fn ($sub) => ($sub['active'] ?? false) || ($sub['hasActiveChild'] ?? false)
            );
        }

        return $item;
    };

    $menu = collect($sidebarSections ?? [])->map(fn ($group) => [
        'title' => $group['title'],
        'items' => array_map($resolveItem, $group['items']),
    ]);

    // Cari id menu yang harus otomatis terbuka saat halaman pertama kali dimuat
    // (submenu yang mengandung route aktif saat ini)
    $findInitialOpenId = function ($items) use (&$findInitialOpenId) {
        foreach ($items as $item) {
            if (!empty($item['submenu'])) {
                if (!empty($item['hasActiveChild'])) {
                    return $item['id'];
                }
                if ($found = $findInitialOpenId($item['submenu'])) {
                    return $found;
                }
            }
        }
        return null;
    };

    $initialOpenId = null;
    foreach ($menu as $group) {
        if ($id = $findInitialOpenId($group['items'])) {
            $initialOpenId = $id;
            break;
        }
    }
@endphp

<nav
    x-data="{
        openMenu: @js($initialOpenId),

        // Scroll container nav supaya elemen `el` berada di paling atas
        scrollToTop(nav, el, delay = 300) {
            setTimeout(() => {
                const offset = el.getBoundingClientRect().top - nav.getBoundingClientRect().top;
                nav.scrollTo({ top: nav.scrollTop + offset - 16, behavior: 'smooth' });
            }, delay);
        }
    }"
    x-init="
        const active = $el.querySelector('[data-active=true]');
        if (active) scrollToTop($el, active, 350);
    "
    class="flex-1 overflow-y-auto px-3 py-4 space-y-6"
>
    @foreach($menu as $group)
        <x-organisms.sidebar.sidebar-group :title="$group['title']" :items="$group['items']" />
    @endforeach
</nav>
