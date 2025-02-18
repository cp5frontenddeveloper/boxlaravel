<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoxStatus;

class BoxStatusSeeder extends Seeder
{
    public function run()
    {
        BoxStatus::create([
            'name' => 'جديد',
            'description' => 'صندوق جديد في المخزن'
        ]);

        BoxStatus::create([
            'name' => 'مستعمل',
            'description' => 'صندوق مستعمل بحالة جيدة'
        ]);

        BoxStatus::create([
            'name' => 'تالف',
            'description' => 'صندوق تالف يحتاج إلى إصلاح'
        ]);
    }
} 