<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GradeHelper;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /**
     * শ্রেণিভিত্তিক সম্পূর্ণ রেজাল্ট শীট
     */
    public function index(Request $request, Exam $exam)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
        }

        $resultData = collect();
        $examSubjects = collect();

        if ($classId) {
            $students = Student::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $examSubjects = ExamSubject::with('subject')
                ->where('exam_id', $exam->id)
                ->where('class_id', $classId)
                ->get();

            $marks = Mark::where('exam_id', $exam->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->groupBy('student_id');

            $resultData = $students->map(function ($student) use ($marks, $examSubjects) {
                $studentMarks = $marks->get($student->id, collect());
                $subjectResults = [];

                foreach ($examSubjects as $es) {
                    $mark = $studentMarks->firstWhere('exam_subject_id', $es->id);
                    $subjectResults[] = [
                        'exam_subject' => $es,
                        'marks' => $mark?->total_marks ?? 0,
                        'full_marks' => $es->full_marks,
                        'pass_marks' => $es->pass_marks,
                        'grade' => $mark?->grade ?? '-',
                        'gpa' => $mark?->gpa ?? 0,
                        'is_absent' => $mark?->is_absent ?? false,
                    ];
                }

                $overall = GradeHelper::calculateGPA(array_map(fn($r) => [
                    'marks' => $r['marks'],
                    'full_marks' => $r['full_marks'],
                    'gpa' => $r['gpa'],
                ], $subjectResults));

                return [
                    'student' => $student,
                    'subjects' => $subjectResults,
                    'overall' => $overall,
                ];
            })->sortByDesc('overall.gpa')->values();

            // মেধাক্রম যোগ
            $resultData = $resultData->map(function ($row, $idx) {
                $row['position'] = $idx + 1;
                return $row;
            });
        }

        return view('admin.exams.result', compact(
            'exam', 'classes', 'sections', 'examSubjects', 'resultData', 'classId', 'sectionId'
        ));
    }

    /**
     * একটি শিক্ষার্থীর মার্কশিট
     */
    public function marksheet(Exam $exam, Student $student)
    {
        $examSubjects = ExamSubject::with('subject')
            ->where('exam_id', $exam->id)
            ->where('class_id', $student->class_id)
            ->get();

        $marks = Mark::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('exam_subject_id');

        $subjectResults = $examSubjects->map(function ($es) use ($marks) {
            $m = $marks->get($es->id);
            return [
                'exam_subject' => $es,
                'marks' => $m?->total_marks ?? 0,
                'full_marks' => $es->full_marks,
                'pass_marks' => $es->pass_marks,
                'grade' => $m?->grade ?? '-',
                'gpa' => $m?->gpa ?? 0,
                'is_absent' => $m?->is_absent ?? false,
            ];
        })->toArray();

        $overall = GradeHelper::calculateGPA(array_map(fn($r) => [
            'marks' => $r['marks'],
            'full_marks' => $r['full_marks'],
            'gpa' => $r['gpa'],
        ], $subjectResults));

        return view('admin.exams.marksheet', compact(
            'exam', 'student', 'subjectResults', 'overall'
        ));
    }
}
