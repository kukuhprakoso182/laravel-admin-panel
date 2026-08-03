<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

class PermissionCrudTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::factory()->create();
        $this->grantFull($role, 'permissions.index');

        $this->actor = User::factory()->create(['status' => 'active']);
        $this->actor->roles()->attach($role->id);
    }

    public function test_bisa_membuat_permission_baru(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/permissions', [
            'name' => 'approve',
            'description' => 'Menyetujui pengajuan',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('permissions', ['name' => 'approve']);
    }

    public function test_bisa_update_permission(): void
    {
        $permission = Permission::factory()->create(['description' => 'Deskripsi lama']);

        $response = $this->actingAs($this->actor)->putJson("/permissions/{$permission->id}", [
            'description' => 'Deskripsi baru',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'description' => 'Deskripsi baru']);
    }

    public function test_bisa_hapus_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->actingAs($this->actor)->deleteJson("/permissions/{$permission->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

}
