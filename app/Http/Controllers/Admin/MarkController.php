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
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    /**
     * মার্ক এন্ট্রি পেজ
     */
    public function index(Request $request, Exam $exam)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $examSubjects = collect();
        $students = collect();
        $existingMarks = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $examSubjectId = $request->exam_subject_id;

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
            $examSubjects = ExamSubject::with('subject')
                ->where('exam_id', $exam->id)
                ->where('class_id', $classId)
                ->get();
        }

        if ($classId && $examSubjectId) {
            $students = Student::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $existingMarks = Mark::where('exam_subject_id', $examSubjectId)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('admin.exams.marks-entry', compact(
            'exam', 'classes', 'sections', 'examSubjects', 'students',
            'existingMarks', 'classId', 'sectionId', 'examSubjectId'
        ));
    }

    /**
     * মার্ক সংরক্ষণ (bulk)
     */
    public function store(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'exam_subject_id' => 'required|exists:exam_subjects,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'written' => 'nullable|array',
            'mcq' => 'nullable|array',
            'practical' => 'nullable|array',
            'is_absent' => 'nullable|array',
            'remarks' => 'nullable|array',
        ]);

        $examSubject = ExamSubject::findOrFail($validated['exam_subject_id']);
        $fullMarks = $examSubject->full_marks;
        $passMarks = $examSubject->pass_marks;

        DB::beginTransaction();
        try {
            $studentIds = array_unique(array_merge(
                array_keys($validated['written'] ?? []),
                array_keys($validated['mcq'] ?? []),
                array_keys($validated['practical'] ?? []),
            ));

            foreach ($studentIds as $sid) {
                $written = (float)($validated['written'][$sid] ?? 0);
                $mcq = (float)($validated['mcq'][$sid] ?? 0);
                $practical = (float)($validated['practical'][$sid] ?? 0);
                $isAbsent = !empty($validated['is_absent'][$sid]);

                $total = $isAbsent ? 0 : ($written + $mcq + $practical);
                $gradeData = $isAbsent
                    ? ['grade' => 'F', 'gpa' => 0.00]
                    : GradeHelper::getGrade($total, $fullMarks);

                Mark::updateOrCreate(
                    [
                        'exam_subject_id' => $examSubject->id,
                        'student_id' => $sid,
                    ],
                    [
                        'exam_id' => $exam->id,
                        'class_id' => $validated['class_id'],
                        'section_id' => $validated['section_id'] ?? null,
                        'written_marks' => $written,
                        'mcq_marks' => $mcq,
                        'practical_marks' => $practical,
                        'total_marks' => $total,
                        'grade' => $gradeData['grade'],
                        'gpa' => $gradeData['gpa'],
                        'is_absent' => $isAbsent,
                        'remarks' => $validated['remarks'][$sid] ?? null,
                        'entered_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'নম্বর সংরক্ষণ হয়েছে। মোট ' . count($studentIds) . ' জন।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
