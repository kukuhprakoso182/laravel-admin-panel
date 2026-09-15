<x-molecules.modal show="showPermissionModal" max-width="max-w-2xl">

    <x-slot:title>
        <span x-text="`Kelola Permission — ${matrixRole?.name ?? ''}`"></span>
    </x-slot:title>

    <div class="min-h-75">

        <template x-if="loadingMatrix">
            <div class="flex items-center justify-center py-16 text-gray-400">
                <span class="animate-spin inline-block size-5 border-2 border-blue-500 border-t-transparent rounded-full"></span>
            </div>
        </template>

        <template x-if="!loadingMatrix && matrixMenus.length === 0">
            <div class="text-center py-16 text-gray-400">Belum ada menu.</div>
        </template>

        <template x-if="!loadingMatrix && matrixMenus.length > 0">
            <div class="border border-gray-100 rounded-lg overflow-auto" style="max-height: 65vh;">
                <div
                    class="text-sm grid"
                    :style="`grid-template-columns: minmax(180px, 1fr) repeat(${permissions.length}, minmax(88px, max-content)); min-width: max-content;`"
                >
                    {{-- Header: pojok kiri atas (sticky top + left) --}}
                    <div class="sticky top-0 left-0 z-20 bg-gray-50 py-2 pr-3 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        Menu
                    </div>

                    {{-- Header: kolom permission (sticky top saja) --}}
                    <template x-for="perm in permissions" :key="perm.id">
                        <div class="sticky top-0 z-10 bg-gray-50 py-1.5 px-2 border-b border-l border-gray-100 flex flex-col items-center justify-center gap-0.5 whitespace-nowrap">
                            <span
                                class="text-[10px] font-medium text-gray-400 leading-none"
                                x-show="perm.name.includes('.')"
                                x-text="perm.name.slice(0, perm.name.lastIndexOf('.')).toUpperCase()"
                            ></span>
                            <span
                                class="text-xs font-semibold text-gray-600 leading-none"
                                x-text="(perm.name.includes('.') ? perm.name.split('.').pop() : perm.name).toUpperCase()"
                            ></span>
                        </div>
                    </template>

                    {{-- Rows --}}
                    <template x-for="node in matrixMenus" :key="node.id">
                        @include('pages.roles.partials.permission-menu-row', ['depth' => 0])
                    </template>
                </div>
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
