<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'timestamp',
        'event_type',
        'usertype',
        'opening_method',
        'device_id',
        'event_data',
        'description',
        'status'
    ];

    protected $casts = [
        'event_data' => 'array',
        'timestamp' => 'datetime'
    ];

    // تحسين إخراج JSON
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    // إضافة العلاقة مع المستخدم الفرعي
    public function getSubuserAttribute()
    {
        return Subuser::where('const_code', $this->usertype)->first();
    }
}
