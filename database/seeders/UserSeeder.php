<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'مستخدم تجريبي',
            'email' => 'test@example.com',
            'phone_number' => '1234567890',
            'password' => Hash::make('password123'),
        ]);
    }
} 