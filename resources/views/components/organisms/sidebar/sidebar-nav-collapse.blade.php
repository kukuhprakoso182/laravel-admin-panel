@props([
    'label' => '',
    'id' => null,   // wajib unik per item, mis. item['id'] = "menu-7"
])

<div>
    <button
        type="button"
        @click="openMenu = (openMenu === '{{ $id }}' ? null : '{{ $id }}')"
        class="w-full flex items-center justify-between gap-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors cursor-pointer"
    >
        <span class="flex items-center gap-x-3">
            {{ $icon ?? '' }}
            <span>{{ $label }}</span>
        </span>
        <svg class="size-4 text-gray-400 transition-transform"
             :class="openMenu === '{{ $id }}' && 'rotate-180'"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="openMenu === '{{ $id }}'" x-collapse x-cloak class="pl-9 mt-1 space-y-1">
        {{ $slot }}
    </div>
</div>
