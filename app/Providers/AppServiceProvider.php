<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Menu;
use App\Services\SidebarService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            if (!$event->user instanceof Model || !$event->user->exists) {
                return;
            }

            ActivityLog::create([
                'user_id' => $event->user->getAuthIdentifier(),
                'event' => 'login',
                'description' => "{$event->user->name} berhasil login",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (!$event->user instanceof Model || !$event->user->exists) {
                return;
            }

            ActivityLog::create([
                'user_id' => $event->user?->getAuthIdentifier(),
                'event' => 'logout',
                'description' => ($event->user->name ?? 'User') . ' logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            ActivityLog::create([
                'user_id' => null,
                'event' => 'login_failed',
                'description' => 'Percobaan login gagal untuk email: ' . ($event->credentials['email'] ?? '-'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        });

        View::composer('components.organisms.sidebar.sidebar-menu', function ($view) {
            if (Auth::check()) {
                $view->with(
                    'sidebarSections',
                    app(SidebarService::class)->getForUser(Auth::user())
                );
            }
        });

        /**
         * Bridge role_menu_permissions ke `@can()` / `->can()` bawaan Laravel.
         *
         * Konvensi ability string: "{link_alias_menu}:{nama_permission}",
         * misal "users.index:edit", "roles.index:delete", "users.index:export".
         *
         * Query permission-nya sendiri tetap lewat User::hasPermission()
         * yang sudah ada — di sini cuma menerjemahkan nama route jadi
         * menu_id, karena hasPermission() butuh menu_id (int), bukan
         * link_alias (string).
         */
        Gate::before(function ($user, string $ability) {
            if (! str_contains($ability, ':')) {
                return null;
            }

            [$routeName, $permissionName] = explode(':', $ability, 2);

            $menuId = Menu::where('link_alias', '=',$routeName, 'and')->value('id');

            if (! $menuId) {
                // Menu tidak terdaftar di sistem permission — sembunyikan
                // tombol aksi secara default (fail-closed), bukan tampilkan.
                return false;
            }

            return $user->hasPermission($permissionName, $menuId);
        });
    }
}
