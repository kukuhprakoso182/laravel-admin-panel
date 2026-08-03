<x-molecules.data-table
    :columns="[
        ['key' => 'preview', 'label' => 'Preview', 'sortable' => false],
        ['key' => 'value', 'label' => 'Value'],
        ['key' => 'section', 'label' => 'Section'],
        ['key' => 'is_active', 'label' => 'Status'],
    ]"
    rows-var="icons"
    row-key="id"
    empty-text="Tidak ada data icon."
    show-number
>
    <x-slot:cell_preview>
        <i :class="row.value" class="ri-lg text-gray-600"></i>
    </x-slot:cell_preview>

    <x-slot:cell_value>
        <span class="font-medium text-gray-900" x-text="row.value"></span>
    </x-slot:cell_value>

    <x-slot:cell_section>
        <x-atoms.badge color="gray"><span x-text="row.section"></span></x-atoms.badge>
    </x-slot:cell_section>

    <x-slot:cell_is_active>
        <x-atoms.status-badge value="row.is_active" true-value="true" true-label="Aktif" false-label="Nonaktif" />
    </x-slot:cell_is_active>

    <x-slot:actions>
        <x-molecules.table-row-actions
            edit-fn="openEdit(row)"
            delete-label="row.value"
            delete-url="`/icons/${row.id}`"
            on-delete-success="selected = selected.filter(id => id !== row.id); fetchData();"
        />
    </x-slot:actions>
</x-molecules.data-table>
