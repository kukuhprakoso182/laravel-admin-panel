<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            'view' => 'Melihat data',
            'create' => 'Menambahkan data baru',
            'edit' => 'Mengubah data yang sudah ada',
            'delete' => 'Menghapus data',
            'export' => 'Mengekspor data ke file',
        ];

        $now = now();

        $rows = collect($actions)->map(fn ($description, $name) => [
            'name' => $name,
            'description' => $description,
            'created_at' => $now,
            'updated_at' => $now,
        ])->values()->all();

        DB::table('permissions')->insert($rows);
    }
}
