<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryBox;

class InventoryBoxSeeder extends Seeder
{
    public function run()
    {
        InventoryBox::create([
            'invoice_number' => 'INV-2024-001',
            'workshop_id' => 1,
            'box_type_id' => 1,
            'box_status_id' => 1,
            'quantity' => 100,
            'received_quantity' => 80,
            'order_date' => now(),
            'actual_delivery_date' => now()->addDays(5),
            'notes' => 'طلبية صناديق كبيرة للمخزن الرئيسي'
        ]);

        InventoryBox::create([
            'invoice_number' => 'INV-2024-002',
            'workshop_id' => 2,
            'box_type_id' => 2,
            'box_status_id' => 1,
            'quantity' => 150,
            'received_quantity' => 150,
            'order_date' => now()->subDays(10),
            'actual_delivery_date' => now()->subDays(5),
            'notes' => 'طلبية صناديق متوسطة مكتملة التسليم'
        ]);
    }
} 