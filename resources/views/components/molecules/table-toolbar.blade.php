@props([
    'searchModel' => 'filters.search',
    'searchPlaceholder' => 'Cari...',
    'onSearch' => "filters.page = 1; fetchData()",
    'menu' => null,           // nama route untuk cek permission, misal 'users.index'. Kosongkan untuk skip pengecekan (perilaku lama).
    'createFn' => null,       // expression Alpine, misal 'openCreate()'. Kosongkan kalau halaman ini tidak ada tombol tambah.
    'createLabel' => 'Tambah Data',
])

@php
    $onSearchExpr = $onSearch ?? "filters.page = 1; fetchData ? fetchData() : (typeof fetchData === 'function' && fetchData())";
    $canCreate = $createFn && (! $menu || auth()->user()?->can("{$menu}:create"));
@endphp

<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
    <div class="flex-1 max-w-sm">
        <div class="relative">
            <div class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4-4"/>
                </svg>
            </div>
            <input
                type="text"
                x-model="{{ $searchModel }}"
                x-on:input.debounce.400ms="{{ $onSearchExpr }}"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full ps-10 pe-9 py-2.5 rounded-lg border border-gray-200 text-sm
                       placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
            <button
                type="button"
                x-show="{{ $searchModel }}"
                x-cloak
                x-on:click="{{ $searchModel }} = ''; {{ $onSearchExpr }}"
                class="absolute inset-y-0 inset-e-0 flex items-center pe-3 text-gray-400 hover:text-gray-600 cursor-pointer"
            >
                <i class="ri-close-line ri-lg"></i>
            </button>
        </div>
    </div>

    {{ $filters ?? '' }}

    <div class="sm:ms-auto flex items-center gap-2">
        @if($canCreate)
            <x-atoms.button type="button" @click="{{ $createFn }}">
                <i class="ri-add-line"></i>
                {{ $createLabel }}
            </x-atoms.button>
        @endif

        {{ $action ?? '' }}
    </div>
</div>
