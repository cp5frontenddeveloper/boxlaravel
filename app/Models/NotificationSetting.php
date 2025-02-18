<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'door_open',
        'door_left_open',
        'lock_status',
        'battery',
        'internet',
        'tamper',
        'notification_sound',
        'vibration_enabled'
    ];

    protected $casts = [
        'door_open' => 'boolean',
        'door_left_open' => 'boolean',
        'lock_status' => 'boolean',
        'battery' => 'boolean',
        'internet' => 'boolean',
        'tamper' => 'boolean',
        'vibration_enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
