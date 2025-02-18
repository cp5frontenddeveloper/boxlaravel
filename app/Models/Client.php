<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'location',
        'joining_date',
    ];

    protected $casts = [
        'joining_date' => 'date'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}