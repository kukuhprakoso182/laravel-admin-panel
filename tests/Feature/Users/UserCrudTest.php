<?php

namespace Tests\Feature\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::factory()->create();
        $this->grantFull($role, 'users.index');

        $this->actor = User::factory()->create(['status' => 'active']);
        $this->actor->roles()->attach($role->id);
    }

    public function test_bisa_membuat_user_baru_dengan_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->actor)->postJson('/users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'status' => 'active',
            'roles' => [$role->id],
        ]);

        $response->assertCreated();

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue($user->roles->contains($role->id));
    }

    public function test_password_disimpan_dalam_bentuk_hash_bukan_plain_text(): void
    {
        $this->actingAs($this->actor)->postJson('/users', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'rahasia123',
            'status' => 'active',
        ]);

        $user = User::where('email', 'siti@example.com')->first();

        $this->assertNotSame('rahasia123', $user->password);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_create_user_tanpa_role_tetap_berhasil(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/users', [
            'name' => 'Tanpa Role',
            'email' => 'tanpa-role@example.com',
            'password' => 'password123',
            'status' => 'active',
            'roles' => [],
        ]);

        $response->assertCreated();
        $user = User::where('email', 'tanpa-role@example.com')->first();
        $this->assertCount(0, $user->roles);
    }

    public function test_validasi_menolak_email_duplikat(): void
    {
        User::factory()->create(['email' => 'sudah-ada@example.com']);

        $response = $this->actingAs($this->actor)->postJson('/users', [
            'name' => 'Duplikat',
            'email' => 'sudah-ada@example.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_validasi_menolak_password_kurang_dari_8_karakter(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/users', [
            'name' => 'Password Pendek',
            'email' => 'pendek@example.com',
            'password' => 'pdk123',
            'status' => 'active',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_update_user_tanpa_isi_password_tidak_mengubah_password_lama(): void
    {
        $user = User::factory()->create(['password' => Hash::make('passwordLama123')]);
        $hashLama = $user->password;

        $response = $this->actingAs($this->actor)->putJson("/users/{$user->id}", [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'password' => '',
            'status' => 'active',
        ]);

        $response->assertOk();
        $user->refresh();

        $this->assertSame($hashLama, $user->password);
        $this->assertTrue(Hash::check('passwordLama123', $user->password));
    }

    public function test_update_user_dengan_password_baru_mengubah_hash(): void
    {
        $user = User::factory()->create(['password' => Hash::make('passwordLama123')]);

        $this->actingAs($this->actor)->putJson("/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'passwordBaru123',
            'status' => 'active',
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('passwordBaru123', $user->password));
    }

    public function test_update_roles_benar_benar_mengubah_pivot_roles(): void
    {
        $roleLama = Role::factory()->create();
        $roleBaru = Role::factory()->create();

        $user = User::factory()->create();
        $user->roles()->attach($roleLama->id);

        $response = $this->actingAs($this->actor)->putJson("/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'status' => 'active',
            'roles' => [$roleBaru->id],
        ]);

        $response->assertOk();
        $user->refresh();

        $this->assertFalse($user->roles->contains($roleLama->id));
        $this->assertTrue($user->roles->contains($roleBaru->id));
    }

    public function test_update_roles_dengan_array_kosong_melepas_semua_role(): void
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        $response = $this->actingAs($this->actor)->putJson("/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'status' => 'active',
            'roles' => [],
        ]);

        $response->assertOk();
        $user->refresh();

        $this->assertCount(0, $user->roles);
    }

    public function test_delete_user_soft_delete_bukan_hard_delete(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->actor)->deleteJson("/users/{$user->id}");

        $response->assertOk();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_filter_role_pada_list_data_hanya_menampilkan_user_dengan_role_itu(): void
    {
        $roleA = Role::factory()->create();
        $roleB = Role::factory()->create();

        $userA = User::factory()->create(['name' => 'User Role A']);
        $userA->roles()->attach($roleA->id);

        $userB = User::factory()->create(['name' => 'User Role B']);
        $userB->roles()->attach($roleB->id);

        $response = $this->actingAs($this->actor)->getJson("/users/data?role={$roleA->id}");

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');

        $this->assertTrue($names->contains('User Role A'));
        $this->assertFalse($names->contains('User Role B'));
    }

    public function test_search_mencari_berdasarkan_nama_dan_email(): void
    {
        User::factory()->create(['name' => 'Nama Unik Sekali', 'email' => 'lain@example.com']);
        User::factory()->create(['name' => 'Orang Lain', 'email' => 'unik-sekali@example.com']);
        User::factory()->create(['name' => 'Tidak Relevan', 'email' => 'tidak-relevan@example.com']);

        $response = $this->actingAs($this->actor)->getJson('/users/data?search=unik sekali');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
