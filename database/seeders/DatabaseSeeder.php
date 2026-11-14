<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@stokly.id'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'is_admin' => true,
                'password' => Hash::make('password123'),
            ]
        );

        $this->call(ProductSeeder::class);
    }
}