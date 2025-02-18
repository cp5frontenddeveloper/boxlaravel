<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->text('description');
            $table->string('order_number', 50)->nullable();
            $table->enum('status', ['new', 'in_progress', 'waiting', 'closed'])->default('new');
            $table->integer('progress')->default(0);
            $table->text('status_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
}; 