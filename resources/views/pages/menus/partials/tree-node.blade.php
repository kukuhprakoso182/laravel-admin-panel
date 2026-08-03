@props(['depth' => 0, 'maxDepth' => 6])

<li class="list-none">
    <div class="group flex items-center gap-x-2 py-2 pr-2 rounded-lg hover:bg-gray-50"
         :style="`padding-left: ${ {{ $depth }} * 24 + 8 }px`">

        <button type="button"
            x-show="node.children && node.children.length"
            x-cloak
            @click="toggleNode(node.id)"
            class="shrink-0 size-5 flex items-center justify-center text-gray-400 hover:text-gray-600">
            <i class="ri-arrow-down-s-line" x-show="isExpanded(node.id)"></i>
            <i class="ri-arrow-right-s-line" x-show="!isExpanded(node.id)"></i>
        </button>
        <span class="shrink-0 size-5" x-show="!(node.children && node.children.length)"></span>

        <i class="ri-lg text-gray-500 shrink-0" :class="node.icon?.value || 'ri-file-line'"></i>

        <span class="text-sm font-medium text-gray-700" x-text="node.name"></span>
        <span x-show="node.link" class="text-xs text-gray-400 font-mono" x-text="node.link"></span>

        <span
            class="text-[10px] px-1.5 py-0.5 rounded-full font-medium"
            :class="node.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400'"
            x-text="node.is_active ? 'Aktif' : 'Nonaktif'"
        ></span>

        <span class="flex-1"></span>

        <div class="opacity-0 group-hover:opacity-100 transition flex items-center gap-x-1 shrink-0">
            <button type="button" @click="openCreate(); form.parent_id = node.id"
                title="Tambah sub-menu"
                class="size-7 flex items-center justify-center rounded-md text-gray-400 hover:text-teal-600 hover:bg-teal-50">
                <i class="ri-add-line"></i>
            </button>
            <button type="button" @click="openEdit(node)"
                title="Edit"
                class="size-7 flex items-center justify-center rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50">
                <i class="ri-pencil-line"></i>
            </button>
            <button type="button" @click="deleteMenu(node)"
                title="Hapus"
                class="size-7 flex items-center justify-center rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    </div>

    @if($depth < $maxDepth)
        <template x-if="node.children && node.children.length && isExpanded(node.id)">
            <ul>
                <template x-for="node in node.children" :key="node.id">
                    @include('pages.menus.partials.tree-node', ['depth' => $depth + 1, 'maxDepth' => $maxDepth])
                </template>
            </ul>
        </template>
    @endif
</li>
