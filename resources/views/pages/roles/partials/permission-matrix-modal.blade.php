<x-molecules.modal show="showPermissionModal" max-width="max-w-2xl">

    <x-slot:title>
        <span x-text="`Kelola Permission — ${matrixRole?.name ?? ''}`"></span>
    </x-slot:title>

    <div class="min-h-75 max-h-[60vh] overflow-y-auto">

        <template x-if="loadingMatrix">
            <div class="flex items-center justify-center py-16 text-gray-400">
                <span class="animate-spin inline-block size-5 border-2 border-blue-500 border-t-transparent rounded-full"></span>
            </div>
        </template>

        <template x-if="!loadingMatrix && matrixMenus.length === 0">
            <div class="text-center py-16 text-gray-400">Belum ada menu.</div>
        </template>

        <template x-if="!loadingMatrix && matrixMenus.length > 0">
            <div class="text-sm" style="display: grid; grid-template-columns: 1fr repeat(5, auto);">
                {{-- Header --}}
                <div class="contents text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <div class="py-2 pr-3 border-b border-gray-100">Menu</div>
                    <template x-for="perm in permissions" :key="perm.id">
                        <div class="py-2 px-2 text-center whitespace-nowrap border-b border-gray-100" x-text="perm.name"></div>
                    </template>
                </div>

                {{-- Rows --}}
                <template x-for="node in matrixMenus" :key="node.id">
                    @include('pages.roles.partials.permission-menu-row', ['depth' => 0])
                </template>
            </div>
        </template>

    </div>

    <x-slot:footer>
        <x-atoms.button color="gray" variant="outline" type="button" @click="showPermissionModal = false">
            Batal
        </x-atoms.button>
        <x-atoms.button color="teal" type="button" @click="savePermissionMatrix()" x-bind:disabled="savingMatrix">
            <span x-text="savingMatrix ? 'Menyimpan...' : 'Simpan'"></span>
        </x-atoms.button>
    </x-slot:footer>

</x-molecules.modal>
