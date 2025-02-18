<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workshop;

class WorkshopSeeder extends Seeder
{
    public function run()
    {
        Workshop::create([
            'name' => 'ورشة الأمل',
            'workshop_number' => 'W001',
            'email' => 'alamal@example.com',
            'manager_name' => 'أحمد محمد',
            'location' => 'الرياض - حي النزهة',
            'rating' => 4.5,
            'iban' => 'SA0380000000608010167519',
            'records' => json_encode(['تم إنشاء الورشة بتاريخ ' . now()])
        ]);

        Workshop::create([
            'name' => 'ورشة النور',
            'workshop_number' => 'W002',
            'email' => 'alnoor@example.com',
            'manager_name' => 'خالد عبدالله',
            'location' => 'جدة - حي الصفا',
            'rating' => 4.2,
            'iban' => 'SA0380000000608010167520',
            'records' => json_encode(['تم إنشاء الورشة بتاريخ ' . now()])
        ]);
    }
} 