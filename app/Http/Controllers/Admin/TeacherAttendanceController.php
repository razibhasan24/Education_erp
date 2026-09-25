<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->attendance_date ?? now()->toDateString();
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        $existing = TeacherAttendance::where('attendance_date', $date)
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->get()
            ->keyBy('teacher_id');

        return view('admin.attendance.teachers.index', compact('teachers', 'existing', 'date'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'statuses'        => 'required|array',
            'statuses.*'      => 'in:present,absent,late,leave,holiday',
            'in_time'         => 'nullable|array',
            'out_time'        => 'nullable|array',
            'remarks'         => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['statuses'] as $teacherId => $status) {
                TeacherAttendance::updateOrCreate(
                    ['teacher_id' => $teacherId, 'attendance_date' => $validated['attendance_date']],
                    [
                        'in_time'    => $validated['in_time'][$teacherId] ?? null,
                        'out_time'   => $validated['out_time'][$teacherId] ?? null,
                        'status'     => $status,
                        'remarks'    => $validated['remarks'][$teacherId] ?? null,
                        'recorded_by'=> auth()->id(),
                    ]
                );
            }
            DB::commit();
            return back()->with('success', 'শিক্ষকদের হাজিরা সংরক্ষণ হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        $attendances = TeacherAttendance::whereBetween('attendance_date', [$start, $end])
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->get()
            ->groupBy('teacher_id');

        $reportData = $teachers->map(function ($teacher) use ($attendances) {
            $records = $attendances->get($teacher->id, collect());
            return [
                'teacher' => $teacher,
                'present' => $records->where('status', 'present')->count(),
                'absent'  => $records->where('status', 'absent')->count(),
                'late'    => $records->where('status', 'late')->count(),
                'leave'   => $records->where('status', 'leave')->count(),
                'total'   => $records->count(),
            ];
        });

        $months = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];

        return view('admin.attendance.teachers.report', compact(
            'reportData', 'month', 'year', 'months'
        ));
    }
}
