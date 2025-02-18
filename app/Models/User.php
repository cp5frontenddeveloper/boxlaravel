<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'tempCode',
        'email_verified_at',
        'last_login_at',
        'last_login_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function subusers()
    {
        return $this->hasMany(Subuser::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function codeLists()
    {
        return $this->hasMany(CodeList::class);
    }
    public function access_token()
    {
        return $this->hasOne(UserToken::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
