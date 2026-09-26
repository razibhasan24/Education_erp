<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone', 'message', 'type', 'student_id', 'status', 'response', 'sent_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
