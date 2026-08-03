<x-molecules.data-table
    :columns="[
        ['key' => 'name', 'label' => 'Nama'],
        ['key' => 'description', 'label' => 'Deskripsi', 'sortable' => false],
    ]"
    rows-var="permissions"
    loading-var="loading"
    meta-var="meta"
    filters-var="filters"
    default-sort="name"
    default-direction="asc"
    empty-text="Tidak ada data permission."
    show-number
>
    <x-slot:cell_description>
        <span x-text="row.description || '-'"></span>
    </x-slot:cell_description>

    <x-slot:actions>
        <x-molecules.table-row-actions
            edit-fn="openEdit(row)"
            delete-label="row.name"
            delete-url="`/permissions/${row.id}`"
            on-delete-success="selected = selected.filter(id => id !== row.id); fetchData();"
        />
    </x-slot:actions>
</x-molecules.data-table>
