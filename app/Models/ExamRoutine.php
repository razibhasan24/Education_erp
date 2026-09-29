<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRoutine extends Model
{
    protected $fillable = [
        'exam_id', 'class_id', 'section_id', 'exam_subject_id', 'subject_id',
        'exam_date', 'start_time', 'end_time', 'room_no', 'invigilator', 'note',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function exam() { return $this->belongsTo(Exam::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function section() { return $this->belongsTo(Section::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function examSubject() { return $this->belongsTo(ExamSubject::class); }

    public function getDurationAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) return '-';
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $mins = $start->diffInMinutes($end);
        return $mins . ' মিনিট';
    }
}