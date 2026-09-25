<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    /**
     * হাজিরা নেওয়ার পেজ
     */
    public function index(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $students = collect();
        $existingAttendance = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $date = $request->attendance_date ?? now()->toDateString();

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
        }

        if ($classId && $date) {
            $students = Student::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $existingAttendance = StudentAttendance::where('attendance_date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('admin.attendance.students.index', compact(
            'classes', 'sections', 'students', 'existingAttendance',
            'classId', 'sectionId', 'date'
        ));
    }

    /**
     * হাজিরা সংরক্ষণ (Bulk)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'         => 'required|exists:classes,id',
            'section_id'       => 'nullable|exists:sections,id',
            'attendance_date'  => 'required|date',
            'statuses'         => 'required|array',
            'statuses.*'       => 'in:present,absent,late,leave,holiday',
            'remarks'          => 'nullable|array',
        ]);

        $currentYear = AcademicYear::where('is_current', true)->first();

        DB::beginTransaction();
        try {
            $counts = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0, 'holiday' => 0];

            foreach ($validated['statuses'] as $studentId => $status) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id'      => $studentId,
                        'attendance_date' => $validated['attendance_date'],
                    ],
                    [
                        'class_id'         => $validated['class_id'],
                        'section_id'       => $validated['section_id'] ?? null,
                        'academic_year_id' => $currentYear?->id,
                        'status'           => $status,
                        'remarks'          => $validated['remarks'][$studentId] ?? null,
                        'recorded_by'      => auth()->id(),
                    ]
                );
                $counts[$status] = ($counts[$status] ?? 0) + 1;
            }

            DB::commit();

            $msg = sprintf(
                'হাজিরা সংরক্ষণ হয়েছে — উপস্থিত: %d, অনুপস্থিত: %d, বিলম্ব: %d, ছুটি: %d',
                $counts['present'], $counts['absent'], $counts['late'], $counts['leave']
            );

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * মাসিক রিপোর্ট
     */
    public function report(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $students = collect();
        $reportData = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        if ($classId) {
            $sections = Section::where('class_id', $classId)->get();
        }

        if ($classId) {
            $students = Student::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $attendances = StudentAttendance::whereIn('student_id', $students->pluck('id'))
                ->whereBetween('attendance_date', [$start, $end])
                ->get()
                ->groupBy('student_id');

            $reportData = $students->map(function ($student) use ($attendances) {
                $records = $attendances->get($student->id, collect());
                return [
                    'student'  => $student,
                    'present'  => $records->where('status', 'present')->count(),
                    'absent'   => $records->where('status', 'absent')->count(),
                    'late'     => $records->where('status', 'late')->count(),
                    'leave'    => $records->where('status', 'leave')->count(),
                    'total'    => $records->count(),
                ];
            });
        }

        $months = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];

        return view('admin.attendance.students.report', compact(
            'classes', 'sections', 'reportData', 'classId', 'sectionId',
            'month', 'year', 'months'
        ));
    }

    /**
     * একজন শিক্ষার্থীর বিস্তারিত হাজিরা
     */
    public function studentReport(Request $request, Student $student)
    {
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

        $months = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];

        return view('admin.attendance.students.student-report', compact(
            'student', 'attendances', 'summary', 'month', 'year', 'months'
        ));
    }
}
