@props(['title', 'items'])
<div>
    @if($title)
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $title }}</p>
    @endif
    <div class="space-y-1">
        @foreach($items as $item)
            <x-organisms.sidebar.sidebar-item
                :id="$item['id'] ?? null"
                :label="$item['label']"
                :icon="$item['icon'] ?? null"
                :href="$item['href'] ?? '#'"
                :active="$item['active'] ?? false"
                :badge="$item['badge'] ?? null"
                :submenu="$item['submenu'] ?? null"
                :has-active-child="$item['hasActiveChild'] ?? false"
            />
        @endforeach
    </div>
</div>
