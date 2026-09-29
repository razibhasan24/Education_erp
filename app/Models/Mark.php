<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mark extends Model
{
    use LogsActivity;

    protected $fillable = [
        'exam_id', 'exam_subject_id', 'student_id', 'class_id', 'section_id',
        'written_marks', 'mcq_marks', 'practical_marks', 'total_marks',
        'grade', 'gpa', 'is_absent', 'remarks', 'entered_by',
    ];

    protected $casts = [
        'is_absent' => 'boolean',
    ];
     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('student');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSubject()
    {
        return $this->belongsTo(ExamSubject::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}