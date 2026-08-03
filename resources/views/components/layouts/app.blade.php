@props([
    'title' => 'Dashboard',
    'header' => $title,
])

<x-layouts.base :title="$title">
    <div x-data="{ sidebarOpen: false }" class="h-screen flex gap-3 bg-gray-100 p-3 overflow-hidden">

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

        <div
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 w-64 p-3 transition-transform duration-200 ease-in-out
                   lg:static lg:translate-x-0 lg:p-0 lg:h-full"
        >
            <x-organisms.sidebar.sidebar />
        </div>

        <div class="flex-1 flex flex-col min-w-0 gap-3 min-h-0">
            <x-organisms.navbar class="shrink-0" />

            <main class="flex-1 flex flex-col min-h-0 rounded-4xl bg-white shadow-2xl overflow-hidden">
                <div class="shrink-0 px-4 pt-4 lg:px-6 lg:pt-6 pb-3">
                    <x-atoms.span color="gray" size="lg" weight="bold">{{ $header }}</x-atoms.span>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0 px-4 pb-4 lg:px-6 lg:pb-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-layouts.base>
