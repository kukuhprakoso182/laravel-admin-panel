<?php

namespace Tests\Unit\Services;

use App\Models\Icon;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression test untuk bug: tree menu sebelumnya cuma render sampai
 * 1-2 level karena eager-load dot-notation ('children.icon') tidak
 * rekursif. Fix-nya pakai relasi self-referencing `childrenRecursive()`
 * + query builder yang tidak dibatasi kedalaman.
 */
class MenuServiceTreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_tree_mengembalikan_struktur_nested_sampai_4_level(): void
    {
        $level1 = Menu::factory()->create(['parent_id' => null, 'name' => 'Settings']);
        $level2 = Menu::factory()->create(['parent_id' => $level1->id, 'name' => 'Menu Management']);
        $level3 = Menu::factory()->create(['parent_id' => $level2->id, 'name' => 'Permissions']);
        $level4 = Menu::factory()->create(['parent_id' => $level3->id, 'name' => 'Icons']);

        $tree = app(MenuService::class)->tree();

        $settingsNode = collect($tree)->firstWhere('name', 'Settings');
        $this->assertNotNull($settingsNode);

        $menuManagementNode = collect($settingsNode['children'])->firstWhere('name', 'Menu Management');
        $this->assertNotNull($menuManagementNode, 'Level 2 (anak dari Settings) tidak muncul.');

        $permissionsNode = collect($menuManagementNode['children'])->firstWhere('name', 'Permissions');
        $this->assertNotNull($permissionsNode, 'Level 3 (cucu dari Settings) tidak muncul.');

        $iconsNode = collect($permissionsNode['children'])->firstWhere('name', 'Icons');
        $this->assertNotNull($iconsNode, 'Level 4 (cicit dari Settings) tidak muncul — bug rekursi kembali terjadi.');
    }

    public function test_tree_hanya_mengembalikan_menu_top_level_sebagai_root(): void
    {
        $top = Menu::factory()->create(['parent_id' => null]);
        Menu::factory()->create(['parent_id' => $top->id]);

        $tree = app(MenuService::class)->tree();

        $this->assertCount(1, $tree);
        $this->assertSame($top->id, $tree[0]['id']);
    }

    public function test_menu_tanpa_children_mengembalikan_array_kosong_bukan_null(): void
    {
        $menu = Menu::factory()->create(['parent_id' => null]);

        $tree = app(MenuService::class)->tree();

        $node = collect($tree)->firstWhere('id', $menu->id);
        $this->assertIsArray($node['children']);
        $this->assertCount(0, $node['children']);
    }

    public function test_icon_ikut_ter_eager_load_di_setiap_level_kedalaman(): void
    {
        $icon = Icon::factory()->create(['value' => 'ri-test-line']);

        $level1 = Menu::factory()->create(['parent_id' => null]);
        $level2 = Menu::factory()->create(['parent_id' => $level1->id, 'icon_id' => $icon->id]);

        $tree = app(MenuService::class)->tree();

        $node = collect($tree)->firstWhere('id', $level1->id);
        $childNode = collect($node['children'])->firstWhere('id', $level2->id);

        $this->assertSame('ri-test-line', $childNode['icon']['value']);
    }

    public function test_tree_diurutkan_berdasarkan_kolom_order(): void
    {
        Menu::factory()->create(['parent_id' => null, 'name' => 'Kedua', 'order' => 2]);
        Menu::factory()->create(['parent_id' => null, 'name' => 'Pertama', 'order' => 1]);

        $tree = app(MenuService::class)->tree();

        $this->assertSame('Pertama', $tree[0]['name']);
        $this->assertSame('Kedua', $tree[1]['name']);
    }
}
