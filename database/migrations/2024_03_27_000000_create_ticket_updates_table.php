<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->enum('status', ['new', 'in_progress', 'waiting', 'closed']);
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_updates');
    }
}; 