<x-layouts.app title="Role Management">

    <div
        data-module="role-management"
        x-data="roleManagement()"
        x-init="init(); permissions = @js($permissions->map(fn ($p) => ['id' => $p->id, 'name' => $p->name]))"
    >

        <x-molecules.table-toolbar
            menu="roles.index"
            create-fn="openCreate()"
            create-label="Tambah Role"
            search-placeholder="Cari nama atau deskripsi role..."
            on-search="filters.page = 1; fetchData()"
        />

        @include('pages.roles.partials.table')

        @include('pages.roles.partials.form-modal')

        @include('pages.roles.partials.permission-matrix-modal')

    </div>
</x-layouts.app>
