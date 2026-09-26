<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Helpers\GradeHelper;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\FeeInvoice;
use App\Models\Mark;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class GuardianPortalController extends Controller
{
    private function guardian()
    {
        return auth()->user()->guardian ?? \App\Models\Guardian::where('user_id', auth()->id())->first();
    }

    public function dashboard()
    {
        $guardian = $this->guardian();
        abort_unless($guardian, 403, 'গার্ডিয়ান প্রোফাইল নেই');

        $children = $guardian->students()->with(['schoolClass', 'section'])->get();

        return view('guardian.dashboard', compact('guardian', 'children'));
    }

    public function childOverview($studentId)
    {
        $guardian = $this->guardian();
        $student = $guardian->students()->with(['schoolClass', 'section', 'academicYear'])->findOrFail($studentId);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendance = StudentAttendance::where('student_id', $student->id)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear);

        $stats = [
            'present' => (clone $attendance)->where('status', 'present')->count(),
            'absent'  => (clone $attendance)->where('status', 'absent')->count(),
            'late'    => (clone $attendance)->where('status', 'late')->count(),
        ];

        $feeSummary = [
            'total' => FeeInvoice::where('student_id', $student->id)->sum('total_amount'),
            'paid'  => FeeInvoice::where('student_id', $student->id)->sum('paid_amount'),
            'due'   => FeeInvoice::where('student_id', $student->id)->sum('due_amount'),
        ];

        // সর্বশেষ পরীক্ষার রেজাল্ট
        $lastExam = Exam::where('is_published', true)
            ->whereHas('marks', fn($q) => $q->where('student_id', $student->id))
            ->latest()->first();

        $lastResult = null;
        if ($lastExam) {
            $examSubjects = ExamSubject::with('subject')
                ->where('exam_id', $lastExam->id)
                ->where('class_id', $student->class_id)->get();
            $marks = Mark::where('exam_id', $lastExam->id)
                ->where('student_id', $student->id)
                ->get()->keyBy('exam_subject_id');
            $subjects = $examSubjects->map(function ($es) use ($marks) {
                $m = $marks->get($es->id);
                return [
                    'marks' => $m?->total_marks ?? 0,
                    'full_marks' => $es->full_marks,
                    'gpa' => $m?->gpa ?? 0,
                ];
            })->toArray();
            $lastResult = GradeHelper::calculateGPA($subjects);
        }

        $recentInvoices = FeeInvoice::where('student_id', $student->id)->latest()->limit(5)->get();

        return view('guardian.child-overview', compact(
            'guardian', 'student', 'stats', 'feeSummary', 'lastResult', 'recentInvoices', 'lastExam'
        ));
    }
}
