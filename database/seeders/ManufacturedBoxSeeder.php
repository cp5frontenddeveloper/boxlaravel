<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManufacturedBox;

class ManufacturedBoxSeeder extends Seeder
{
    public function run()
    {
        ManufacturedBox::create([
            'invoice_number' => 'MFG-2024-001',
            'workshop_id' => 1,
            'box_type_id' => 1,
            'quantity' => 200,
            'received_quantity' => 150,
            'order_date' => now()->subDays(15),
            'actual_delivery_date' => now()->subDays(10),
            'notes' => 'دفعة إنتاج صناديق كبيرة'
        ]);

        ManufacturedBox::create([
            'invoice_number' => 'MFG-2024-002',
            'workshop_id' => 2,
            'box_type_id' => 3,
            'quantity' => 300,
            'received_quantity' => 300,
            'order_date' => now()->subDays(20),
            'actual_delivery_date' => now()->subDays(15),
            'notes' => 'دفعة إنتاج صناديق صغيرة مكتملة'
        ]);
    }
} 