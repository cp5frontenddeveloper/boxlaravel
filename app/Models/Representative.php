<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Representative extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Remove this method to prevent double hashing
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = Hash::make($value);
    // }

    public function communicationLogs()
    {
        return $this->hasMany(AdminCommunicationLog::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}