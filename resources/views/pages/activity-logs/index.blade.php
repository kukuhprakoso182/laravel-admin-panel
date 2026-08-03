<x-layouts.app title="Log Aktivitas">

    <div data-module="activity-log-management" x-data="activityLogManagement()" x-init="init()">

        <x-molecules.table-toolbar
            menu="activity-logs.index"
            search-placeholder="Cari deskripsi, event, atau IP..."
            on-search="filters.page = 1; fetchData()"
        >
            <x-slot:filters>
                <x-atoms.select
                    model="filters.event"
                    on-change="filters.page = 1; fetchData()"
                    :options="[
                        ['value' => 'created', 'label' => 'Dibuat'],
                        ['value' => 'updated', 'label' => 'Diperbarui'],
                        ['value' => 'deleted', 'label' => 'Dihapus'],
                    ]"
                    placeholder="Semua Aktivitas"
                />
            </x-slot:filters>
        </x-molecules.table-toolbar>

        @include('pages.activity-logs.partials.table')

        @include('pages.activity-logs.partials.detail-modal')

    </div>
</x-layouts.app>
