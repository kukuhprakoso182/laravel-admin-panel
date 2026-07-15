<footer class="sidebar__footer">
    <ul class="sidebar__list">
        <x-sidebar.sidebar-item
            label="Settings"
            icon="settings-3-line"
            :href="\Illuminate\Support\Facades\Route::has('settings.index') ? route('settings.index') : '#'"
        />
        <li class="sidebar__item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar__button w-full text-start">
                    <x-ui.icon name="logout-box-r-line" />
                    <span>Log out</span>
                </button>
            </form>
        </li>
    </ul>

    <div class="copyright">
        <hr
            class="separator my-3"
            style="--separator-color: var(--sidebar-submenu-border-color)"
        />
        <p class="text-xs text-muted-foreground" style="--link-color: var(--color-foreground)">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>
</footer>
