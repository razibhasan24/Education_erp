<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'name', 'name_bn', 'exam_type', 'academic_year_id',
        'start_date', 'end_date', 'description', 'is_published', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public static function typeLabels(): array
    {
        return [
            'class_test'  => 'ক্লাস টেস্ট',
            'monthly'     => 'মাসিক পরীক্ষা',
            'half_yearly' => 'অর্ধ-বার্ষিক',
            'annual'      => 'বার্ষিক',
            'model_test'  => 'মডেল টেস্ট',
            'admission'   => 'ভর্তি পরীক্ষা',
        ];
    }
}
