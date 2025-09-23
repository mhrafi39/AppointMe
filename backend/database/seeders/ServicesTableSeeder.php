<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        //DB::table('services')->truncate();

        DB::table('services')->insert([
            ['services_id'=>23,'user_id'=>3001,'name'=>'Tony','description'=>'I have good experience','category'=>'Plumber','location'=>'Dhaka','price'=>600,'available_time'=>'10am-6pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>24,'user_id'=>3002,'name'=>'Rahim','description'=>'I have good experience','category'=>'Electrician','location'=>'Dhaka','price'=>1500,'available_time'=>'10am-6pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>25,'user_id'=>3006,'name'=>'Tony','description'=>'I have good experience','category'=>'Lawyer','location'=>'Dhaka','price'=>600,'available_time'=>'8am-7pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>26,'user_id'=>3003,'name'=>'Karim','description'=>'I have good experience','category'=>'Plumber','location'=>'Dhaka','price'=>600,'available_time'=>'8am-7pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>27,'user_id'=>3070,'name'=>'Ter','description'=>'I have good experience','category'=>'Chef','location'=>'Khulna','price'=>1500,'available_time'=>'10am-6pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>28,'user_id'=>3004,'name'=>'Hitler','description'=>'I have good experience','category'=>'Lawyer','location'=>'Barisal','price'=>1500,'available_time'=>'3am-6pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>29,'user_id'=>3007,'name'=>'Putin','description'=>'I have good experience','category'=>'Lawyer','location'=>'Khulna','price'=>600,'available_time'=>'2am-9pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>31,'user_id'=>3008,'name'=>'Mahdi Khan','description'=>'I have good experience','category'=>'Engineer','location'=>'Dhaka','price'=>1500,'available_time'=>'1pm-9pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>32,'user_id'=>3009,'name'=>'Sadnan Saher','description'=>'I have good experience','category'=>'Chef','location'=>'Khulna','price'=>600,'available_time'=>'8am-7pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>33,'user_id'=>3010,'name'=>'Rafi','description'=>'I have good experience','category'=>'Electrician','location'=>'Khulna','price'=>1500,'available_time'=>'1pm-9pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>34,'user_id'=>3011,'name'=>'Fatah','description'=>'I have good experience','category'=>'Plumber','location'=>'Khulna','price'=>1500,'available_time'=>'10am-6pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],

            // 5 new records
            ['services_id'=>35,'user_id'=>3012,'name'=>'Sami','description'=>'Experienced painter','category'=>'Painter','location'=>'Dhaka','price'=>1200,'available_time'=>'9am-5pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>36,'user_id'=>3013,'name'=>'Nabila','description'=>'Professional cleaner','category'=>'Cleaner','location'=>'Barisal','price'=>800,'available_time'=>'7am-3pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>37,'user_id'=>3014,'name'=>'Jamil','description'=>'Expert carpenter','category'=>'Carpenter','location'=>'Khulna','price'=>1000,'available_time'=>'10am-6pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>38,'user_id'=>3015,'name'=>'Ayesha','description'=>'Skilled decorator','category'=>'Decorator','location'=>'Dhaka','price'=>2000,'available_time'=>'11am-8pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
            ['services_id'=>39,'user_id'=>3016,'name'=>'Rasel','description'=>'Professional gardener','category'=>'Gardener','location'=>'Chittagong','price'=>900,'available_time'=>'6am-2pm','created_at'=>'2025-08-15','updated_at'=>'2025-08-15'],
        ]);
    }
}
