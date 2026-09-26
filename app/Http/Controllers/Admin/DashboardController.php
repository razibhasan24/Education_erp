<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_teachers' => Teacher::where('status', 'active')->count(),
            'total_classes' => SchoolClass::where('is_active', true)->count(),
            'total_subjects' => Subject::where('is_active', true)->count(),
            'present_today' => StudentAttendance::where('attendance_date', $today)->where('status', 'present')->count(),
            'absent_today' => StudentAttendance::where('attendance_date', $today)->where('status', 'absent')->count(),
            'fee_collected_month' => FeePayment::whereMonth('payment_date', $currentMonth)
                ->whereYear('payment_date', $currentYear)->sum('amount'),
            'fee_due_total' => FeeInvoice::sum('due_amount'),
        ];

        // সাম্প্রতিক ভর্তি
        $recentStudents = Student::with(['schoolClass', 'section'])
            ->latest()->limit(5)->get();

        // সাম্প্রতিক পেমেন্ট
        $recentPayments = FeePayment::with('student')
            ->latest()->limit(5)->get();

        // আজকের হাজিরা চেক
        $todayAttendanceTaken = StudentAttendance::where('attendance_date', $today)->exists();

        return view('admin.dashboard', compact(
            'stats', 'recentStudents', 'recentPayments', 'todayAttendanceTaken'
        ));
    }
}
