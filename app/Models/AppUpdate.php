<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'release_date',
        'changes',
        'is_active'
    ];

    protected $casts = [
        'changes' => 'array',
        'release_date' => 'date',
        'is_active' => 'boolean'
    ];
}