@props([
    'columns' => [],
    'rowsVar' => 'users',
    'loadingVar' => 'loading',
    'metaVar' => 'meta',
    'filtersVar' => 'filters',
    'pageVar' => 'filters.page',
    'sortVar' => 'filters.sort',
    'directionVar' => 'filters.direction',
    'perPageVar' => 'filters.per_page',
    'defaultPerPage' => 10,
    'defaultSort' => '',
    'defaultDirection' => 'desc',
    'perPageOptions' => [10, 25, 50, 100],
    'fetchFn' => 'fetchData',
    'rowKey' => 'id',
    'emptyText' => 'Tidak ada data.',
    'hasActions' => true,
    'showNumber' => false,
    'selectable' => false,
    'selectedVar' => 'selected',
    'exportable' => false,
    'exportFn' => 'exportData',
    'exportingVar' => 'exporting',
    'exportLabel' => 'Export',
    'maxHeight' => '65vh', // <-- baru: tinggi area scroll data. Set null untuk nonaktifkan scroll+sticky.
])

@php
    $colspan = count($columns)
        + ($hasActions ? 1 : 0)
        + ($showNumber ? 1 : 0)
        + ($selectable ? 1 : 0);
@endphp

<div
    x-init="
        if (!('per_page' in {{ $filtersVar }})) {{ $filtersVar }}.per_page = {{ $defaultPerPage }};
        if (!('sort' in {{ $filtersVar }})) {{ $filtersVar }}.sort = '{{ $defaultSort }}';
        if (!('direction' in {{ $filtersVar }})) {{ $filtersVar }}.direction = '{{ $defaultDirection }}';
    "
    class="bg-white rounded-2xl border border-gray-200 overflow-hidden"
