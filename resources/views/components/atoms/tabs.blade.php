@props([
    'tabs' => [],
    'model',
    'onChange' => null,
])

<div class="border-b border-line-200">
    <nav class="-mb-px flex gap-x-6 overflow-x-auto" aria-label="Tabs">
        @foreach ($tabs as $tab)
            <button
                type="button"
                @click="{{ $model }} = '{{ $tab['key'] }}'{{ $onChange ? '; ' . $onChange : '' }}"
                :class="{{ $model }} === '{{ $tab['key'] }}'
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-ink-500 hover:text-ink-700 hover:border-line-300'"
                class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium transition-colors focus:outline-none cursor-pointer"
            >
                {{ $tab['label'] }}

                @isset($tab['badge'])
                    <span
                        class="ml-1.5 rounded-full bg-surface-100 px-2 py-0.5 text-xs font-normal text-ink-600"
                        x-text="{{ $tab['badge'] }}"
                    ></span>
                @endisset
            </button>
        @endforeach
    </nav>
</div>
