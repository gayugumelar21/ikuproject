<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            // Dummy data: urutan penting!
            // 1. Kominfo  → membuat SEKDA & Asisten II
            // 2. Disdik   → membuat Asisten I
            // 3. Asisten1 → membuat Kabag-kabag
            // 4. Skoring  → membuat IkuSkoring dari realisasi (HARUS PALING AKHIR)
            DummyKominfoSeeder::class,
            DummyDisdikKerjasamaSeeder::class,
            DummyAsisten1Seeder::class,
            DummySkoringSeeder::class,
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Admin Super',
            'email' => 'admin@ikuproject.test',
            'username' => 'admin',
        ]);

        $adminUser->assignRole('admin_super');
    }
}
