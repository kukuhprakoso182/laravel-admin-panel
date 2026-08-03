<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

/**
 * BaseFormRequest jadi tempat sentral penanganan gagal validasi untuk
 * SEMUA modul (Store/UpdateXxxRequest semuanya extends ini). Kalau
 * ini regresi, error handling di semua modul CRUD ikut rusak.
 */
class BaseFormRequestTest extends TestCase
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

    public function test_request_ajax_gagal_validasi_dapat_response_json_422(): void
    {
        $response = $this->actingAs($this->actor)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson('/users', []);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors']);
    }

    public function test_response_422_menyertakan_pesan_error_per_field(): void
    {
        $response = $this->actingAs($this->actor)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson('/users', ['email' => 'bukan-email-valid']);

        $response->assertJsonValidationErrors(['name', 'email', 'password', 'status']);
    }

    public function test_request_form_biasa_gagal_validasi_redirect_dengan_errors_bag(): void
    {
        $response = $this->actingAs($this->actor)
            ->from('/users')
            ->post('/users', []);

        $response->assertRedirect('/users');
        $response->assertSessionHasErrors(['name', 'email', 'password', 'status']);
    }

    public function test_request_valid_tidak_menghasilkan_error_apapun(): void
    {
        $response = $this->actingAs($this->actor)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson('/users', [
                'name' => 'Nama Valid',
                'email' => 'valid@example.com',
                'password' => 'password123',
                'status' => 'active',
            ]);

        $response->assertCreated();
    }
}
