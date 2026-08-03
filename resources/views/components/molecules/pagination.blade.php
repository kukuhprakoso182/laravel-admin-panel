@props([
    'metaVar' => 'meta',
    'pageVar' => 'filters.page',
    'fetchFn' => 'fetchData',
])

<div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 text-sm text-gray-500" x-show="{{ $metaVar }}.total > 0">
    <span>
        Menampilkan <span x-text="{{ $metaVar }}.from"></span>–<span x-text="{{ $metaVar }}.to"></span>
        dari <span x-text="{{ $metaVar }}.total"></span> data
    </span>
    <div class="flex items-center gap-x-1">
        <button type="button" :disabled="{{ $pageVar }} <= 1"
                @click="{{ $pageVar }}--; {{ $fetchFn }}()"
                class="px-3 py-1.5 rounded-lg border border-gray-200 disabled:opacity-40 disabled:pointer-events-none hover:bg-teal-50 cursor-pointer">
            Prev
        </button>
        <button type="button" :disabled="{{ $pageVar }} >= {{ $metaVar }}.last_page"
                @click="{{ $pageVar }}++; {{ $fetchFn }}()"
                class="px-3 py-1.5 rounded-lg border border-gray-200 disabled:opacity-40 disabled:pointer-events-none hover:bg-teal-50 cursor-pointer">
            Next
        </button>
    </div>
</div>
