<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if an admin already exists to prevent duplicates
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Site Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'), // Password from your error output
                
                'role' => 'admin',
                'is_admin' => true,
                'is_instructor' => false,
                'is_user' => false,
            ]);
        }
    }
}
