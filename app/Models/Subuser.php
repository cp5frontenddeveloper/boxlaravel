<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subuser extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'const_code',
        'user_id',
        'device_id',
        'email',
        'phone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
