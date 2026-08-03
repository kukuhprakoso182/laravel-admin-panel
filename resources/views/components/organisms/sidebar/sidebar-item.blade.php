@props([
    'href' => '#',
    'icon' => null,
    'label',
    'active' => false,
    'badge' => null,
    'submenu' => null,
    'id' => null,
    'hasActiveChild' => false,
])

@php
    // Fallback id kalau tidak dikirim dari data (idealnya selalu dikirim
    // dari SidebarService berbasis primary key menu supaya dijamin unik).
    $submenuId = $id ?? 'nav-' . \Illuminate\Support\Str::slug($label) . '-' . \Illuminate\Support\Str::random(6);

    // Grup dianggap "terbuka" kalau item ini sendiri aktif ATAU salah satu turunannya aktif
    $isOpen = $active || $hasActiveChild;
@endphp

@if ($submenu)
    <x-organisms.sidebar.sidebar-nav-collapse :label="$label" :id="$id">
        <x-slot:icon>
            <x-organisms.sidebar.sidebar-nav-icon :name="$icon" />
        </x-slot:icon>
        @foreach($submenu as $sub)
            <x-organisms.sidebar.sidebar-item
                :id="$sub['id'] ?? null"
                :label="$sub['label']"
                :icon="$sub['icon'] ?? null"
                :href="$sub['href'] ?? '#'"
                :active="$sub['active'] ?? false"
                :badge="$sub['badge'] ?? null"
                :submenu="$sub['submenu'] ?? null"
                :has-active-child="$sub['hasActiveChild'] ?? false"
            />
        @endforeach
    </x-organisms.sidebar.sidebar-nav-collapse>
@else
    <x-organisms.sidebar.sidebar-nav-link :href="$href" :active="$active">
        <x-organisms.sidebar.sidebar-nav-icon :name="$icon" />
        <span>{{ $label }}</span>
    </x-organisms.sidebar.sidebar-nav-link>
@endif
