<x-layouts.app title="User Management">

    <div data-module="user-management" x-data="userManagement()" x-init="init()">

        <x-molecules.table-toolbar
            menu="users.index"
            create-fn="openCreate()"
            create-label="Tambah User"
            search-placeholder="Cari nama atau email..."
            on-search="filters.page = 1; fetchData()"
        >
            <x-slot:filters>
                <x-atoms.select
                    model="filters.role"
                    on-change="filters.page = 1; fetchData()"
                    :options="collect($roles)->map(fn ($r) => ['value' => $r['id'], 'label' => $r['name']])"
                    placeholder="Semua Role"
                />
            </x-slot:filters>
        </x-molecules.table-toolbar>

        @include('pages.users.partials.table')

        @include('pages.users.partials.form-modal', ['roles' => $roles])

    </div>
</x-layouts.app>
