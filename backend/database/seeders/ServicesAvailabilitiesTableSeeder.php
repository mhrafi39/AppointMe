<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesAvailabilitiesTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Insert dummy availability data
        DB::table('service_availabilities')->insert([
            ['services_id' => 23, 'is_booked' => true, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 24, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 25, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 26, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 27, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 28, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 29, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 31, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 32, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 33, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 34, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 35, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 36, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 37, 'is_booked' => false, 'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 38, 'is_booked' => false,  'created_at' => now(), 'updated_at' => now()],
            ['services_id' => 39, 'is_booked' => false,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
