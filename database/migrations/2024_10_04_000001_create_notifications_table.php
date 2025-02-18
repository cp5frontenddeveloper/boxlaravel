<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['door_open', 'door_left_open', 'lock_status', 'battery', 'internet', 'tamper']);
            $table->string('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
