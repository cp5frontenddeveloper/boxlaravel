<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];
    public function inventoryBoxes() {
        return $this->hasMany(InventoryBox::class);
    }
}