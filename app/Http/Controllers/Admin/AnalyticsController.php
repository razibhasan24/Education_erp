<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * ড্যাশবোর্ড চার্ট ডেটা (JSON)
     */
    public function dashboardData()
    {
        return response()->json([
            'students_by_class' => $this->studentsByClass(),
            'attendance_trend' => $this->attendanceTrend(),
            'fee_collection' => $this->feeCollection(),
            'grade_distribution' => $this->gradeDistribution(),
            'summary' => $this->summary(),
        ]);
    }

    private function summary(): array
    {
        $today = now()->toDateString();
        return [
            'total_students' => Student::where('status', 'active')->count(),
            'total_teachers' => Teacher::where('status', 'active')->count(),
            'total_classes' => SchoolClass::where('is_active', true)->count(),
            'present_today' => StudentAttendance::where('attendance_date', $today)
                ->where('status', 'present')->count(),
            'absent_today' => StudentAttendance::where('attendance_date', $today)
                ->where('status', 'absent')->count(),
            'fee_collected_month' => FeePayment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)->sum('amount'),
            'fee_due_total' => \App\Models\FeeInvoice::sum('due_amount'),
        ];
    }

    private function studentsByClass()
    {
        return SchoolClass::where('is_active', true)
            ->orderBy('numeric_value')
            ->withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->get()
            ->map(fn($c) => [
                'label' => $c->name,
                'count' => $c->students_count,
            ]);
    }

    private function attendanceTrend()
    {
        $days = [];
        $present = [];
        $absent = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('d M');

            $records = StudentAttendance::whereDate('attendance_date', $date->toDateString());
            $present[] = (clone $records)->where('status', 'present')->count();
            $absent[] = (clone $records)->where('status', 'absent')->count();
        }

        return [
            'labels' => $days,
            'present' => $present,
            'absent' => $absent,
        ];
    }

    private function feeCollection()
    {
        $data = FeePayment::selectRaw('YEAR(payment_date) year, MONTH(payment_date) month, SUM(amount) total')
            ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $amounts = [];

        foreach ($data as $row) {
            $labels[] = date('M Y', mktime(0, 0, 0, $row->month, 1, $row->year));
            $amounts[] = (float) $row->total;
        }

        return compact('labels', 'amounts');
    }

    private function gradeDistribution()
    {
        $marks = Mark::select('grade', DB::raw('COUNT(*) as count'))
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->orderByRaw("FIELD(grade, 'A+', 'A', 'A-', 'B', 'C', 'D', 'F')")
            ->get();

        return [
            'labels' => $marks->pluck('grade'),
            'counts' => $marks->pluck('count'),
        ];
    }
}
