@props([
    'selectedVar' => 'selected',
])

<div x-show="{{ $selectedVar }}.length > 0"
     x-transition
     class="flex items-center justify-between px-5 py-3 bg-blue-50 border-b border-blue-100 text-sm">
    <span class="text-blue-700 font-medium">
        <span x-text="{{ $selectedVar }}.length"></span> data dipilih
    </span>
    <div class="flex items-center gap-x-2">
        {{ $slot }}
        <button type="button"
                @click="{{ $selectedVar }} = []"
                class="text-gray-500 hover:text-gray-700 px-2 cursor-pointer">
            Batal
        </button>
    </div>
</div>
