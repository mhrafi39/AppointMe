<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceAvailabilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('service_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('services_id')->unique();
            $table->boolean('is_booked')->default(false);
            $table->timestamps();

            // foreign key to services.services_id (adjust if your PK name differs)
            $table->foreign('services_id')
                  ->references('services_id')
                  ->on('services')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_availabilities');
    }
}
