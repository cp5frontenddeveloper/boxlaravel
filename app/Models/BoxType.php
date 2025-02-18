<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function inventory()
    {
        return $this->hasMany(BoxInventory::class);
    }

    public function boxesUnderManufacturing()
    {
        return $this->hasMany(BoxUnderManufacturing::class);
    }
    public function inventoryBoxes() {
        return $this->hasMany(InventoryBox::class);
    }
}