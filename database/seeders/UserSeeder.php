<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
            'name'     => 'Developer',
            'email'    => 'developer@example.com',
            'password' => 'Test@Password123#',
        ];

        // Validate data to enforce data consistency (input sanitization)
        $validator = Validator::make($userData, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            Log::error('User seeder failed validation', [
                'errors' => $validator->errors()->toArray()
            ]);
            return;
        }

        try {
            // Create user with hashed password
            User::create([
                'name'     => $userData['name'],
                'email'    => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            Log::info('Developer user seeded successfully.');
        } catch (\Throwable $e) {
            Log::error('User seeding failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }
}
