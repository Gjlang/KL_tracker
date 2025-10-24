<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin One',   'email' => 'admin1@bluedale.com.my',   'username' => 'admin1',   'role' => 'admin'],
            ['name' => 'Admin Two',   'email' => 'admin2@bluedale.com.my',   'username' => 'admin2',   'role' => 'admin'],
            ['name' => 'User One',    'email' => 'user1@bluedale.com.my',    'username' => 'user1',    'role' => 'user'],
            ['name' => 'User Two',    'email' => 'user2@bluedale.com.my',    'username' => 'user2',    'role' => 'user'],
            ['name' => 'Support One', 'email' => 'support1@bluedale.com.my', 'username' => 'support1', 'role' => 'support'],
            ['name' => 'Support Two', 'email' => 'support2@bluedale.com.my', 'username' => 'support2', 'role' => 'support'],
        ];

        foreach ($accounts as $acct) {
            $user = User::updateOrCreate(
                ['email' => $acct['email']],
                [
                    'name'     => $acct['name'],
                    'email'    => $acct['email'],
                    'username' => $acct['username'],
                    'password' => Hash::make($acct['username']),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role using Spatie
            $user->syncRoles([$acct['role']]);
        }
    }
}
