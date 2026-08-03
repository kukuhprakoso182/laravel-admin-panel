<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            IconSeeder::class,
            UserSeeder::class,
            MenuSeeder::class,
            RoleMenuPermissionSeeder::class,
        ]);
    }
}
