<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_name',
        'mac_address',
        'user_id',
        'location_name',
        'latitude',
        'longitude',
        'usercode',
        'status',
        'site_data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function codeList()
    {
        return $this->hasMany(CodeList::class);
    }
}
