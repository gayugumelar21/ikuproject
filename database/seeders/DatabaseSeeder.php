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
        $this->call(RolePermissionSeeder::class);

        $adminUser = User::factory()->create([
            'name' => 'Admin Super',
            'email' => 'admin@ikuproject.test',
        ]);

        $adminUser->assignRole('admin_super');
    }
}
