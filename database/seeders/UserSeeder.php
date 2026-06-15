<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@glowglam.com',
            'password' => Hash::make('password'),
            'phone' => '01712345678',
            'is_admin' => true,
        ]);

        // Sample users
        $users = [
            ['name' => 'Fatima Rahman', 'email' => 'fatima@example.com', 'phone' => '01812345678'],
            ['name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'phone' => '01912345678'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat@example.com', 'phone' => '01612345678'],
            ['name' => 'Sabina Yasmin', 'email' => 'sabina@example.com', 'phone' => '01512345678'],
            ['name' => 'Rina Akter', 'email' => 'rina@example.com', 'phone' => '01312345678'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'phone' => $user['phone'],
                'is_admin' => false,
            ]);
        }
    }
}
