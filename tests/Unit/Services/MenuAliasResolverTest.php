<?php

namespace Tests\Unit\Services;

use App\Models\Menu;
use App\Services\MenuAliasResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuAliasResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_mengembalikan_menu_id_untuk_alias_yang_ada(): void
    {
        $menu = Menu::factory()->create(['link_alias' => 'users.index']);

        $resolver = app(MenuAliasResolver::class);

        $this->assertSame($menu->id, $resolver->resolve('users.index'));
    }

    public function test_resolve_mengembalikan_null_untuk_alias_yang_tidak_ada(): void
    {
        $resolver = app(MenuAliasResolver::class);

        $this->assertNull($resolver->resolve('tidak-pernah-ada.index'));
    }

    public function test_resolve_exact_match_bukan_prefix_atau_like(): void
    {
        Menu::factory()->create(['link_alias' => 'users.index']);

        $resolver = app(MenuAliasResolver::class);

        // 'users' (tanpa .index) TIDAK boleh match 'users.index' —
        // resolver harus exact match, bukan LIKE/prefix.
        $this->assertNull($resolver->resolve('users'));
    }

    public function test_map_di_cache_setelah_resolve_pertama_kali(): void
    {
        Menu::factory()->create(['link_alias' => 'roles.index']);

        $resolver = app(MenuAliasResolver::class);
        $resolver->resolve('roles.index');

        // Menu baru dibuat TANPA invalidate cache — resolver harus tetap
        // pakai map lama (karena sudah di-cache), sampai clearAll() dipanggil.
        Menu::factory()->create(['link_alias' => 'permissions.index']);

        $this->assertNull($resolver->resolve('permissions.index'));
    }

    public function test_clear_all_membuat_resolver_baca_ulang_dari_database(): void
    {
        $resolver = app(MenuAliasResolver::class);
        $resolver->resolve('menus.index'); // trigger cache dengan map kosong

        Menu::factory()->create(['link_alias' => 'menus.index']);
        $resolver->clearAll();

        $this->assertNotNull($resolver->resolve('menus.index'));
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}
