<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workshop_number',
        'email',
        'manager_name',
        'location',
        'rating',
        'iban',
        'records'
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'records' => 'array'
    ];

    public function boxesUnderManufacturing()
    {
        return $this->hasMany(BoxUnderManufacturing::class);
    }
}