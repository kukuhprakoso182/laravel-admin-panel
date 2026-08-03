<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::factory()->create();
        $this->grantFull($role, 'menus.index');

        $this->actor = User::factory()->create(['status' => 'active']);
        $this->actor->roles()->attach($role->id);
    }

    public function test_bisa_membuat_menu_baru(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/menus', [
            'name' => 'Laporan',
            'link' => '/laporan',
            'order' => 1,
            'is_active' => true,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('menus', ['name' => 'Laporan']);
    }

    public function test_bisa_membuat_menu_dengan_parent(): void
    {
        $parent = Menu::factory()->create();

        $response = $this->actingAs($this->actor)->postJson('/menus', [
            'name' => 'Sub Menu',
            'parent_id' => $parent->id,
            'order' => 1,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('menus', ['name' => 'Sub Menu', 'parent_id' => $parent->id]);
    }

    public function test_validasi_menolak_parent_id_yang_tidak_ada(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/menus', [
            'name' => 'Menu Invalid',
            'parent_id' => 99999,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('parent_id');
    }

    public function test_update_menu_tidak_boleh_menjadikan_dirinya_sendiri_sebagai_parent(): void
    {
        $menu = Menu::factory()->create();

        $response = $this->actingAs($this->actor)->putJson("/menus/{$menu->id}", [
            'parent_id' => $menu->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('parent_id');
    }

    public function test_update_menu_boleh_ganti_parent_ke_menu_lain(): void
    {
        $menu = Menu::factory()->create();
        $parentBaru = Menu::factory()->create();

        $response = $this->actingAs($this->actor)->putJson("/menus/{$menu->id}", [
            'parent_id' => $parentBaru->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'parent_id' => $parentBaru->id]);
    }

    public function test_bisa_hapus_menu(): void
    {
        $menu = Menu::factory()->create();

        $response = $this->actingAs($this->actor)->deleteJson("/menus/{$menu->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    public function test_filter_berdasarkan_parent_id_di_list_data(): void
    {
        $parent = Menu::factory()->create();
        $child = Menu::factory()->create(['parent_id' => $parent->id, 'name' => 'Anak Menu']);
        $lainnya = Menu::factory()->create(['name' => 'Menu Lain']);

        $response = $this->actingAs($this->actor)->getJson("/menus/data?parent_id={$parent->id}");

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');

        $this->assertTrue($names->contains('Anak Menu'));
        $this->assertFalse($names->contains('Menu Lain'));
    }
}
