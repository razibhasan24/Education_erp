<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoutine extends Model
{
    protected $fillable = [
        'class_id', 'section_id', 'subject_id', 'teacher_id', 'academic_year_id',
        'day', 'start_time', 'end_time', 'room_no', 'period_no', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function section() { return $this->belongsTo(Section::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }

    public static function days(): array
    {
        return [
            'saturday' => 'শনিবার',
            'sunday' => 'রবিবার',
            'monday' => 'সোমবার',
            'tuesday' => 'মঙ্গলবার',
            'wednesday' => 'বুধবার',
            'thursday' => 'বৃহস্পতিবার',
            'friday' => 'শুক্রবার',
        ];
    }

    public function getDayLabelAttribute(): string
    {
        return self::days()[$this->day] ?? $this->day;
    }
}