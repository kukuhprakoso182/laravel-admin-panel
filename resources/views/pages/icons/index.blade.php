<x-layouts.app title="Icon Management">

    <div data-module="icon-management" x-data="iconManagement()" x-init="init()">

        <x-molecules.table-toolbar
            menu="icons.index"
            create-fn="openCreate()"
            create-label="Tambah Icon"
            search-placeholder="Cari value atau section..."
            on-search="filters.page = 1; fetchData()"
        />

        @include('pages.icons.partials.table')

        @include('pages.icons.partials.form-modal')

    </div>
</x-layouts.app>
