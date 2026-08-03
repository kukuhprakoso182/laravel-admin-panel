<header
    {{ $attributes->merge(['class' => 'h-16 flex items-center gap-x-4 px-4 lg:px-6']) }}
    x-data="networkStatus()"
    x-init="init()"
>

    <button type="button" @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-900 cursor-pointer hover:shadow-2xl hover:rounded-xl hover:bg-white p-2" aria-label="Toggle sidebar">
        <i class="ri-menu-2-line"></i>
    </button>

    <div class="flex items-center gap-x-1 ms-auto">

        <x-molecules.network-status/>

        <div class="relative ms-2 rounded-xl border border-gray-200 py-2 px-3 cursor-pointer hover:shadow-md" x-data="{ open: false }">
            <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-x-2.5 cursor-pointer">
                <x-atoms.icon :src="auth()->user()->avatar_url ?? null" :name="auth()->user()->name" size="sm"/>
                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                <svg class="size-4 text-gray-400" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="absolute inset-e-0 mt-3 w-48 bg-white rounded-lg border border-gray-200 shadow-lg py-1 z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

                <button type="button" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer" onclick="confirmLogoutDialog()">
                    Log Out
                </button>
            </div>
        </div>
    </div>
</header>
