<x-layouts.app title="Permission Management">

    <div data-module="permission-management" x-data="permissionManagement()" x-init="init()">

        <x-molecules.table-toolbar
            menu="permissions.index"
            create-fn="openCreate()"
            create-label="Tambah Permission"
            search-placeholder="Cari nama atau deskripsi role..."
            on-search="filters.page = 1; fetchData()"
        />

        @include('pages.permissions.partials.table')

        @include('pages.permissions.partials.form-modal')

    </div>
</x-layouts.app>
