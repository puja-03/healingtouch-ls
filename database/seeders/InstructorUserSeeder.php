<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class InstructorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'instructor@example.com')->exists()) {
            User::create([
                'name' => 'Lead Instructor',
                'email' => 'instructor@example.com',
                'password' => Hash::make('12345678'), // Same default password for easy testing
                
                'role' => 'instructor',
                'is_admin' => false,
                'is_instructor' => true,
                'is_user' => false,
            ]);
        }
    }
}