@props([
    'placeholder' => 'Search orders, products, customers...',
    'action' => null,   // URL endpoint search, default: route('search') kalau ada
    'minChars' => 2,
    'debounce' => 300,  // ms
])

@php
    $searchUrl = $action ?? (\Illuminate\Support\Facades\Route::has('search') ? route('search') : '#');
@endphp

<div
    x-data="{
        open: false,
        loading: false,
        query: '',
        results: [],
        activeIndex: -1,
        debounceTimer: null,

        search() {
            clearTimeout(this.debounceTimer);

            if (this.query.length < {{ $minChars }}) {
                this.results = [];
                this.open = false;
                return;
            }

            this.debounceTimer = setTimeout(() => {
                this.loading = true;

                fetch(`{{ $searchUrl }}?q=${encodeURIComponent(this.query)}`, {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(res => res.json())
                    .then(data => {
                        this.results = data.results ?? [];
                        this.open = true;
                        this.activeIndex = -1;
                    })
                    .catch(() => {
                        this.results = [];
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }, {{ $debounce }});
        },

        clear() {
            this.query = '';
            this.results = [];
            this.open = false;
            this.$refs.input.focus();
        },

        onKeydown(e) {
            if (!this.open || this.results.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.activeIndex = (this.activeIndex + 1) % this.results.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.activeIndex = this.activeIndex <= 0 ? this.results.length - 1 : this.activeIndex - 1;
            } else if (e.key === 'Enter' && this.activeIndex >= 0) {
                e.preventDefault();
                window.location.href = this.results[this.activeIndex].url;
            } else if (e.key === 'Escape') {
                this.open = false;
                this.$refs.input.blur();
            }
        }
    }"
    @click.outside="open = false"
    class="relative w-full"
>
    <div class="relative">
        <div class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4-4"/>
            </svg>
        </div>

        <input
            type="text"
            x-ref="input"
            x-model="query"
            x-on:input="search()"
            x-on:keydown="onKeydown($event)"
            x-on:focus="if (results.length > 0) open = true"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="w-full ps-10 pe-10 py-2.5 rounded-full border border-gray-200 bg-gray-50 text-sm
                   placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500
                   focus:border-blue-500 focus:bg-white"
        >

        <div x-show="loading" x-cloak class="absolute inset-y-0 inset-e-0 flex items-center pe-3.5">
            <span class="animate-spin inline-block size-4 border-2 border-blue-500 border-t-transparent rounded-full"></span>
        </div>

        <button
            type="button"
            x-show="!loading && query.length > 0"
            x-cloak
            @click="clear()"
            class="absolute inset-y-0 inset-e-0 flex items-center pe-3.5 text-gray-400 hover:text-gray-600"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="absolute mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden z-50 max-h-80 overflow-y-auto"
    >
        <template x-if="!loading && results.length === 0 && query.length >= {{ $minChars }}">
            <p class="px-4 py-6 text-sm text-gray-400 text-center">
                Tidak ada hasil untuk "<span x-text="query"></span>"
            </p>
        </template>

        <template x-for="(item, index) in results" :key="item.id">

                :href="item.url"
                @mouseenter="activeIndex = index"
                class="flex items-center gap-x-3 px-4 py-2.5 text-sm transition-colors"
                :class="activeIndex === index ? 'bg-blue-50' : 'hover:bg-gray-50'"
            >
                <span class="inline-flex items-center justify-center size-8 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold shrink-0"
                      x-text="item.type_label ?? '?'"></span>

                <span class="flex-1 min-w-0">
                    <span class="block font-medium text-gray-900 truncate" x-text="item.title"></span>
                    <span x-show="item.subtitle" class="block text-xs text-gray-400 truncate" x-text="item.subtitle"></span>
                </span>
            </a>
        </template>
    </div>
</div>
