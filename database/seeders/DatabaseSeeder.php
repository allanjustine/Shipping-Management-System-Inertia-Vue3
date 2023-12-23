<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'type' => 'Admin',
            'password'  => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'user@gmail.com',
            'type' => 'Standard',
            'password'  => bcrypt('password123'),
        ]);
        $this->call([
            RolesSeeder::class,
            // UserSeeder::class
        ]);
    }
}
