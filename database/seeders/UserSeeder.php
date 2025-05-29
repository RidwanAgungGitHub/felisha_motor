<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@falisa.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Kasir User
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@falisa.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
            'email_verified_at' => now(),
        ]);
    }
}
