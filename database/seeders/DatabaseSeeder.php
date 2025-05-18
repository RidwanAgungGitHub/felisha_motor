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
    // Dalam file database/seeders/DatabaseSeeder.php
    public function run()
    {
        $this->call([
            UserSeeder::class,
            // BarangSeeder::class,
        ]);
    }
}
