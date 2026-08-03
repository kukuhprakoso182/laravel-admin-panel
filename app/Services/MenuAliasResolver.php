<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuAliasResolver
{
    protected int $ttlMinutes = 360;

    protected const TAG = 'menu_alias_map';
    protected const VERSION_KEY = 'menu_alias_map:version';

    /**
     * Ambil menu_id berdasarkan link_alias (mis. 'roles.index').
     * Null kalau alias tidak match menu manapun di database.
     */
    public function resolve(string $alias): ?int
    {
        return $this->getMap()[$alias] ?? null;
    }

    /**
     * Panggil ini setiap kali menu berubah (create/update/delete link_alias) —
     * sejajar dengan SidebarService::clearAll(), keduanya perlu di-invalidate
     * bareng saat struktur menu berubah.
     */
    public function clearAll(): void
    {
        if ($this->supportsTags()) {
            Cache::tags([self::TAG])->flush();
            return;
        }

        Cache::increment(self::VERSION_KEY);
    }

    protected function getMap(): array
    {
        if ($this->supportsTags()) {
            return Cache::tags([self::TAG])->remember(
                $this->cacheKey(),
                now()->addMinutes($this->ttlMinutes),
                fn () => $this->buildFromDatabase()
            );
        }

        return Cache::remember(
            $this->cacheKey(),
            now()->addMinutes($this->ttlMinutes),
            fn () => $this->buildFromDatabase()
        );
    }

    protected function supportsTags(): bool
    {
        return method_exists(Cache::getStore(), 'tags');
    }

    protected function cacheKey(): string
    {
        $version = $this->supportsTags() ? null : (Cache::get(self::VERSION_KEY) ?? 1);

        return $version ? "menu_alias_map:v{$version}" : 'menu_alias_map';
    }

    protected function buildFromDatabase(): array
    {
        $query = Menu::whereNotNull('link_alias', "and");

        return $query->pluck('id', 'link_alias')->all();
    }
}
