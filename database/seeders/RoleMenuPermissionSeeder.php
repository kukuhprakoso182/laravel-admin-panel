<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleMenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'name');
        $menus = DB::table('menus')->pluck('id', 'name');
        $permissions = DB::table('permissions')->pluck('id', 'name');

        $rows = [];

        // Definisi per role: menu => daftar aksi yang diizinkan.
        // Menu grup/induk (Dashboard, Settings, User Management, Menu Management, Log Aktivitas)
        // cuma dapat 'view' — supaya menu-nya kelihatan & bisa diakses, tapi tidak ada aksi CRUD.
        // Menu aktual/leaf (Users, Roles, Permissions, Menus, Icons) dapat full CRUD sesuai role.
        $matrix = [
            'Super Admin' => [
                'Dashboard' => ['view'],
                'Settings' => ['view'],
                'User Management' => ['view'],
                'Users' => ['create', 'delete', 'edit', 'export', 'view'],
                'Roles' => ['create', 'delete', 'edit', 'export', 'view'],
                'Permissions' => ['create', 'delete', 'edit', 'export', 'view'],
                'Menu Management' => ['view'],
                'Menus' => ['create', 'delete', 'edit', 'export', 'view'],
                'Icons' => ['create', 'delete', 'edit', 'export', 'view'],
                'Log Aktivitas' => ['view'],
            ],
            'Admin' => [
                'Dashboard' => ['view'],
                'User Management' => ['view'],
                'Users' => ['create', 'delete', 'edit', 'export', 'view'],
                'Log Aktivitas' => ['view'],
            ],
            'User' => [
                'Dashboard' => ['view'],
            ],
        ];

        foreach ($matrix as $roleName => $menuActions) {
            if (!isset($roles[$roleName])) {
                continue;
            }

            foreach ($menuActions as $menuName => $actions) {
                if (!isset($menus[$menuName])) {
                    continue;
                }

                foreach ($actions as $action) {
                    if (!isset($permissions[$action])) {
                        continue;
                    }

                    $rows[] = [
                        'role_id' => $roles[$roleName],
                        'menu_id' => $menus[$menuName],
                        'permission_id' => $permissions[$action],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('role_menu_permissions')->insert($rows);
    }
}
