<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'student_id', 'name', 'name_bn', 'father_name', 'mother_name',
        'father_occupation', 'mother_occupation', 'father_phone', 'mother_phone',
        'guardian_name', 'guardian_phone', 'date_of_birth', 'gender', 'religion',
        'blood_group', 'nationality', 'nid_or_birth_certificate', 'class_id',
        'section_id', 'group_id', 'academic_year_id', 'roll_number',
        'present_address', 'permanent_address', 'photo', 'admission_date',
        'status', 'admission_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // অটো ইউনিক student_id জেনারেট
    public static function boot()
    {
        parent::boot();
        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $next = $last ? $last->id + 1 : 1;
                $student->student_id = 'STU' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