>

    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 text-sm text-gray-500">
        <label class="flex items-center gap-x-2">
            <span>Tampilkan</span>
            <select
                x-model.number="{{ $perPageVar }}"
                x-on:change="{{ $pageVar }} = 1; {{ $fetchFn }}()"
                class="py-1.5 px-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 cursor-pointer"
            >
                @foreach($perPageOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
        </label>

        @if($exportable)
            <x-atoms.button
                color="gray"
                type="button"
                variant="outline"
                @click="{{ $exportFn }}()"
                x-bind:disabled="{{ $exportingVar }}"
            >
                <i class="ri-download-2-line" x-show="!{{ $exportingVar }}"></i>
                <span class="animate-spin inline-block size-4 border-2 border-gray-400 border-t-transparent rounded-full" x-show="{{ $exportingVar }}"></span>
                <span x-text="{{ $exportingVar }} ? 'Mengekspor...' : '{{ $exportLabel }}'"></span>
            </x-atoms.button>
        @endif
    </div>

    @if($selectable)
        <x-molecules.bulk-action-bar :selected-var="$selectedVar">
            {{ $bulkActions ?? '' }}
        </x-molecules.bulk-action-bar>
    @endif

    <div
        class="overflow-x-auto {{ $maxHeight ? 'overflow-y-auto' : '' }}"
        @if($maxHeight) style="max-height: {{ $maxHeight }}" @endif
    >
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    @if($selectable)
                        <th class="sticky top-0 z-10 bg-white px-5 py-3 w-10 border-b border-gray-100">
                            <input type="checkbox"
                                x-ref="selectAll{{ $selectedVar }}"
                                :checked="{{ $rowsVar }}.length > 0 && {{ $rowsVar }}.every(r => {{ $selectedVar }}.includes(r.{{ $rowKey }}))"
                                x-effect="$refs.selectAll{{ $selectedVar }}.indeterminate = {{ $rowsVar }}.some(r => {{ $selectedVar }}.includes(r.{{ $rowKey }})) && !{{ $rowsVar }}.every(r => {{ $selectedVar }}.includes(r.{{ $rowKey }}))"
                                @change="
                                    const ids = {{ $rowsVar }}.map(r => r.{{ $rowKey }});
                                    if ($event.target.checked) {
                                        {{ $selectedVar }} = [...new Set([...{{ $selectedVar }}, ...ids])];
                                    } else {
                                        {{ $selectedVar }} = {{ $selectedVar }}.filter(id => !ids.includes(id));
                                    }
                                "
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                    @endif

                    @if($showNumber)
                        <th class="sticky top-0 z-10 bg-white px-5 py-3 w-14 border-b border-gray-100">No</th>
                    @endif

                    @foreach($columns as $col)
                        @php
                            $isSortable = $col['sortable'] ?? true;
                            $sortKey = $col['sortKey'] ?? $col['key'];
                            $align = ($col['align'] ?? 'left') === 'right' ? 'text-right' : '';
                        @endphp
                        <th class="sticky top-0 z-10 bg-white px-5 py-3 border-b border-gray-100 {{ $align }} {{ $isSortable ? 'cursor-pointer select-none hover:text-gray-700' : '' }}"
                            @if($isSortable)
                                x-on:click="
                                    if ({{ $sortVar }} === '{{ $sortKey }}') {
                                        {{ $directionVar }} = {{ $directionVar }} === 'asc' ? 'desc' : 'asc';
                                    } else {
                                        {{ $sortVar }} = '{{ $sortKey }}';
                                        {{ $directionVar }} = 'asc';
                                    }
                                    {{ $pageVar }} = 1;
                                    {{ $fetchFn }}();
                                "
                            @endif
                        >
                            <span class="inline-flex items-center gap-x-1">
                                {{ $col['label'] }}
                                @if($isSortable)
                                    <i class="ri-arrow-up-line text-blue-600" x-show="{{ $sortVar }} === '{{ $sortKey }}' && {{ $directionVar }} === 'asc'"></i>
                                    <i class="ri-arrow-down-line text-blue-600" x-show="{{ $sortVar }} === '{{ $sortKey }}' && {{ $directionVar }} === 'desc'"></i>
                                    <i class="ri-expand-up-down-line text-gray-300" x-show="{{ $sortVar }} !== '{{ $sortKey }}'"></i>
                                @endif
                            </span>
                        </th>
                    @endforeach

                    @if($hasActions)
                        <th class="sticky top-0 z-10 bg-white px-5 py-3 text-right border-b border-gray-100">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-if="{{ $loadingVar }}">
                    <tr>
                        <td colspan="{{ $colspan }}" class="px-5 py-12 text-center text-gray-400">
                            <span class="animate-spin inline-block size-5 border-2 border-blue-500 border-t-transparent rounded-full"></span>
                        </td>
                    </tr>
                </template>

                <template x-if="!{{ $loadingVar }} && {{ $rowsVar }}.length === 0">
                    <tr>
                        <td colspan="{{ $colspan }}" class="px-5 py-12 text-center text-gray-400">
                            {{ $emptyText }}
                        </td>
                    </tr>
                </template>

                <template x-for="(row, index) in {{ $rowsVar }}" :key="row.{{ $rowKey }}">
                    <tr class="hover:bg-gray-50" @if($selectable) :class="{{ $selectedVar }}.includes(row.{{ $rowKey }}) ? 'bg-blue-50/60' : ''" @endif>
                        @if($selectable)
                            <td class="px-5 py-3">
                                <input type="checkbox"
                                    :checked="{{ $selectedVar }}.includes(row.{{ $rowKey }})"
                                    @change="$event.target.checked
                                        ? {{ $selectedVar }}.push(row.{{ $rowKey }})
                                        : {{ $selectedVar }} = {{ $selectedVar }}.filter(id => id !== row.{{ $rowKey }})"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                        @endif

                        @if($showNumber)
                            <td class="px-5 py-3 text-gray-500" x-text="({{ $metaVar }}?.from ?? 1) + index"></td>
                        @endif

                        @foreach($columns as $col)
                            <td class="px-5 py-3 {{ ($col['align'] ?? 'left') === 'right' ? 'text-right' : '' }}">
                                @php $slotName = 'cell_' . $col['key']; @endphp
                                @isset($$slotName)
                                    {{ $$slotName }}
                                @else
                                    <span x-text="row.{{ $col['key'] }}"></span>
                                @endisset
                            </td>
                        @endforeach

                        @if($hasActions)
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-x-1">
                                    {{ $actions ?? '' }}
                                </div>
                            </td>
                        @endif
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <x-molecules.pagination
        :metaVar="$metaVar"
        :pageVar="$pageVar"
        :fetchFn="$fetchFn"
    />
</div>
