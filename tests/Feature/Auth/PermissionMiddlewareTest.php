<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\GrantsPermissions;
use Tests\TestCase;

/**
 * Middleware `permission:{aksi},{link_alias}` adalah satu-satunya lapisan
 * otorisasi di aplikasi ini. Kalau test di sini gagal, artinya ada celah
 * akses yang nyata — bukan sekadar bug kosmetik.
 */
class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase, GrantsPermissions;

    public function test_guest_ditolak_akses_route_yang_dijaga_auth(): void
    {
        $response = $this->getJson('/users/data');

        // Route berada di dalam group middleware('auth'), jadi guest
        // akan terhenti di 'auth' sebelum sempat menyentuh 'permission'.
        $response->assertRedirect(route('login'));
    }

    public function test_user_tanpa_permission_ditolak_403(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        // Tidak ada role/permission apapun yang diberikan.

        $response = $this->actingAs($user)->getJson('/users/data');

        $response->assertForbidden();
    }

    public function test_user_dengan_permission_yang_tepat_bisa_akses(): void
    {
        $role = Role::factory()->create();
        $this->grant($role, 'users.index', 'view');

        $user = User::factory()->create(['status' => 'active']);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->getJson('/users/data');

        $response->assertOk();
    }

    public function test_user_dengan_permission_di_menu_lain_tetap_ditolak(): void
    {
        $role = Role::factory()->create();
        // Sengaja beri izin ke menu yang BEDA dari yang diakses.
        $this->grant($role, 'roles.index', 'view');

        $user = User::factory()->create(['status' => 'active']);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->getJson('/users/data');

        $response->assertForbidden();
    }

    public function test_user_dengan_permission_tapi_aksi_salah_ditolak(): void
    {
        $role = Role::factory()->create();
        // Beri 'create' saja, tapi endpoint /users/data butuh 'view'.
        $this->grant($role, 'users.index', 'create');

        $user = User::factory()->create(['status' => 'active']);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->getJson('/users/data');

        $response->assertForbidden();
    }

    public function test_user_nonaktif_ditolak_walau_permission_lengkap(): void
    {
        $role = Role::factory()->create();
        $this->grant($role, 'users.index', 'view');

        $user = User::factory()->create(['status' => 'inactive']);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->getJson('/users/data');

        $response->assertForbidden();
        $response->assertSee('tidak aktif');
    }

    public function test_menu_alias_yang_tidak_terdaftar_gagal_aman_bukan_lolos_diam_diam(): void
    {
        // Daftarkan route sementara dengan alias menu yang SENGAJA tidak
        // pernah ada di tabel menus — memastikan fail-safe (403), bukan
        // fail-open (lolos begitu saja karena alias tidak resolve).
        Route::middleware(['web', 'auth', 'permission:view,menu-alias-yang-tidak-ada'])
            ->get('/__test/dummy-protected', fn () => response()->json(['ok' => true]));

        $role = Role::factory()->create();
        $user = User::factory()->create(['status' => 'active']);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->getJson('/__test/dummy-protected');

        $response->assertForbidden();
    }

    public function test_route_tanpa_menu_alias_hanya_cek_permission_generik(): void
    {
        // Middleware mendukung dipanggil tanpa argumen kedua ($menuAlias
        // opsional) — pastikan behavior itu tidak error dan tetap menolak
        // user yang tidak punya permission generik apapun.
        Route::middleware(['web', 'auth', 'permission:view'])
            ->get('/__test/dummy-no-menu', fn () => response()->json(['ok' => true]));

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->getJson('/__test/dummy-no-menu');

        $response->assertForbidden();
    }
}
