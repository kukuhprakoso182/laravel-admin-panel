<div class="relative cursor-pointer" x-data="{ showTooltip: false }">
    <div
        @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
        class="flex items-center gap-x-1.5 rounded-full px-2.5 py-1.5 text-xs font-medium"
        :class="isOnline ? 'text-emerald-600' : 'text-red-600'"
    >
        <span class="relative flex size-2">
            <span
                x-show="isOnline"
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
            ></span>
            <span
                class="relative inline-flex rounded-full size-2"
                :class="isOnline ? 'bg-emerald-500' : 'bg-red-500'"
            ></span>
        </span>
        <span class="hidden sm:inline" x-text="isOnline ? 'Online' : 'Offline'"></span>
    </div>

    <div
        x-show="showTooltip"
        x-cloak x-transition
        class="absolute inset-e-0 top-full mt-1 whitespace-nowrap rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-600 shadow-lg z-50"
    >
        <span x-show="isOnline">Terhubung ke internet</span>
        <span x-show="!isOnline">Tidak ada koneksi internet</span>
    </div>
</div>
