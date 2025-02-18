<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['numbers','device_number','userid'];

    // تحديد أن الحقل numbers يجب أن يُتعامل معه كمصفوفة JSON
    protected $casts = [
        'numbers' => 'array',
    ];
}
