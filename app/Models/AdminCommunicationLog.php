<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminCommunicationLog extends Model
{
    use HasFactory, SoftDeletes;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'representative_id',
        'title',   // عنوان الإشعار
        'date',    // تاريخ الإشعار
        'isNew',   // حالة الإشعار (جديد أو قديم)
        'note',    // الملاحظة
    ];

    // العلاقة مع نموذج المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Add representative relationship
    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}