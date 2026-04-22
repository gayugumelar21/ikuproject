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
            // Dummy data: urutan penting! Kominfo membuat SEKDA & Asisten II,
            // Disdik membuat Asisten I, baru kemudian Asisten1 yang membuat Kabag.
            DummyKominfoSeeder::class,
            DummyDisdikKerjasamaSeeder::class,
            DummyAsisten1Seeder::class,
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Admin Super',
            'email' => 'admin@ikuproject.test',
            'username' => 'admin',
        ]);

        $adminUser->assignRole('admin_super');
    }
}
