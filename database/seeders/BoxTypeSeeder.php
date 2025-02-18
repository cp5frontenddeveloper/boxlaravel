<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoxType;

class BoxTypeSeeder extends Seeder
{
    public function run()
    {
        BoxType::create([
            'name' => 'صندوق كبير',
            'description' => 'صندوق بأبعاد 100×60×40 سم'
        ]);

        BoxType::create([
            'name' => 'صندوق متوسط',
            'description' => 'صندوق بأبعاد 80×40×30 سم'
        ]);

        BoxType::create([
            'name' => 'صندوق صغير',
            'description' => 'صندوق بأبعاد 60×30×20 سم'
        ]);
    }
} 