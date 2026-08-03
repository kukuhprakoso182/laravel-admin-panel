<x-molecules.data-table
    :columns="[
        ['key' => 'user', 'label' => 'User'],
        ['key' => 'roles', 'label' => 'Role'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'created_at', 'label' => 'Bergabung'],
    ]"
    rows-var="users"
    row-key="id"
    empty-text="Tidak ada data user."
    show-number
    exportable
    export-fn="exportData"
    exporting-var="exporting"
    export-label="Export CSV"
>

    <x-slot:cell_user>
        <div class="flex items-center gap-x-3">
            <x-atoms.avatar-text name-var="row.name" size="sm" />
            <div>
                <div class="font-medium text-gray-900" x-text="row.name"></div>
                <div class="text-xs text-gray-400" x-text="row.email"></div>
            </div>
        </div>
    </x-slot:cell_user>

    <x-slot:cell_roles>
        <div class="flex flex-wrap gap-1">
            <template x-for="role in row.roles" :key="role.id">
                <x-atoms.badge color="blue"><span x-text="role.name"></span></x-atoms.badge>
            </template>
        </div>
    </x-slot:cell_roles>

    <x-slot:cell_status>
        <x-atoms.status-badge value="row.status" />
    </x-slot:cell_status>

    <x-slot:cell_created_at>
        <span class="text-gray-500" x-text="formatDate(row.created_at)"></span>
    </x-slot:cell_created_at>

    <x-slot:actions>
        <x-molecules.table-row-actions
            edit-fn="openEdit(row)"
            delete-label="row.name"
            delete-url="`/users/${row.id}`"
            on-delete-success="selected = selected.filter(id => id !== row.id); fetchData();"
        />
    </x-slot:actions>
</x-molecules.data-table>
