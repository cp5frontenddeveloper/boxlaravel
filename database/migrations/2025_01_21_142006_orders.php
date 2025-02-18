<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('restrict');
            $table->foreignId('representative_id')->nullable()->constrained('representatives')->onDelete('set null');  // Fixed order
            $table->foreignId('box_type_id')->constrained('box_types')->onDelete('restrict');
            $table->integer('quantity');
            $table->date('receipt_date');
            $table->string('receipt_method');
            $table->decimal('price', 10, 2);
            $table->boolean('is_completed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['representative_id']);
            $table->dropForeign(['box_type_id']);
        });
        
        Schema::dropIfExists('orders');
    }
};