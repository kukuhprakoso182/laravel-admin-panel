<x-layouts.app title="Menu Management">

    <div
        data-module="menu-management"
        x-data="menuManagement()"
        x-init="init(); icons = @js($icons->map(fn ($i) => ['id' => $i->id, 'value' => $i->value]))"
    >

        <x-molecules.table-toolbar
            menu="menus.index"
            create-fn="openCreate()"
            create-label="Tambah Menu"
            search-placeholder="Cari nama atau link menu..."
            on-search="filters.page = 1; fetchData()"
        >
            <x-slot:filters>
                <x-atoms.select
                    model="filters.parent_id"
                    on-change="filters.page = 1; fetchData()"
                    :options="collect($parentMenus)->map(fn ($m) => ['value' => $m->id, 'label' => $m->name])"
                    placeholder="Semua Parent"
                    x-show="viewMode === 'table'"
                />

                <x-molecules.toggle-table-tree/>
            </x-slot:filters>
        </x-molecules.table-toolbar>

        <div x-show="viewMode === 'table'">
            @include('pages.menus.partials.table')
        </div>

        <div x-show="viewMode === 'tree'" x-cloak>
            @include('pages.menus.partials.tree')
        </div>

        @include('pages.menus.partials.form-modal', ['parentMenus' => $parentMenus, 'icons' => $icons])

    </div>
</x-layouts.app>
