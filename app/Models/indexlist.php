<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class indexlist extends Model
{
    use HasFactory;
    protected $table = 'indexdevice'; // This tells Laravel to use the 'lockfinger' table

    protected $fillable = ['pointer', 'device_id'];

    public function devices()
    {
        return $this->belongsTo(Device::class);
    }
}
