<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('images', function (Blueprint $table) {
            $table->id('image_id');
            $table->string('path', 100);
            $table->date('created_at');
            $table->date('updated_at');
            $table->integer('user_id')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('images');
    }
};
