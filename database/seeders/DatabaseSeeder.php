<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // SupplierSeeder::class,
            // CategorySeeder::class,
            // LaboratorySeeder::class,
            // DoctorSeeder::class,
        ]);
    }
}
