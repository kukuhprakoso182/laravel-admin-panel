@props([
    'tabs' => [],
    'model',
    'onChange' => null,
])

<div class="border-b border-gray-200">
    <nav class="-mb-px flex gap-x-6 overflow-x-auto" aria-label="Tabs">
        @foreach ($tabs as $tab)
            <button
                type="button"
                @click="{{ $model }} = '{{ $tab['key'] }}'{{ $onChange ? '; ' . $onChange : '' }}"
                :class="{{ $model }} === '{{ $tab['key'] }}'
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium transition-colors focus:outline-none cursor-pointer"
            >
                {{ $tab['label'] }}

                @isset($tab['badge'])
                    <span
                        class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-normal text-gray-600"
                        x-text="{{ $tab['badge'] }}"
                    ></span>
                @endisset
            </button>
        @endforeach
    </nav>
</div>
