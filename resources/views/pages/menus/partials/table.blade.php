<x-molecules.data-table
    :columns="[
        ['key' => 'name', 'label' => 'Nama'],
        ['key' => 'parent', 'label' => 'Parent', 'sortable' => false],
        ['key' => 'link', 'label' => 'Link'],
        ['key' => 'order', 'label' => 'Urutan', 'align' => 'right'],
        ['key' => 'is_active', 'label' => 'Status'],
    ]"
    rows-var="menus"
    row-key="id"
    empty-text="Tidak ada data menu."
    show-number
>
    <x-slot:cell_name>
        <div class="flex items-center gap-x-2">
            <i :class="row.icon?.value" class="text-gray-500" x-show="row.icon"></i>
            <span class="font-medium text-gray-900" x-text="row.name"></span>
        </div>
    </x-slot:cell_name>

    <x-slot:cell_parent>
        <span class="text-gray-500" x-text="row.parent?.name || '-'"></span>
    </x-slot:cell_parent>

    <x-slot:cell_link>
        <span class="text-gray-500" x-text="row.link || '-'"></span>
    </x-slot:cell_link>

    <x-slot:cell_order>
        <span class="text-gray-500" x-text="row.order"></span>
    </x-slot:cell_order>

    <x-slot:cell_is_active>
        <x-atoms.status-badge value="row.is_active" true-value="true" true-label="Aktif" false-label="Nonaktif" />
    </x-slot:cell_is_active>

    <x-slot:actions>
        <x-molecules.table-row-actions
            edit-fn="openEdit(row)"
            delete-label="row.name"
            delete-url="`/menus/${row.id}`"
            on-delete-success="selected = selected.filter(id => id !== row.id); fetchData();"
        />
    </x-slot:actions>
</x-molecules.data-table>
