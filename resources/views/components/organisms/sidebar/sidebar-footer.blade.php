@props(['brand', 'author'])
<div class="px-3 pt-2 pb-4 space-y-1">
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    <button type="button" class="w-full cursor-pointer" onclick="confirmLogoutDialog()">
        <span class="flex items-center gap-x-3 px-3 py-2 rounded-lg text-sm font-medium text-ink-600 hover:bg-surface-100 hover:text-ink-900">
            <i class="ri-logout-box-line shrink-0"></i>
            Log Out
        </span>
    </button>

    <p class="px-3 pt-3 text-xs text-ink-400">
        {{ $brand }} by <a href="https://www.instagram.com/kukuh182/" target="_blank" class="underline hover:text-ink-600">{{ $author }}</a><br>
        <x-atoms.span color="primary">v {{ config('app.version') }}</x-atoms.span>
    </p>
</div>
