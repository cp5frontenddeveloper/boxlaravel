<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default ticket types
        DB::table('ticket_types')->insert([
            ['name' => 'شراء online', 'description' => 'مشاكل متعلقة بالشراء عبر الإنترنت', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مشكلة في التوصيل', 'description' => 'مشاكل متعلقة بخدمة التوصيل', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مشكلة في التركيب', 'description' => 'مشاكل متعلقة بتركيب الصندوق', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'اعطال سرقات', 'description' => 'بلاغات عن السرقات والاعطال', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'تلف', 'description' => 'مشاكل متعلقة بتلف المنتج', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'اخرى', 'description' => 'مشاكل اخرى غير مصنفة', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};