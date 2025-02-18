<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'status',
        'text'
    ];

    protected $with = ['ticket']; // Eager load ticket relationship

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Add any additional accessors if needed
    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }
}