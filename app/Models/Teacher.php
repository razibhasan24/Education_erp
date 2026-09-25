<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'teacher_id', 'name', 'name_bn', 'designation', 'department',
        'father_name', 'mother_name', 'date_of_birth', 'gender', 'religion',
        'blood_group', 'nid', 'phone', 'email', 'present_address',
        'permanent_address', 'photo', 'qualification', 'joining_date',
        'basic_salary', 'employment_type', 'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($teacher) {
            if (empty($teacher->teacher_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $next = $last ? $last->id + 1 : 1;
                $teacher->teacher_id = 'TCH' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
