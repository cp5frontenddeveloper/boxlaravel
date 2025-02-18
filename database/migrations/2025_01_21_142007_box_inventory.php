<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('box_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_type_id')->constrained('box_types')->onDelete('restrict');
            $table->foreignId('box_status_id')->constrained('box_statuses')->onDelete('restrict');
            $table->integer('quantity');
            $table->date('manufacturing_date');
            $table->string('location_in_warehouse')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('box_inventory', function (Blueprint $table) {
            $table->dropForeign(['box_type_id']);
            $table->dropForeign(['box_status_id']);
        });
        
        Schema::dropIfExists('box_inventory');
    }
};
