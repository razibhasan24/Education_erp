<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = FeePayment::with(['student', 'invoice']);

        if ($request->filled('from')) $query->where('payment_date', '>=', $request->from);
        if ($request->filled('to')) $query->where('payment_date', '<=', $request->to);
        if ($request->filled('method')) $query->where('payment_method', $request->method);

        $payments = $query->latest()->get();

        $summary = [
            'total' => $payments->sum('amount'),
            'discount' => $payments->sum('discount'),
            'fine' => $payments->sum('fine'),
            'count' => $payments->count(),
        ];

        return view('admin.fees.payments.index', compact('payments', 'summary'));
    }

    /**
     * ইনভয়েসের জন্য পেমেন্ট
     */
    public function create(FeeInvoice $invoice)
    {
        $invoice->load(['student', 'items.category', 'payments']);
        return view('admin.fees.payments.create', compact('invoice'));
    }

    public function store(Request $request, FeeInvoice $invoice)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,bkash,nagad,rocket,bank,card,cheque',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        if ($validated['amount'] > $invoice->due_amount) {
            return back()->with('error', 'পেমেন্টের পরিমাণ বকেয়ার চেয়ে বেশি হতে পারে না।')->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = FeePayment::create([
                'receipt_no' => FeePayment::generateReceiptNo(),
                'fee_invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'discount' => $validated['discount'] ?? 0,
                'fine' => $validated['fine'] ?? 0,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'received_by' => auth()->id(),
            ]);

            // ইনভয়েস আপডেট
            $paid = $invoice->paid_amount + $validated['amount'];
            $due = $invoice->total_amount - $paid;

            $invoice->update([
                'paid_amount' => $paid,
                'due_amount' => $due,
                'status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);

            DB::commit();
            return redirect()->route('admin.fees.payments.receipt', $payment)
                ->with('success', 'পেমেন্ট সংরক্ষণ হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * প্রিন্টেবল রিসিট
     */
    public function receipt(FeePayment $payment)
    {
        $payment->load(['student.schoolClass', 'student.section', 'invoice.items.category', 'receiver']);
        return view('admin.fees.payments.receipt', compact('payment'));
    }

    public function destroy(FeePayment $payment)
    {
        DB::beginTransaction();
        try {
            $invoice = $payment->invoice;
            $paid = $invoice->paid_amount - $payment->amount;
            $due = $invoice->total_amount - $paid;

            $invoice->update([
                'paid_amount' => $paid,
                'due_amount' => $due,
                'status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);

            $payment->delete();
            DB::commit();
            return back()->with('success', 'পেমেন্ট বাতিল হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
