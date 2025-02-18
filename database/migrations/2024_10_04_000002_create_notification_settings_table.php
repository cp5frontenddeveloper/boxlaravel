<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('door_open')->default(true);
            $table->boolean('door_left_open')->default(true);
            $table->boolean('lock_status')->default(true);
            $table->boolean('battery')->default(true);
            $table->boolean('internet')->default(true);
            $table->boolean('tamper')->default(true);
            $table->string('notification_sound')->default('default');
            $table->boolean('vibration_enabled')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
