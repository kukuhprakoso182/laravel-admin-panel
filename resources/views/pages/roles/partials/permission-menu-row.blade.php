@php
    $depth = $depth ?? 0;
    $maxDepth = $maxDepth ?? 6;
@endphp

<div class="contents">
    <div class="contents hover:bg-gray-50">
        <div class="py-2 pr-3 border-b border-gray-50" style="padding-left: {{ $depth * 20 }}px">
            <div class="flex items-center gap-x-1.5">
                <i class="ri-lg text-gray-400" :class="node.icon?.value || 'ri-file-line'"></i>
                <span class="text-gray-700" x-text="node.name"></span>
            </div>
        </div>
        <template x-for="perm in permissions" :key="perm.id">
            <div class="py-2 px-2 text-center border-b border-gray-50">
                <input type="checkbox"
                    :checked="isChecked(node.id, perm.id)"
                    @change="togglePermission(node.id, perm.id)"
                    class="rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
            </div>
        </template>
    </div>

    @if($depth < $maxDepth)
        <template x-if="node.children && node.children.length">
            <template x-for="node in node.children" :key="node.id">
                @include('pages.roles.partials.permission-menu-row', ['depth' => $depth + 1, 'maxDepth' => $maxDepth])
            </template>
        </template>
    @endif
</div>
