<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representative_id')->nullable()->constrained('representatives')->onDelete('set null');
            $table->string('title'); // عنوان الإشعار
            $table->date('date'); // تاريخ الإشعار
            $table->boolean('isNew')->default(true); // حالة الإشعار (جديد أو قديم)
            $table->text('note')->nullable(); // الملاحظة (يمكن أن تكون فارغة)
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::table('admin_communication_logs', function (Blueprint $table) {
            $table->dropForeign(['representative_id']);
        });
        
        Schema::dropIfExists('admin_communication_logs');
    }
};