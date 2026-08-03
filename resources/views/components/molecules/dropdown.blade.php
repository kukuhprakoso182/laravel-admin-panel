@props([
    'label' => 'Menu',
    'align' => 'left',   // left | right
])

<div x-data="{ open: false }" x-on:click.outside="open = false" class="relative inline-block text-left">
    <button
        type="button"
        x-on:click="open = !open"
        class="inline-flex items-center gap-x-2 py-2.5 px-4 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none cursor-pointer"
    >
        {{ $label }}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
             class="size-4 transition-transform" x-bind:class="open ? 'rotate-180' : ''">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        x-cloak
        {{ $attributes->merge(['class' => 'absolute z-10 mt-2 min-w-[12rem] py-1 bg-white border border-gray-200 rounded-lg shadow-md ' . ($align === 'right' ? 'right-0' : 'left-0')]) }}
    >
        {{ $slot }}
    </div>
</div>
