<x-molecules.modal show="showDetailModal" max-width="max-w-xl">

    <x-slot:title>
        <span>Detail Aktivitas</span>
    </x-slot:title>

    <div class="space-y-3 text-sm" x-show="selectedLog">
        <div class="grid grid-cols-3 gap-2">
            <span class="text-gray-400">Waktu</span>
            <span class="col-span-2 text-gray-700" x-text="formatDate(selectedLog?.created_at)"></span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <span class="text-gray-400">User</span>
            <span class="col-span-2 text-gray-700" x-text="selectedLog?.causer?.name ?? 'System'"></span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <span class="text-gray-400">Event</span>
            <span class="col-span-2 text-gray-700" x-text="selectedLog?.event"></span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <span class="text-gray-400">Deskripsi</span>
            <span class="col-span-2 text-gray-700" x-text="selectedLog?.description"></span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <span class="text-gray-400">IP Address</span>
            <span class="col-span-2 text-gray-700" x-text="selectedLog?.ip_address"></span>
        </div>

        <template x-if="selectedLog?.properties">
            <div class="pt-2 border-t border-gray-100">
                <span class="text-gray-400 block mb-2">Perubahan Data</span>
                <pre class="bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto" x-text="JSON.stringify(selectedLog.properties, null, 2)"></pre>
            </div>
        </template>
    </div>

    <x-slot:footer>
        <x-atoms.button color="gray" variant="outline" type="button" @click="showDetailModal = false">
            Tutup
        </x-atoms.button>
    </x-slot:footer>

</x-molecules.modal>
