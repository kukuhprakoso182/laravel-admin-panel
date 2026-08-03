<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $iconId = fn (string $value) => DB::table('icons')->where('value', $value)->value('id');

        // Menu induk (top-level): Dashboard — berdiri sendiri, tidak masuk Settings
        DB::table('menus')->insertGetId([
            'parent_id' => null,
            'icon_id' => $iconId('ri-dashboard-line'),
            'name' => 'Dashboard',
            'link' => '/dashboard',
            'link_alias' => 'dashboard',
            'order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Menu induk (top-level) BARU: Settings — menaungi semua menu lain selain Dashboard
        $settingsId = DB::table('menus')->insertGetId([
            'parent_id' => null,
            'icon_id' => $iconId('ri-settings-3-line'),
            'name' => 'Settings',
            'link' => null,
            'link_alias' => null,
            'order' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub-menu level 2: User Management, sekarang jadi ANAK dari Settings
        $userManagementId = DB::table('menus')->insertGetId([
            'parent_id' => $settingsId,
            'icon_id' => $iconId('ri-group-line'),
            'name' => 'User Management',
            'link' => null,
            'link_alias' => null,
            'order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub-menu level 3: anak dari User Management (tetap seperti sebelumnya)
        $userManagementChildren = [
            ['name' => 'Users', 'link' => '/users', 'link_alias' => 'users.index', 'icon' => 'ri-user-line', 'order' => 1],
            ['name' => 'Roles', 'link' => '/roles', 'link_alias' => 'roles.index', 'icon' => 'ri-shield-user-line', 'order' => 2],
            ['name' => 'Permissions', 'link' => '/permissions', 'link_alias' => 'permissions.index', 'icon' => 'ri-key-2-line', 'order' => 3],
        ];

        foreach ($userManagementChildren as $child) {
            DB::table('menus')->insert([
                'parent_id' => $userManagementId,
                'icon_id' => $iconId($child['icon']),
                'name' => $child['name'],
                'link' => $child['link'],
                'link_alias' => $child['link_alias'],
                'order' => $child['order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sub-menu level 2: Menu Management, sekarang jadi ANAK dari Settings
        $menuManagementId = DB::table('menus')->insertGetId([
            'parent_id' => $settingsId,
            'icon_id' => $iconId('ri-menu-line'),
            'name' => 'Menu Management',
            'link' => null,
            'link_alias' => null,
            'order' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub-menu level 3: anak dari Menu Management (tetap seperti sebelumnya)
        $menuManagementChildren = [
            ['name' => 'Menus', 'link' => '/menus', 'link_alias' => 'menus.index', 'icon' => 'ri-list-check-2', 'order' => 1],
            ['name' => 'Icons', 'link' => '/icons', 'link_alias' => 'icons.index', 'icon' => 'ri-emotion-line', 'order' => 2],
        ];

        foreach ($menuManagementChildren as $child) {
            DB::table('menus')->insert([
                'parent_id' => $menuManagementId,
                'icon_id' => $iconId($child['icon']),
                'name' => $child['name'],
                'link' => $child['link'],
                'link_alias' => $child['link_alias'],
                'order' => $child['order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sub-menu level 2 BARU: Log Aktivitas, jadi ANAK dari Settings — halaman monitoring, view-only
        DB::table('menus')->insert([
            'parent_id' => $settingsId,
            'icon_id' => $iconId('ri-file-list-3-line'),
            'name' => 'Log Aktivitas',
            'link' => '/activity-logs',
            'link_alias' => 'activity-logs.index',
            'order' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
