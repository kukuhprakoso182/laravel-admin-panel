<div class="bg-white rounded-2xl border border-gray-200 p-4">

    <template x-if="loadingTree">
        <div class="flex items-center justify-center py-12 text-gray-400">
            <span class="animate-spin inline-block size-5 border-2 border-blue-500 border-t-transparent rounded-full"></span>
        </div>
    </template>

    <template x-if="!loadingTree && treeMenus.length === 0">
        <div class="text-center py-12 text-gray-400">Belum ada menu.</div>
    </template>

    <template x-if="!loadingTree && treeMenus.length > 0">
        <ul class="space-y-0.5">
            <template x-for="node in treeMenus" :key="node.id">
                @include('pages.menus.partials.tree-node', ['depth' => 0])
            </template>
        </ul>
    </template>

</div>
