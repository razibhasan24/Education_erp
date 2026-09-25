<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubject extends Model
{
    protected $fillable = [
        'exam_id', 'class_id', 'subject_id', 'group_id',
        'full_marks', 'pass_marks', 'written_marks', 'mcq_marks',
        'practical_marks', 'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
