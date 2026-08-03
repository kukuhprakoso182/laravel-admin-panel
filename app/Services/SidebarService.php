<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SidebarService
{
    protected int $ttlMinutes = 360; // 6 jam

    protected const TAG = 'sidebar';
    protected const VERSION_KEY = 'sidebar_menu:version';

    public function getForUser(User $user): array
    {
        if ($this->supportsTags()) {
            return Cache::tags([self::TAG])->remember(
                $this->cacheKey($user->id),
                now()->addMinutes($this->ttlMinutes),
                fn () => $this->buildFromDatabase($user)
            );
        }

        // Driver tanpa dukungan tag (file/array/database) -> pakai versioning,
        // supaya clearAll() tetap bisa "meng-invalidasi" tanpa perlu hapus per-key.
        return Cache::remember(
            $this->cacheKey($user->id),
            now()->addMinutes($this->ttlMinutes),
            fn () => $this->buildFromDatabase($user)
        );
    }

    public function clearForUser(int|string $userId): void
    {
        if ($this->supportsTags()) {
            Cache::tags([self::TAG])->forget($this->cacheKey($userId));
            return;
        }

        Cache::forget($this->cacheKey($userId));
    }

    /**
     * Hapus SEMUA cache sidebar (semua user) — panggil saat ada perubahan
     * struktural yang berdampak luas (menu baru, permission role berubah, dst).
     */
    public function clearAll(): void
    {
        if ($this->supportsTags()) {
            Cache::tags([self::TAG])->flush();
            return;
        }

        // Fallback untuk driver non-taggable (file/array/database):
        // naikkan versi -> semua cache key lama otomatis "basi" (tidak akan
        // pernah match key baru), tidak perlu tahu daftar user id.
        Cache::increment(self::VERSION_KEY);
    }

    protected function supportsTags(): bool
    {
        return method_exists(Cache::getStore(), 'tags');
    }

    protected function cacheKey(int|string $userId): string
    {
        $version = $this->supportsTags() ? null : (Cache::get(self::VERSION_KEY) ?? 1);

        return $version
            ? "sidebar_menu:v{$version}:user:{$userId}"
            : "sidebar_menu:user:{$userId}";
    }

    protected function buildFromDatabase(User $user): array
    {
        $roleIds = $user->roles()->pluck('roles.id')->all();

        // Cast eksplisit ke int, karena DB::table()->pluck() (query builder mentah)
        // bisa mengembalikan string tergantung driver/PDO setting, sedangkan
        // $menu->id dari Eloquent selalu int -> in_array(..., true) butuh tipe sama.
        $accessibleMenuIds = DB::table('role_menu_permissions')
            ->whereIn('role_id', $roleIds)
            ->pluck('menu_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();

        $menusByParent = Menu::with('icon')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('parent_id');

        $topLevelMenus = $menusByParent->get(null, collect());
        $sections = [];

        foreach ($topLevelMenus as $top) {
            $mapped = $this->mapMenuRecursive($top, $menusByParent, $accessibleMenuIds);

            if ($mapped === null) {
                continue;
            }

            if (isset($mapped['submenu'])) {
                $sections[] = [
                    'title' => $top->name,
                    'items' => $mapped['submenu'],
                ];
            } else {
                $sections[] = [
                    'title' => null,
                    'items' => [$mapped],
                ];
            }
        }

        return $sections;
    }

    protected function mapMenuRecursive(Menu $menu, Collection $menusByParent, array $accessibleMenuIds): ?array
    {
        $children = $menusByParent->get($menu->id, collect());

        $mappedChildren = $children
            ->map(fn ($child) => $this->mapMenuRecursive($child, $menusByParent, $accessibleMenuIds))
            ->filter()
            ->values();

        $isDirectlyAccessible = in_array($menu->id, $accessibleMenuIds, true);

        if (!$isDirectlyAccessible && $mappedChildren->isEmpty()) {
            return null;
        }

        $item = [
            'id' => 'menu-' . $menu->id,
            'label' => $menu->name,
            'icon' => $menu->icon?->value,
            'route' => $menu->link_alias,
        ];

        if ($mappedChildren->isNotEmpty()) {
            $item['submenu'] = $mappedChildren->all();
        }

        return $item;
    }
}
