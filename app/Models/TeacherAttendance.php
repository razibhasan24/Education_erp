<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'teacher_id', 'attendance_date', 'in_time', 'out_time',
        'status', 'remarks', 'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
