@php
    $depth = $depth ?? 0;
    $maxDepth = $maxDepth ?? 6;
@endphp

<div class="contents group">
    <div class="contents">
        <div
            class="py-2 pr-3 border-b border-line-50 sticky left-0 z-[5] bg-white group-hover:bg-surface-50"
            style="padding-left: {{ $depth * 20 }}px"
        >
            <div class="flex items-center gap-x-1.5">
                <i class="ri-lg text-ink-400" :class="node.icon?.value || 'ri-file-line'"></i>
                <span class="text-ink-700 whitespace-nowrap" x-text="node.name"></span>
            </div>
        </div>
        <template x-for="perm in permissions" :key="perm.id">
            <div class="py-2 px-2 text-center border-b border-l border-line-50 group-hover:bg-surface-50">
                <input type="checkbox"
                    :checked="isChecked(node.id, perm.id)"
                    @change="togglePermission(node.id, perm.id)"
                    class="rounded border-line-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
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
