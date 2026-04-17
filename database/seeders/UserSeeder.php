<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Casemix',
                'email' => 'admin@casemix.local',
                'password' => 'password',
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Petugas Casemix',
                'email' => 'casemix@casemix.local',
                'password' => 'password',
                'role' => 'casemix',
                'is_active' => true,
            ],
            [
                'name' => 'Verifier Casemix',
                'email' => 'verifier@casemix.local',
                'password' => 'password',
                'role' => 'verifier',
                'is_active' => true,
            ],
            [
                'name' => 'Manager Casemix',
                'email' => 'manager@casemix.local',
                'password' => 'password',
                'role' => 'manager',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}