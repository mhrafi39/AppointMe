<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Call the other table seeders in order
        $this->call([
            ServicesTableSeeder::class,
            ServicesAvailabilitiesTableSeeder::class,
            BookingsTableSeeder::class,
            ImagesTableSeeder::class,
            PaymentsTableSeeder::class,
            ReviewsTableSeeder::class,
            AdminAccountSeeder::class
        ]);
    }
}
