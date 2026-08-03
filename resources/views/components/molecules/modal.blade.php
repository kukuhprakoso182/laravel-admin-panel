@props([
    'show' => 'show',
    'maxWidth' => 'max-w-lg',
])

<div x-show="{{ $show }}" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     x-transition.opacity>
    <div {{ $attributes->merge(['class' => "w-full {$maxWidth} bg-white rounded-2xl shadow-xl p-6 max-h-[90vh] overflow-y-auto"]) }}>

        @isset($title)
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                <button type="button" @click="{{ $show }} = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i class="ri-close-line ri-lg"></i>
                </button>
            </div>
        @endisset

        {{ $slot }}

        @isset($footer)
            <div class="flex items-center justify-end gap-x-2 pt-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
