<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeList extends Model
{
    use HasFactory;

    protected $fillable = [
        'codes',
        'device_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
