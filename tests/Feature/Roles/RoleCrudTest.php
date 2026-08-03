<?php

namespace Tests\Feature\Roles;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    protected User $actor;
    protected Role $actorRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actorRole = Role::factory()->create();
        $this->grantFull($this->actorRole, 'roles.index');

        $this->actor = User::factory()->create(['status' => 'active']);
        $this->actor->roles()->attach($this->actorRole->id);
    }

    public function test_bisa_membuat_role_baru(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/roles', [
            'name' => 'Editor',
            'description' => 'Bisa edit konten',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('roles', ['name' => 'Editor']);
    }

    public function test_validasi_menolak_nama_role_duplikat(): void
    {
        Role::factory()->create(['name' => 'Editor']);

        $response = $this->actingAs($this->actor)->postJson('/roles', [
            'name' => 'Editor',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_bisa_update_role(): void
    {
        $role = Role::factory()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($this->actor)->putJson("/roles/{$role->id}", [
            'name' => 'Nama Baru',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Nama Baru']);
    }

    public function test_update_role_boleh_pakai_nama_sendiri_tanpa_kena_unique_error(): void
    {
        $role = Role::factory()->create(['name' => 'Nama Tetap']);

        $response = $this->actingAs($this->actor)->putJson("/roles/{$role->id}", [
            'name' => 'Nama Tetap',
            'description' => 'Deskripsi diperbarui',
        ]);

        $response->assertOk();
    }

    public function test_bisa_hapus_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->actor)->deleteJson("/roles/{$role->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_sync_permissions_menyimpan_kombinasi_menu_dan_permission(): void
    {
        $target = Role::factory()->create();
        $menu = Menu::factory()->create();
        $permission = Permission::factory()->create(['name' => 'view']);

        $response = $this->actingAs($this->actor)->postJson("/roles/{$target->id}/permissions", [
            'menu_permissions' => [
                ['menu_id' => $menu->id, 'permission_id' => $permission->id],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('role_menu_permissions', [
            'role_id' => $target->id,
            'menu_id' => $menu->id,
            'permission_id' => $permission->id,
        ]);
    }

    public function test_sync_permissions_menghapus_kombinasi_lama_yang_tidak_dikirim_ulang(): void
    {
        $target = Role::factory()->create();
        $menuLama = Menu::factory()->create();
        $menuBaru = Menu::factory()->create();
        $permission = Permission::factory()->create(['name' => 'view']);

        // Kondisi awal: role sudah punya izin ke menuLama.
        $this->grant($target, $menuLama->link_alias, 'view', $menuLama);

        $response = $this->actingAs($this->actor)->postJson("/roles/{$target->id}/permissions", [
            'menu_permissions' => [
                ['menu_id' => $menuBaru->id, 'permission_id' => $permission->id],
            ],
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing('role_menu_permissions', [
            'role_id' => $target->id,
            'menu_id' => $menuLama->id,
        ]);
        $this->assertDatabaseHas('role_menu_permissions', [
            'role_id' => $target->id,
            'menu_id' => $menuBaru->id,
            'permission_id' => $permission->id,
        ]);
    }

    public function test_permission_matrix_mengembalikan_pasangan_menu_permission_yang_sudah_di_assign(): void
    {
        $target = Role::factory()->create();
        $menu = Menu::factory()->create();
        $permission = Permission::factory()->create(['name' => 'view']);
        $this->grant($target, $menu->link_alias, 'view', $menu);

        $response = $this->actingAs($this->actor)->getJson("/roles/{$target->id}/permissions");

        $response->assertOk();
        $response->assertJsonFragment(['assigned' => ["{$menu->id}:{$permission->id}"]]);
    }
}
