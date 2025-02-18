<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_id',
        'description',
        'order_number',
        'status',
        'progress',
        'status_notes'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            $latestTicket = static::latest()->first();
            $nextNumber = $latestTicket ? intval(substr($latestTicket->order_number, 4)) + 1 : 1;
            $ticket->order_number = 'TKT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    public function updates()
    {
        return $this->hasMany(TicketUpdate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function rating()
    {
        return $this->hasOne(TicketRating::class);
    }
} 