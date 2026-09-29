<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\FeeRefund;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeRefund::with(['student', 'invoice', 'approvedBy']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('student_id')) $query->where('student_id', $request->student_id);
        $refunds = $query->latest()->paginate(50);

        $summary = [
            'total' => FeeRefund::where('status', 'paid')->sum('amount'),
            'pending' => FeeRefund::where('status', 'pending')->count(),
        ];

        $students = Student::where('status', 'active')->get();
        return view('admin.fees.refunds.index', compact('refunds', 'summary', 'students'));
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'active')->get();
        $selectedStudent = $request->student_id ? Student::find($request->student_id) : null;
        $payments = $selectedStudent
            ? FeePayment::where('student_id', $selectedStudent->id)->latest()->get()
            : collect();

        return view('admin.fees.refunds.create', compact('students', 'selectedStudent', 'payments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_payment_id' => 'nullable|exists:fee_payments,id',
            'fee_invoice_id' => 'nullable|exists:fee_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'refund_date' => 'required|date',
            'refund_method' => 'required|in:cash,bkash,nagad,bank,cheque,adjustment',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        // Check — refund amount payment amount এর বেশি কিনা
        if (!empty($data['fee_payment_id'])) {
            $payment = FeePayment::find($data['fee_payment_id']);
            $alreadyRefunded = FeeRefund::where('fee_payment_id', $payment->id)
                ->whereIn('status', ['approved', 'paid'])
                ->sum('amount');

            if (($alreadyRefunded + $data['amount']) > $payment->amount) {
                return back()->with('error', 'Refund amount payment amount এর বেশি হতে পারে না।')->withInput();
            }
        }

        $data['refund_no'] = FeeRefund::generateRefundNo();
        $data['status'] = 'pending';
        $data['requested_by'] = auth()->id();

        FeeRefund::create($data);

        return redirect()->route('admin.fees.refunds.index')->with('success', 'Refund request জমা হয়েছে।');
    }

    public function approve(FeeRefund $refund)
    {
        $refund->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'অনুমোদিত।');
    }

    public function reject(Request $request, FeeRefund $refund)
    {
        $refund->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);
        return back()->with('success', 'বাতিল হয়েছে।');
    }

    /**
     * Refund paid হিসেবে চিহ্নিত + Accounting entry
     */
    public function markPaid(FeeRefund $refund)
    {
        if ($refund->status !== 'approved') {
            return back()->with('error', 'আগে approve করুন।');
        }

        DB::beginTransaction();
        try {
            $refund->update(['status' => 'paid']);

            // Accounting Entry: Refund Expense (Debit) / Cash (Credit)
            $expenseAccount = \App\Models\Account::where('code', '5900')->first(); // Misc Expense
            $cashAccount = \App\Models\Account::where('code', '1000')->first();

            if ($expenseAccount && $cashAccount) {
                \App\Models\JournalEntry::record([
                    'entry_date' => $refund->refund_date,
                    'reference' => $refund->refund_no,
                    'narration' => "Fee Refund to {$refund->student->name} — {$refund->reason}",
                    'type' => 'refund',
                    'source_type' => FeeRefund::class,
                    'source_id' => $refund->id,
                ], [
                    ['account_id' => $expenseAccount->id, 'debit' => $refund->amount, 'credit' => 0, 'description' => 'Refund Expense'],
                    ['account_id' => $cashAccount->id, 'debit' => 0, 'credit' => $refund->amount, 'description' => 'Cash Paid'],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Refund Paid হিসেবে চিহ্নিত হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(FeeRefund $refund)
    {
        $refund->load(['student', 'invoice', 'payment', 'approvedBy']);
        return view('admin.fees.refunds.show', compact('refund'));
    }

    public function destroy(FeeRefund $refund)
    {
        if ($refund->status === 'paid') {
            return back()->with('error', 'Paid refund মুছে ফেলা যাবে না।');
        }
        $refund->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }
}