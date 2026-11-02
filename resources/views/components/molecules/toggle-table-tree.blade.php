<div class="flex items-center rounded-lg border border-line-200 p-0.5 bg-surface-50">
    <button type="button" @click="switchView('table')"
        :class="viewMode === 'table' ? 'bg-white shadow-sm text-ink-800' : 'text-ink-500'"
        class="flex items-center gap-x-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
        <i class="ri-table-line"></i> Tabel
    </button>
    <button type="button" @click="switchView('tree')"
        :class="viewMode === 'tree' ? 'bg-white shadow-sm text-ink-800' : 'text-ink-500'"
        class="flex items-center gap-x-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
        <i class="ri-node-tree"></i> Tree
    </button>
</div>
