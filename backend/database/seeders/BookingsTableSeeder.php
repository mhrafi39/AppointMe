<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingsTableSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear the table first
        DB::table('bookings')->truncate();

        // Insert bookings with boolean status and payment_status
        DB::table('bookings')->insert([
            [
                'booking_id' => 1,
                'services_id' => 19,
                'user_id' => 43423,
                'booking_time' => '2025-08-14 10:00:00',
                'created_at' => '2025-08-14 22:43:03',
                'status' => 1,            // true / confirmed
                'payment_status' => 1,    // true / paid
                'updated_at' => '2025-08-14 22:43:03'
            ],
            [
                'booking_id' => 3,
                'services_id' => 19,
                'user_id' => 34345,
                'booking_time' => '2025-08-14 10:07:00',
                'created_at' => '2025-08-14 22:49:20',
                'status' => 0,            // false / unconfirmed
                'payment_status' => 0,    // false / unpaid
                'updated_at' => '2025-08-14 22:49:20'
            ],
            [
                'booking_id' => 4,
                'services_id' => 19,
                'user_id' => 3453,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 05:21:09',
                'status' => 0,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 05:21:09'
            ],
            [
                'booking_id' => 5,
                'services_id' => 19,
                'user_id' => 3453,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 05:22:22',
                'status' => 0,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 05:22:22'
            ],
            [
                'booking_id' => 6,
                'services_id' => 19,
                'user_id' => 354542,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 05:23:07',
                'status' => 1,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 05:23:07'
            ],
            [
                'booking_id' => 7,
                'services_id' => 19,
                'user_id' => 35264,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 05:37:27',
                'status' => 1,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 05:37:27'
            ],
            [
                'booking_id' => 8,
                'services_id' => 20,
                'user_id' => 4254,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 06:39:22',
                'status' => 0,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 06:39:22'
            ],
            [
                'booking_id' => 9,
                'services_id' => 22,
                'user_id' => 5666,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 09:29:29',
                'status' => 0,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 09:29:29'
            ],
            [
                'booking_id' => 10,
                'services_id' => 31,
                'user_id' => 142,
                'booking_time' => '2025-08-15 10:07:00',
                'created_at' => '2025-08-15 21:13:42',
                'status' => 1,
                'payment_status' => 0,
                'updated_at' => '2025-08-15 21:13:42'
            ],
        ]);
    }
}
