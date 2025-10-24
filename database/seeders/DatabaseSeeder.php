<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1) MESTI create roles & permissions DULU
            RolesAndPermissionsSeeder::class,

            // 2) Baru create users & assign roles
            AdminUserSeeder::class,
            InitialUsersSeeder::class,

            // 3) Lepas tu baru data lain
            JobSeeder::class,
            MasterFileSeeder::class,
            OutdoorCoordinatorSeeder::class,

            // 4) Kalau ada report permissions, run last
            // ReportPermissionSeeder::class,
        ]);
    }
}
