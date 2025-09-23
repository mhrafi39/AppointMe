<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::table('reviews')->truncate();

        DB::table('reviews')->insert([
            ['review_id'=>1,'services_id'=>19,'rating'=>5,'comment'=>'dafA','created_at'=>'2025-08-14 23:05:00','updated_at'=>'2025-08-14 23:05:00'],
            ['review_id'=>2,'services_id'=>19,'rating'=>5,'comment'=>'srggsr','created_at'=>'2025-08-15 05:30:10','updated_at'=>'2025-08-15 05:30:10'],
            ['review_id'=>3,'services_id'=>19,'rating'=>5,'comment'=>'srggsr','created_at'=>'2025-08-15 05:31:10','updated_at'=>'2025-08-15 05:31:10'],
            ['review_id'=>4,'services_id'=>19,'rating'=>5,'comment'=>'hdydfty','created_at'=>'2025-08-15 05:37:09','updated_at'=>'2025-08-15 05:37:09'],
            ['review_id'=>5,'services_id'=>19,'rating'=>3,'comment'=>'test','created_at'=>'2025-08-15 05:37:50','updated_at'=>'2025-08-15 05:37:50'],
            ['review_id'=>6,'services_id'=>22,'rating'=>4,'comment'=>'He is good','created_at'=>'2025-08-15 09:30:06','updated_at'=>'2025-08-15 09:30:06'],
            ['review_id'=>7,'services_id'=>31,'rating'=>4,'comment'=>'4fsef','created_at'=>'2025-08-15 21:13:48','updated_at'=>'2025-08-15 21:13:48'],
            ['review_id'=>8,'services_id'=>31,'rating'=>4,'comment'=>'dada','created_at'=>'2025-08-15 21:13:53','updated_at'=>'2025-08-15 21:13:53'],
        ]);
    }
}

