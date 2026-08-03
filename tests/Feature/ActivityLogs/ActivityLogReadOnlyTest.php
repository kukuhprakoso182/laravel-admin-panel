<?php

namespace Tests\Feature\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

class ActivityLogReadOnlyTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $before = ActivityLog::count();
        $newUser = User::factory()->create();
        $log = ActivityLog::where('subject_type', User::class)->where('subject_id', $newUser->id)->where('event', 'created')->first();
        $this->assertNotNull($log);
    }

    public function test_user_dengan_permission_view_bisa_melihat_daftar_log(): void
    {
        ActivityLog::factory()->create(['description' => 'Aktivitas contoh']);

        $response = $this->actingAs($this->actor)->getJson('/activity-logs/data');

        $response->assertOk();
        $response->assertJsonFragment(['description' => 'Aktivitas contoh']);
    }

    public function test_tidak_ada_endpoint_untuk_membuat_log_secara_manual(): void
    {
        $response = $this->actingAs($this->actor)->postJson('/activity-logs', [
            'event' => 'created',
            'description' => 'Coba buat manual',
        ]);

        // Tidak ada route POST /activity-logs terdaftar sama sekali.
        $response->assertStatus(404);
    }

    public function test_tidak_ada_endpoint_untuk_menghapus_log(): void
    {
        $log = ActivityLog::factory()->create();

        $response = $this->actingAs($this->actor)->deleteJson("/activity-logs/{$log->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('activity_logs', ['id' => $log->id]);
    }

    public function test_membuat_user_baru_otomatis_tercatat_di_activity_log(): void
    {
        $this->assertDatabaseCount('activity_logs', 0);

        $newUser = User::factory()->create();

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $newUser->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($log);
    }

    public function test_update_user_tercatat_dengan_properties_old_dan_new(): void
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);

        $user->update(['name' => 'Nama Baru']);

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'updated')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('Nama Lama', $log->properties['old']['name']);
        $this->assertSame('Nama Baru', $log->properties['new']['name']);
    }

    public function test_password_tidak_pernah_tercatat_di_activity_log(): void
    {
        $user = User::factory()->create(['password' => bcrypt('lamaLama123')]);

        $user->update(['password' => bcrypt('baruBaru123')]);

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'updated')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertArrayNotHasKey('password', $log->properties['old'] ?? []);
        $this->assertArrayNotHasKey('password', $log->properties['new'] ?? []);
    }

    public function test_update_tanpa_perubahan_nyata_tidak_membuat_log_baru(): void
    {
        $user = User::factory()->create(['name' => 'Nama Sama']);
        $jumlahLogSebelum = ActivityLog::count();

        // updated_at akan berubah otomatis, tapi tidak ada field lain yang
        // berubah — trait harus skip logging kalau changes cuma updated_at.
        $user->update(['name' => 'Nama Sama']);

        $this->assertSame($jumlahLogSebelum, ActivityLog::count());
    }

    public function test_hapus_user_tercatat_sebagai_event_deleted(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'deleted')
            ->first();

        $this->assertNotNull($log);
    }
}
