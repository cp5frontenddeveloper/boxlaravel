<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'representative_id',
        'box_type_id',
        'quantity',
        'receipt_date',
        'receipt_method',
        'price',
        'is_completed',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'price' => 'decimal:2',
        'is_completed' => 'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function boxType()
    {
        return $this->belongsTo(BoxType::class);
    }
}