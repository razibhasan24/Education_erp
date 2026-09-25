<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Expense;
use App\Models\SchoolClass;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeReportController extends Controller
{
    public function dueList(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_value')->get();
        $sections = collect();
        $invoices = collect();

        $classId = $request->class_id;
        $sectionId = $request->section_id;

        if ($classId) {
            $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
        }

        $query = FeeInvoice::with(['student', 'schoolClass', 'section'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_amount', '>', 0);

        if ($classId) $query->where('class_id', $classId);
        if ($sectionId) $query->where('section_id', $sectionId);
        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year')) $query->where('year', $request->year);

        $invoices = $query->orderBy('due_amount', 'desc')->get();

        $months = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];

        $totalDue = $invoices->sum('due_amount');

        return view('admin.fees.reports.due', compact(
            'invoices', 'classes', 'sections', 'classId', 'sectionId', 'months', 'totalDue'
        ));
    }

    public function incomeExpense(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();

        $totalIncome = FeePayment::whereBetween('payment_date', [$from, $to])->sum('amount');
        $totalExpense = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $totalDiscount = FeePayment::whereBetween('payment_date', [$from, $to])->sum('discount');
        $totalFine = FeePayment::whereBetween('payment_date', [$from, $to])->sum('fine');

        // মাসিক ব্রেকডাউন
        $monthlyIncome = FeePayment::selectRaw('YEAR(payment_date) year, MONTH(payment_date) month, SUM(amount) total')
            ->whereBetween('payment_date', [$from, $to])
            ->groupBy('year', 'month')->orderBy('year')->orderBy('month')->get();

        $monthlyExpense = Expense::selectRaw('YEAR(expense_date) year, MONTH(expense_date) month, SUM(amount) total')
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('year', 'month')->orderBy('year')->orderBy('month')->get();

        return view('admin.fees.reports.income-expense', compact(
            'from', 'to', 'totalIncome', 'totalExpense', 'totalDiscount', 'totalFine',
            'monthlyIncome', 'monthlyExpense'
        ));
    }

    /**
     * একটি শিক্ষার্থীর সম্পূর্ণ ফি ইতিহাস
     */
    public function studentLedger(Request $request, \App\Models\Student $student)
    {
        $invoices = FeeInvoice::with(['items.category', 'payments'])
            ->where('student_id', $student->id)
            ->latest()->get();

        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalDue = $invoices->sum('due_amount');

        return view('admin.fees.reports.student-ledger', compact(
            'student', 'invoices', 'totalBilled', 'totalPaid', 'totalDue'
        ));
    }
}
