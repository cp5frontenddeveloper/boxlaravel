<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'rating',
        'resolution'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
} 