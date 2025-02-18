<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryBox extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'workshop_id',
        'box_type_id',
        'box_status_id',
        'quantity',
        'received_quantity',
        'order_date',
        'actual_delivery_date',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'actual_delivery_date' => 'date'
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function boxType()
    {
        return $this->belongsTo(BoxType::class);
    }

    public function boxStatus()
    {
        return $this->belongsTo(BoxStatus::class);
    }
} 