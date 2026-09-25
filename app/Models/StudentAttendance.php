<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $fillable = [
        'student_id', 'class_id', 'section_id', 'academic_year_id',
        'attendance_date', 'status', 'remarks', 'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function statusLabels(): array
    {
        return [
            'present' => ['label' => 'উপস্থিত', 'color' => 'success'],
            'absent'  => ['label' => 'অনুপস্থিত', 'color' => 'danger'],
            'late'    => ['label' => 'বিলম্ব', 'color' => 'warning'],
            'leave'   => ['label' => 'ছুটি', 'color' => 'info'],
            'holiday' => ['label' => 'বন্ধ', 'color' => 'secondary'],
        ];
    }
}
