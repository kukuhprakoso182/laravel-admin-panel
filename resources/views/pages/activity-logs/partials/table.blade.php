<x-molecules.data-table
    :columns="[
        ['key' => 'created_at', 'label' => 'Waktu'],
        ['key' => 'causer', 'label' => 'User', 'sortable' => false],
        ['key' => 'event', 'label' => 'Event'],
        ['key' => 'description', 'label' => 'Deskripsi', 'sortable' => false],
        ['key' => 'subject_type', 'label' => 'Subject'],
    ]"
    rows-var="logs"
    row-key="id"
    empty-text="Belum ada aktivitas."
    show-number
    exportable
    export-fn="exportData"
    exporting-var="exporting"
    export-label="Export CSV"
    :has-actions="true"
>
    <x-slot:cell_created_at>
        <span class="text-gray-500 whitespace-nowrap" x-text="formatDate(row.created_at)"></span>
    </x-slot:cell_created_at>

    <x-slot:cell_causer>
        <span class="text-gray-700" x-text="row.causer?.name ?? 'System'"></span>
    </x-slot:cell_causer>

    <x-slot:cell_event>
        <span class="inline-flex items-center py-1 px-2.5 rounded-full text-xs font-medium"
            x-bind:class="{
                'bg-green-100 text-green-700': row.event === 'created',
                'bg-blue-100 text-blue-700': row.event === 'updated',
                'bg-red-100 text-red-700': row.event === 'deleted',
            }"
            x-text="{ created: 'Dibuat', updated: 'Diperbarui', deleted: 'Dihapus' }[row.event] || row.event">
        </span>
    </x-slot:cell_event>

    <x-slot:cell_description>
        <span class="text-gray-500" x-text="row.description"></span>
    </x-slot:cell_description>

    <x-slot:cell_subject_type>
        <span class="text-gray-500" x-text="row.subject_type ? `${row.subject_type} #${row.subject_id}` : '-'"></span>
    </x-slot:cell_subject_type>

    <x-slot:actions>
        <button type="button" @click="openDetail(row)"
                class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-blue-600 cursor-pointer" title="Lihat Detail">
            <i class="ri-eye-line ri-lg"></i>
        </button>
    </x-slot:actions>
</x-molecules.data-table>
