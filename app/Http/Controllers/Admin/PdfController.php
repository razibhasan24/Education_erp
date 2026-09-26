<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GradeHelper;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * মার্কশিট PDF
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
        ], $subjectResults));

        $pdf = Pdf::loadView('admin.pdf.marksheet', compact(
            'exam', 'student', 'subjectResults', 'overall'
        ));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("marksheet-{$student->student_id}-{$exam->id}.pdf");
    }

    /**
     * মানি রিসিট PDF
     */
    public function receipt(FeePayment $payment)
    {
        $payment->load(['student.schoolClass', 'student.section', 'invoice.items.category', 'receiver']);

        $pdf = Pdf::loadView('admin.pdf.receipt', compact('payment'));
        $pdf->setPaper('A5', 'portrait');

        return $pdf->download("receipt-{$payment->receipt_no}.pdf");
    }

    /**
     * ক্লাস রেজাল্ট শীট PDF
     */
    public function resultSheet(Request $request, Exam $exam)
    {
        $classId = $request->class_id;
        $sectionId = $request->section_id;

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

        $results = $students->map(function ($student) use ($marks, $examSubjects) {
            $studentMarks = $marks->get($student->id, collect());
            $subjects = [];
            foreach ($examSubjects as $es) {
                $m = $studentMarks->firstWhere('exam_subject_id', $es->id);
                $subjects[] = [
                    'subject' => $es->subject->name ?? '-',
                    'marks' => $m?->total_marks ?? 0,
                    'full_marks' => $es->full_marks,
                    'grade' => $m?->grade ?? '-',
                    'gpa' => $m?->gpa ?? 0,
                ];
            }
            $overall = GradeHelper::calculateGPA(array_map(fn($r) => [
                'marks' => $r['marks'],
                'full_marks' => $r['full_marks'],
                'gpa' => $r['gpa'],
            ], $subjects));
            return compact('student', 'subjects', 'overall');
        })->sortByDesc('overall.gpa')->values();

        $class = SchoolClass::find($classId);
        $section = $sectionId ? Section::find($sectionId) : null;
        $exam->load('academicYear');

        $pdf = Pdf::loadView('admin.pdf.result-sheet', compact(
            'exam', 'class', 'section', 'results', 'examSubjects'
        ));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("result-{$exam->id}-class-{$classId}.pdf");
    }

    /**
     * বকেয়া তালিকা PDF
     */
    public function dueList(Request $request)
    {
        $invoices = FeeInvoice::with(['student', 'schoolClass', 'section'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_amount', '>', 0)
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id))
            ->when($request->section_id, fn($q) => $q->where('section_id', $request->section_id))
            ->orderBy('due_amount', 'desc')
            ->get();

        $totalDue = $invoices->sum('due_amount');

        $pdf = Pdf::loadView('admin.pdf.due-list', compact('invoices', 'totalDue'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('due-list-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Attendance Report PDF
     */
    public function attendanceReport(Request $request)
    {
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $students = Student::where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get();

        $attendances = \App\Models\StudentAttendance::whereIn('student_id', $students->pluck('id'))
            ->whereBetween('attendance_date', [$start, $end])
            ->get()
            ->groupBy('student_id');

        $reportData = $students->map(function ($student) use ($attendances) {
            $records = $attendances->get($student->id, collect());
            return [
                'student' => $student,
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'leave' => $records->where('status', 'leave')->count(),
                'total' => $records->count(),
            ];
        });

        $class = SchoolClass::find($classId);
        $section = $sectionId ? Section::find($sectionId) : null;

        $pdf = Pdf::loadView('admin.pdf.attendance-report', compact(
            'class', 'section', 'reportData', 'month', 'year'
        ));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("attendance-{$classId}-{$month}-{$year}.pdf");
    }
}
