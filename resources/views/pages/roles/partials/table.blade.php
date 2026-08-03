<x-molecules.data-table
    :columns="[
        ['key' => 'name', 'label' => 'Nama Role'],
        ['key' => 'description', 'label' => 'Deskripsi', 'sortable' => false],
        ['key' => 'users_count', 'label' => 'Jumlah User', 'align' => 'right'],
        ['key' => 'created_at', 'label' => 'Dibuat'],
    ]"
    rows-var="roles"
    row-key="id"
    empty-text="Tidak ada data role."
    show-number
>
    <x-slot:bulkActions>
        <x-atoms.button color="red" variant="outline" @click="bulkDelete()" x-bind:disabled="submitting">
            <i class="ri-delete-bin-line"></i>
            Hapus Terpilih
        </x-atoms.button>
    </x-slot:bulkActions>

    <x-slot:cell_name>
        <span class="font-medium text-gray-900" x-text="row.name"></span>
    </x-slot:cell_name>

    <x-slot:cell_description>
        <span class="text-gray-500" x-text="row.description || '-'"></span>
    </x-slot:cell_description>

    <x-slot:cell_users_count>
        <x-atoms.badge color="blue"><span x-text="row.users_count"></span></x-atoms.badge>
    </x-slot:cell_users_count>

    <x-slot:cell_created_at>
        <span class="text-gray-500" x-text="formatDate(row.created_at)"></span>
    </x-slot:cell_created_at>

    <x-slot:actions>
        <x-atoms.button
            type="button"
            variant="ghost"
            color="teal"
            @click="openPermissionMatrix(row)"
            title="Kelola Permission"
            class="p-2! my-0!"
        >
            <i class="ri-shield-keyhole-line"></i>
        </x-atoms.button>
        <x-molecules.table-row-actions
            edit-fn="openEdit(row)"
            delete-label="row.name"
            delete-url="`/roles/${row.id}`"
            on-delete-success="selected = selected.filter(id => id !== row.id); fetchData();"
        />
    </x-slot:actions>
</x-molecules.data-table>
