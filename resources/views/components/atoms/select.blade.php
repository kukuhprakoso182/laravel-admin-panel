@props([
    'options' => [],       // array of ['value' => ..., 'label' => ...] — mode statis
    'optionsVar' => null,  // nama variabel Alpine reaktif di scope luar, mis. 'provinsiOptions'
    'placeholder' => null,
    'model' => null,       // wajib: expression Alpine, misal 'filters.role'
    'onChange' => null,    // opsional: expression Alpine dijalankan setelah pilihan berubah
])

@php
    $optionsJson = collect($options)->values()->toJson();
@endphp

<div
    x-data="{
        open: false,
        search: '',
        dropdownStyle: '',
        options: {{ $optionsJson }},
        get filtered() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        get selectedLabel() {
            const found = this.options.find(o => String(o.value) === String({{ $model }}));
            return found ? found.label : {{ $placeholder ? "'".addslashes($placeholder)."'" : "''" }};
        },
        openDropdown() {
            this.open = true;
            this.$nextTick(() => {
                const rect = this.$refs.trigger.getBoundingClientRect();
                this.dropdownStyle = `top: ${rect.bottom + 4}px; left: ${rect.left}px; width: ${rect.width}px;`;
                this.$refs.searchInput.focus();
            });
        },
        select(value) {
            {{ $model }} = value;
            this.open = false;
            this.search = '';
            {{ $onChange ?? '' }}
        },
    }"
    @if($optionsVar)
        x-effect="options = {{ $optionsVar }}"
    @endif
    x-on:click.outside="open = false"
    x-on:scroll.document.capture="open = false"
    x-on:resize.window="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <button
        type="button"
        x-ref="trigger"
        x-on:click="open ? (open = false) : openDropdown()"
        class="w-full flex items-center justify-between gap-2 py-2.5 px-4 rounded-lg border border-gray-200 text-sm text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 cursor-pointer"
    >
        <span x-text="selectedLabel" class="truncate"></span>
        <svg class="size-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        x-bind:style="dropdownStyle"
        class="fixed z-50 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
    >
        <div class="p-2 border-b border-gray-100">
            <input
                type="text"
                x-ref="searchInput"
                x-model="search"
                placeholder="Cari..."
                x-on:keydown.escape="open = false"
                class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>
        <ul class="max-h-56 overflow-y-auto py-1">
            @if($placeholder)
                <li
                    x-on:click="select('')"
                    x-bind:class="String({{ $model }}) === '' ? 'text-blue-600 font-medium' : 'text-gray-700'"
                    class="px-4 py-2 text-sm cursor-pointer hover:bg-gray-50"
                >{{ $placeholder }}</li>
            @endif
            <template x-for="option in filtered" :key="option.value">
                <li
                    x-on:click="select(option.value)"
                    x-text="option.label"
                    x-bind:class="String({{ $model }}) === String(option.value) ? 'text-blue-600 font-medium' : 'text-gray-700'"
                    class="px-4 py-2 text-sm cursor-pointer hover:bg-gray-50"
                ></li>
            </template>
            <li x-show="filtered.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ditemukan</li>
        </ul>
    </div>
</div>
