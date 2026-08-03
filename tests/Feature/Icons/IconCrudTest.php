<?php

namespace Tests\Feature\Icons;

use App\Models\Icon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

class IconCrudTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::factory()->create();
        $this->grantFull($role, 'icons.index');

        $this->actor = User::factory()->create(['status' => 'active']);
        $this->actor->roles()->attach($role->id);
    }

    public function test_bisa_membuat_icon_baru(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/icons', [
            'value' => 'ri-home-line',
            'section' => 'sidebar',
            'is_active' => true,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('icons', ['value' => 'ri-home-line']);
    }

    public function test_validasi_menolak_tanpa_value(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/icons', [
            'section' => 'sidebar',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('value');
    }

    public function test_bisa_update_status_aktif_icon(): void
    {
        $icon = Icon::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->actor)->putJson("/icons/{$icon->id}", [
            'is_active' => false,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('icons', ['id' => $icon->id, 'is_active' => false]);
    }

    public function test_bisa_hapus_icon(): void
    {
        $icon = Icon::factory()->create();

        $response = $this->actingAs($this->actor)->deleteJson("/icons/{$icon->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('icons', ['id' => $icon->id]);
    }

    public function test_filter_berdasarkan_section(): void
    {
        Icon::factory()->create(['value' => 'ri-a-line', 'section' => 'sidebar']);
        Icon::factory()->create(['value' => 'ri-b-line', 'section' => 'status']);

        $response = $this->actingAs($this->actor)->getJson('/icons/data?section=sidebar');

        $response->assertOk();
        $values = collect($response->json('data'))->pluck('value');

        $this->assertTrue($values->contains('ri-a-line'));
        $this->assertFalse($values->contains('ri-b-line'));
    }
}
