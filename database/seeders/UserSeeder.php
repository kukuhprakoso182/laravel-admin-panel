<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleIds = DB::table('roles')->pluck('id', 'name');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'roles' => ['Super Admin'],
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'roles' => ['Admin'],
            ],
            [
                'name' => 'Multi Role Example',
                'email' => 'multirole@example.com',
                'roles' => ['Admin', 'User'], // contoh 1 user dengan 2 role sekaligus
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'roles' => ['User'],
            ],
        ];

        foreach ($users as $user) {
            $userId = Str::uuid();
            DB::table('users')->insertGetId([
                'id' => $userId,
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($user['roles'] as $roleName) {
                DB::table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleIds[$roleName],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
