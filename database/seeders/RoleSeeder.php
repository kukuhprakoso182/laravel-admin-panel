<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Memiliki akses penuh ke seluruh sistem'],
            ['name' => 'Admin', 'description' => 'Mengelola operasional harian'],
            ['name' => 'User', 'description' => 'Pengguna biasa dengan akses terbatas'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
