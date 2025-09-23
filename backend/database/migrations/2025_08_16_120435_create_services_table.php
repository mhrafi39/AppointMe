<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $table) {
            $table->id('services_id');
            $table->integer('user_id');
            $table->string('name', 50);
            $table->string('description', 500);
            $table->string('category', 50);
            $table->string('location', 100);
            $table->integer('price');
            $table->string('available_time', 50);
            $table->date('created_at');
            $table->date('updated_at');
        });
    }  

    public function down(): void {
        Schema::dropIfExists('services');
    }
};