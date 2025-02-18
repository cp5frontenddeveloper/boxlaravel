<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoxUnderManufacturing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'workshop_id',
        'box_type_id',
        'quantity',
        'unit_price',
        'paid_amount',
        'remaining_amount',
        'received_quantity',
        'order_date',
        'actual_delivery_date',
        'notes',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function boxType()
    {
        return $this->belongsTo(BoxType::class);
    }
}