<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    protected $fillable = [
        'scholarship_id', 'student_id', 'academic_year_id',
        'requested_amount', 'approved_amount', 'reason',
        'status', 'approved_by', 'approved_at', 'remarks',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'approved_amount' => 'decimal:2',
        'requested_amount' => 'decimal:2',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}