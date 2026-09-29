<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionTestResult extends Model
{
    protected $fillable = [
        'roll_no', 'student_name', 'father_name', 'mother_name', 'phone', 'email',
        'class_id', 'academic_year_id', 'exam_id',
        'bangla', 'english', 'math', 'general_knowledge',
        'total_marks', 'merit_position', 'status', 'remarks', 'is_published',
    ];

    protected $casts = ['is_published' => 'boolean'];

    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function exam() { return $this->belongsTo(Exam::class); }

    public static function generateRollNo(int $classId): string
    {
        $prefix = 'ADM-' . date('Y') . '-' . $classId . '-';
        $last = self::where('roll_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->roll_no, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotal(): float
    {
        return $this->bangla + $this->english + $this->math + $this->general_knowledge;
    }

    /**
     * একটি ক্লাসের merit position পুনঃহিসাব
     */
    public static function recalculateMeritPositions(int $classId): void
    {
        $results = self::where('class_id', $classId)
            ->orderByDesc('total_marks')
            ->get();

        $rank = 1;
        foreach ($results as $r) {
            $r->update(['merit_position' => $rank++]);
        }
    }
}