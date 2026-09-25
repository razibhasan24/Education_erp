<?php

namespace App\Helpers;

class GradeHelper
{
    /**
     * বাংলাদেশ শিক্ষা বোর্ডের গ্রেডিং পদ্ধতি (২০১৫ সংস্করণ)
     */
    public static function getGrade(float $marks, int $fullMarks = 100): array
    {
        if ($fullMarks <= 0) $fullMarks = 100;
        $percent = ($marks / $fullMarks) * 100;

        return match (true) {
            $percent >= 80 => ['grade' => 'A+', 'gpa' => 5.00, 'color' => 'success'],
            $percent >= 70 => ['grade' => 'A',  'gpa' => 4.00, 'color' => 'success'],
            $percent >= 60 => ['grade' => 'A-', 'gpa' => 3.50, 'color' => 'info'],
            $percent >= 50 => ['grade' => 'B',  'gpa' => 3.00, 'color' => 'info'],
            $percent >= 40 => ['grade' => 'C',  'gpa' => 2.00, 'color' => 'warning'],
            $percent >= 33 => ['grade' => 'D',  'gpa' => 1.00, 'color' => 'warning'],
            default        => ['grade' => 'F',  'gpa' => 0.00, 'color' => 'danger'],
        };
    }

    /**
     * একাধিক বিষয়ের GPA হিসাব (F থাকলে GPA 0)
     */
    public static function calculateGPA(array $subjectResults): array
    {
        $totalGpa = 0;
        $count = count($subjectResults);
        $hasFail = false;
        $totalMarks = 0;
        $totalFull = 0;

        foreach ($subjectResults as $r) {
            if ($r['gpa'] == 0) $hasFail = true;
            $totalGpa += $r['gpa'];
            $totalMarks += $r['marks'];
            $totalFull += $r['full_marks'];
        }

        if ($count == 0) {
            return ['gpa' => 0, 'grade' => 'F', 'failed' => true];
        }

        $gpa = $hasFail ? 0 : round($totalGpa / $count, 2);
        $grade = self::gpaToGrade($gpa);

        return [
            'gpa' => $gpa,
            'grade' => $grade,
            'failed' => $hasFail,
            'total_marks' => $totalMarks,
            'total_full_marks' => $totalFull,
            'average_percent' => $totalFull > 0 ? round(($totalMarks / $totalFull) * 100, 2) : 0,
        ];
    }

    public static function gpaToGrade(float $gpa): string
    {
        return match (true) {
            $gpa >= 5.00 => 'A+',
            $gpa >= 4.00 => 'A',
            $gpa >= 3.50 => 'A-',
            $gpa >= 3.00 => 'B',
            $gpa >= 2.00 => 'C',
            $gpa >= 1.00 => 'D',
            default      => 'F',
        };
    }

    /**
     * ফেল করা বিষয় চেক
     */
    public static function isFail(float $marks, int $passMarks): bool
    {
        return $marks < $passMarks;
    }
}
