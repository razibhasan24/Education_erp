<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherEvaluation extends Model
{
    protected $fillable = [
        'teacher_id', 'student_id', 'class_id', 'subject_id',
        'teaching_quality', 'communication', 'behavior', 'punctuality', 'knowledge',
        'average_rating', 'comments', 'is_anonymous', 'status',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }

    public static function criteria(): array
    {
        return [
            'teaching_quality' => 'শিক্ষাদানের গুণমান',
            'communication' => 'যোগাযোগ দক্ষতা',
            'behavior' => 'ব্যবহার',
            'punctuality' => 'সময়ানুবর্তিতা',
            'knowledge' => 'বিষয় জ্ঞান',
        ];
    }

    public function getRatingStarsAttribute(): string
    {
        $rating = round($this->average_rating);
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }

    /**
     * একটি শিক্ষকের সব evaluation থেকে average
     */
    public static function teacherAverage(int $teacherId): array
    {
        $evals = self::where('teacher_id', $teacherId)->where('status', 'approved');
        $count = $evals->count();

        if ($count === 0) {
            return ['overall' => 0, 'count' => 0, 'breakdown' => []];
        }

        $breakdown = [];
        foreach (array_keys(self::criteria()) as $key) {
            $breakdown[$key] = round((clone $evals)->avg($key), 2);
        }

        return [
            'overall' => round((clone $evals)->avg('average_rating'), 2),
            'count' => $count,
            'breakdown' => $breakdown,
        ];
    }
}