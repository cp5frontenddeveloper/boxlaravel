<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('workshop_number');
            $table->string('email');
            $table->string('manager_name');
            $table->text('location');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->string('iban');
            $table->text('records')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workshops');
    }
}; 