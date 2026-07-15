@props(['user'])

@php
    $initials = collect(explode(' ', $user->name ?? '?'))
        ->map(fn ($w) => \Illuminate\Support\Str::substr($w, 0, 1))
        ->take(2)
        ->implode('');
@endphp

<div class="menu">
    <button
        type="button"
        class="button button--ghost button--neutral flex items-center gap-2"
        data-stisla-menu-trigger="topbarUser"
        aria-haspopup="menu"
        aria-expanded="false"
        aria-controls="topbarUser"
    >
        <span class="hidden sm:inline font-medium">{{ $user->name ?? 'Guest' }}</span>
        <x-ui.avatar :src="$user->avatar_url ?? null" :fallback="$initials" size="sm" />
        <x-ui.icon name="arrow-down-s-line" />
    </button>

    <div class="menu__popup w-48" id="topbarUser" data-stisla-menu role="menu" data-state="closed">
        <div class="menu__group" role="group" aria-labelledby="topbarUserHead">
            <h3 class="menu__group-label" id="topbarUserHead">{{ $user->name ?? 'Guest' }}</h3>
            <a href="{{ route('profile.edit') }}" class="menu__item" role="menuitem">
                <x-ui.icon name="user-line" />Profile
            </a>
            <a href="{{ route('settings.index') }}" class="menu__item" role="menuitem">
                <x-ui.icon name="settings-3-line" />Settings
            </a>
        </div>
        <hr class="menu__separator" role="separator" />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="menu__item w-full text-start" role="menuitem">
                <x-ui.icon name="logout-box-r-line" />Log out
            </button>
        </form>
    </div>
</div>
