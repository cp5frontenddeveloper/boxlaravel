<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lockfinger extends Model
{
    use HasFactory;

    // Specify the table name since it doesn't follow Laravel's pluralization convention
    protected $table = 'lockfinger'; // This tells Laravel to use the 'lockfinger' table

    protected $fillable = ['statelock', 'device_id'];

    public function devices()
    {
        return $this->belongsTo(Device::class);
    }
}
