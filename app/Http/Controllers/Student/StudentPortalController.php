<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\FeeInvoice;
use App\Models\Mark;
use App\Models\StudentAttendance;
use App\Helpers\GradeHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    private function student()
    {
        return auth()->user()->student;
    }

    public function dashboard()
    {
        $student = $this->student();
        abort_unless($student, 403, 'স্টুডেন্ট প্রোফাইল নেই');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendance = StudentAttendance::where('student_id', $student->id)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear);

        $stats = [
            'present' => (clone $attendance)->where('status', 'present')->count(),
            'absent'  => (clone $attendance)->where('status', 'absent')->count(),
            'late'    => (clone $attendance)->where('status', 'late')->count(),
            'leave'   => (clone $attendance)->where('status', 'leave')->count(),
        ];

        $feeSummary = [
            'total' => FeeInvoice::where('student_id', $student->id)->sum('total_amount'),
            'paid'  => FeeInvoice::where('student_id', $student->id)->sum('paid_amount'),
            'due'   => FeeInvoice::where('student_id', $student->id)->sum('due_amount'),
        ];

        $recentInvoices = FeeInvoice::where('student_id', $student->id)->latest()->limit(5)->get();

        return view('student.dashboard', compact('student', 'stats', 'feeSummary', 'recentInvoices'));
    }

    public function attendance(Request $request)
    {
        $student = $this->student();
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $attendances = StudentAttendance::where('student_id', $student->id)
            ->whereBetween('attendance_date', [$start, $end])
            ->orderBy('attendance_date')
            ->get();

        $summary = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent'  => $attendances->where('status', 'absent')->count(),
            'late'    => $attendances->where('status', 'late')->count(),
            'leave'   => $attendances->where('status', 'leave')->count(),
        ];

        $months = [1=>'জানুয়ারি',2=>'ফেব্রুয়ারি',3=>'মার্চ',4=>'এপ্রিল',5=>'মে',6=>'জুন',7=>'জুলাই',8=>'আগস্ট',9=>'সেপ্টেম্বর',10=>'অক্টোবর',11=>'নভেম্বর',12=>'ডিসেম্বর'];

        return view('student.attendance', compact('student', 'attendances', 'summary', 'month', 'year', 'months'));
    }

    public function results()
    {
        $student = $this->student();

        $exams = Exam::where('is_published', true)
            ->whereHas('marks', fn($q) => $q->where('student_id', $student->id))
            ->latest()
            ->get();

        $examResults = $exams->map(function ($exam) use ($student) {
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
                    'grade' => $m?->grade ?? '-',
                    'gpa' => $m?->gpa ?? 0,
                ];
            })->toArray();

            $overall = GradeHelper::calculateGPA(array_map(fn($r) => [
                'marks' => $r['marks'],
                'full_marks' => $r['full_marks'],
                'gpa' => $r['gpa'],
            ], $subjects));

            return compact('exam', 'subjects', 'overall');
        });

        return view('student.results', compact('student', 'examResults'));
    }

    public function fees()
    {
        $student = $this->student();

        $invoices = FeeInvoice::with(['items.category', 'payments'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $summary = [
            'total' => $invoices->sum('total_amount'),
            'paid'  => $invoices->sum('paid_amount'),
            'due'   => $invoices->sum('due_amount'),
        ];

        return view('student.fees', compact('student', 'invoices', 'summary'));
    }

    public function profile()
    {
        $student = $this->student();
        return view('student.profile', compact('student'));
    }
}
