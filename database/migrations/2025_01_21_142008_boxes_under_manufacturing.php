<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('boxes_under_manufacturing', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('restrict');
            $table->foreignId('box_type_id')->constrained('box_types')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->integer('received_quantity')->default(0);
            $table->date('order_date');
            $table->date('actual_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('boxes_under_manufacturing', function (Blueprint $table) {
            $table->dropForeign(['workshop_id']);
            $table->dropForeign(['box_type_id']);
        });
        
        Schema::dropIfExists('boxes_under_manufacturing');
    }
};