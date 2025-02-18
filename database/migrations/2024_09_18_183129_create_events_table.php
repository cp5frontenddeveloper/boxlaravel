<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id'); // Foreign key to devices table
            $table->timestamp("timestamp");
            $table->string("event_type");
            $table->string("usertype");
            $table->string("opening_method");
            $table->json("event_data")->nullable();
            $table->string("description")->nullable();
            $table->string("status")->default('active');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade'); // Reference to devices table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
