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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_active', true)->latest()->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        return view('admin.report-cards.index', compact('exams', 'classes'));
    }

    /**
     * একক রিপোর্ট কার্ড
     */
    public function single(Exam $exam, Student $student)
    {
        $data = $this->buildStudentData($exam, $student);
        $pdf = Pdf::loadView('admin.pdf.report-card', array_merge($data, ['exam' => $exam]));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("report-card-{$student->student_id}.pdf");
    }

    /**
     * পুরো ক্লাসের রিপোর্ট কার্ড (একটি PDF এ সব)
     */
    public function bulk(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $students = Student::where('class_id', $validated['class_id'])
            ->when($validated['section_id'], fn($q) => $q->where('section_id', $validated['section_id']))
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'কোনো শিক্ষার্থী পাওয়া যায়নি।');
        }

        $allData = $students->map(fn($s) => $this->buildStudentData($exam, $s))->toArray();

        $class = SchoolClass::find($validated['class_id']);
        $section = $validated['section_id'] ? Section::find($validated['section_id']) : null;

        $pdf = Pdf::loadView('admin.pdf.report-card-bulk', [
            'exam' => $exam,
            'class' => $class,
            'section' => $section,
            'studentsData' => $allData,
        ]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("report-cards-class-{$class->name}-{$exam->id}.pdf");
    }

    /**
     * একসাথে সব PDF merge করার জন্য data structure
     */
    private function buildStudentData(Exam $exam, Student $student): array
    {
        $examSubjects = ExamSubject::with('subject')
            ->where('exam_id', $exam->id)
            ->where('class_id', $student->class_id)
            ->get();

        $marks = Mark::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('exam_subject_id');

        $subjects = $examSubjects->map(function ($es) use ($marks) {
            $m = $marks->get($es->id);
            return [
                'subject' => $es->subject->name ?? '-',
                'marks' => $m?->total_marks ?? 0,
                'full_marks' => $es->full_marks,
                'pass_marks' => $es->pass_marks,
                'grade' => $m?->grade ?? '-',
                'gpa' => $m?->gpa ?? 0,
            ];
        })->toArray();

        $overall = GradeHelper::calculateGPA(array_map(fn($r) => [
            'marks' => $r['marks'],
            'full_marks' => $r['full_marks'],
            'gpa' => $r['gpa'],
        ], $subjects));

        // ক্লাস পজিশন বের করা
        $position = null;
        $classId = $student->class_id;
        if ($classId) {
            $allMarks = Mark::where('exam_id', $exam->id)
                ->where('class_id', $classId)
                ->get()
                ->groupBy('student_id');

            $gpas = [];
            foreach ($allMarks as $sid => $sMarks) {
                $avg = $sMarks->avg('gpa') ?? 0;
                $gpas[$sid] = round($avg, 2);
            }
            arsort($gpas);
            $position = array_search($student->id, array_keys($gpas));
            $position = $position !== false ? $position + 1 : null;
        }

        return [
            'student' => $student,
            'subjects' => $subjects,
            'overall' => $overall,
            'position' => $position,
        ];
    }
}
