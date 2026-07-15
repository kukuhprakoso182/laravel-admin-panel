{{--
    Pakai: <x-sidebar.sidebar-group title="Store" :items="$storeItems" />
    $items = array of associative array sesuai props sidebar-item
    (label, icon, href, active, badge, submenu)
--}}
@props(['title', 'items'])

<div class="sidebar__group">
    <span class="sidebar__group-title">{{ $title }}</span>
    <ul class="sidebar__list">
        @foreach($items as $item)
            <x-sidebar.sidebar-item
                :label="$item['label']"
                :icon="$item['icon'] ?? null"
                :href="$item['href'] ?? '#'"
                :active="$item['active'] ?? false"
                :badge="$item['badge'] ?? null"
                :submenu="$item['submenu'] ?? null"
            />
        @endforeach
    </ul>
</div>
