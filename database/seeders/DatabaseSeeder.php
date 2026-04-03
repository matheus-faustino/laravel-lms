<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@laravel.com',
            'role' => RoleEnum::ADMIN,
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@laravel.com',
            'role' => RoleEnum::USER,
        ]);
    }
}
