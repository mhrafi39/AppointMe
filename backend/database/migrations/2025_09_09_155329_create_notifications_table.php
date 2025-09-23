<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // notification id
            $table->unsignedBigInteger('user_id'); // user who receives the notification
            $table->string('type'); // booking, cancel, approval, etc.
            $table->text('message'); // notification message
            $table->boolean('is_read')->default(false); // mark as read/unread
            $table->timestamps();

            // foreign key to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};