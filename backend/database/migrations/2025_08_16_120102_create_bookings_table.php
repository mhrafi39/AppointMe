<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->integer('services_id');
            $table->integer('user_id');
            $table->string('booking_time', 100);
            $table->boolean('status')->default(false); // default false
            $table->boolean('payment_status')->default(false); // default false
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
