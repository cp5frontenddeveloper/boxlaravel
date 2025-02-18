<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('user_tokens', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');  // لربط التوكن بالمستخدم
        $table->string('token');
        $table->text('access_token');
        $table->timestamps();

        // Foreign key to the users table
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
