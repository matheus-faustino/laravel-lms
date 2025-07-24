<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Test Student',
            'email' => 'student@email.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_STUDENT,
        ]);

        $this->call([
            CourseSeeder::class,
        ]);
    }
}
