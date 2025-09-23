<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update admin account
        Admin::updateOrCreate(
            ['email' => 'mahdikhan.chowdhury@gmail.com'], // checks if email exists
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'), // hash the password
            ]
        );
    }
}
