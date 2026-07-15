@props(['messages' => [], 'notifications' => []])

<header class="navbar">
    <x-navbar.navbar-toggle />
    <x-navbar.navbar-search />

    <div class="ms-auto">
        <div class="flex gap-1">
            <x-navbar.navbar-popover-messages :messages="$messages" />
            <x-navbar.navbar-popover-notifications :notifications="$notifications" />
            <x-navbar.navbar-theme-toggle />
            <x-navbar.navbar-user-menu :user="auth()->user()" />
        </div>
    </div>
</header>
