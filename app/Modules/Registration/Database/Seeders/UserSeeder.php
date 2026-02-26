<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing users (optional - clears slate)
        // User::truncate();

        // Create Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'name' => 'Admin User',
            'email' => 'admin@events.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'username' => 'admin',
            'age' => 30,
            'email_verified_at' => now(),
            'is_approved' => true,
            'is_active' => true,
        ]);

        // Create Event Creator
        User::create([
            'first_name' => 'Creator',
            'last_name' => 'User',
            'name' => 'Creator User',
            'email' => 'creator@events.com',
            'password' => Hash::make('password123'),
            'user_type' => 'event_creator',
            'username' => 'creator',
            'age' => 28,
            'organization_name' => 'Creator Events',
            'email_verified_at' => now(),
            'is_approved' => true,
            'is_active' => true,
        ]);

        // Create Attendee
        User::create([
            'first_name' => 'Attendee',
            'last_name' => 'User',
            'name' => 'Attendee User',
            'email' => 'attendee@events.com',
            'password' => Hash::make('password123'),
            'user_type' => 'attendee',
            'username' => 'attendee',
            'age' => 25,
            'email_verified_at' => now(),
            'is_approved' => true,
            'is_active' => true,
        ]);

        // Create Test User (optional)
        User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'attendee',
            'username' => 'testuser',
            'age' => 25,
            'email_verified_at' => now(),
            'is_approved' => true,
            'is_active' => true,
        ]);

        $this->command->info('Users created successfully!');
    }
}
