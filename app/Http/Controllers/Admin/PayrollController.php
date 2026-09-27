<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryPayment;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::where('status', 'active')->get();

        $query = SalaryPayment::with('teacher');
        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year')) $query->where('year', $request->year);
        if ($request->filled('teacher_id')) $query->where('teacher_id', $request->teacher_id);
        $payments = $query->latest()->get();

        $summary = [
            'total' => $payments->sum('net_salary'),
            'count' => $payments->count(),
        ];

        $months = [1=>'জানুয়ারি',2=>'ফেব্রুয়ারি',3=>'মার্চ',4=>'এপ্রিল',5=>'মে',6=>'জুন',7=>'জুলাই',8=>'আগস্ট',9=>'সেপ্টেম্বর',10=>'অক্টোবর',11=>'নভেম্বর',12=>'ডিসেম্বর'];

        return view('admin.payroll.index', compact('payments', 'teachers', 'summary', 'months'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'month' => 'required|string|max:2',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:30',
            'remarks' => 'nullable|string',
        ]);

        $exists = SalaryPayment::where('teacher_id', $data['teacher_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'এই মাসের বেতন ইতিমধ্যে দেওয়া হয়েছে।');
        }

        $data['allowance'] = $data['allowance'] ?? 0;
        $data['deduction'] = $data['deduction'] ?? 0;
        $data['net_salary'] = $data['basic_salary'] + $data['allowance'] - $data['deduction'];
        $data['voucher_no'] = SalaryPayment::generateVoucherNo();
        $data['paid_by'] = auth()->id();

        SalaryPayment::create($data);
        return back()->with('success', 'বেতন পরিশোধ হয়েছে।');
    }

    public function destroy(SalaryPayment $payment)
    {
        $payment->delete();
        return back()->with('success', 'বেতন রেকর্ড মুছে ফেলা হয়েছে।');
    }

    public function payslip(SalaryPayment $payment)
    {
        $payment->load('teacher', 'paidBy' ?? null);
        return view('admin.payroll.payslip', compact('payment'));
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|string|max:2',
            'year' => 'required|integer',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:30',
        ]);

        $teachers = Teacher::where('status', 'active')->whereNotNull('basic_salary')->get();

        $created = 0;
        $skipped = 0;

        foreach ($teachers as $teacher) {
            $exists = SalaryPayment::where('teacher_id', $teacher->id)
                ->where('month', $data['month'])->where('year', $data['year'])->exists();
            if ($exists) { $skipped++; continue; }

            SalaryPayment::create([
                'voucher_no' => SalaryPayment::generateVoucherNo(),
                'teacher_id' => $teacher->id,
                'month' => $data['month'],
                'year' => $data['year'],
                'basic_salary' => $teacher->basic_salary,
                'allowance' => 0,
                'deduction' => 0,
                'net_salary' => $teacher->basic_salary,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'paid_by' => auth()->id(),
            ]);
            $created++;
        }

        return back()->with('success', "সফল: {$created}টি, স্কিপ: {$skipped}টি");
    }
}
