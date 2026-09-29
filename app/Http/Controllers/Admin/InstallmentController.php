<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeInstallment;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Services\LateFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function __construct(private LateFeeService $lateFeeService) {}

    /**
     * একটি ইনভয়েসকে কিস্তিতে ভাগ করা
     */
    public function create(FeeInvoice $invoice)
    {
        $invoice->load('installments');
        return view('admin.fees.installments.create', compact('invoice'));
    }

    public function store(Request $request, FeeInvoice $invoice)
    {
        $data = $request->validate([
            'installments' => 'required|array|min:2',
            'installments.*.amount' => 'required|numeric|min:0.01',
            'installments.*.due_date' => 'required|date',
        ]);

        $totalAmount = collect($data['installments'])->sum('amount');

        if (round($totalAmount, 2) != round($invoice->total_amount, 2)) {
            return back()->with('error', "কিস্তির মোট ({$totalAmount}) invoice total ({$invoice->total_amount}) এর সমান নয়।")->withInput();
        }

        DB::beginTransaction();
        try {
            // পুরোনো কিস্তি থাকলে মুছে ফেলুন
            $invoice->installments()->delete();

            foreach ($data['installments'] as $i => $inst) {
                FeeInstallment::create([
                    'fee_invoice_id' => $invoice->id,
                    'installment_no' => $i + 1,
                    'amount' => $inst['amount'],
                    'due_date' => $inst['due_date'],
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.fees.invoices.show', $invoice)
                ->with('success', 'কিস্তি প্ল্যান তৈরি হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(FeeInvoice $invoice)
    {
        $invoice->installments()->delete();
        return back()->with('success', 'কিস্তি প্ল্যান মুছে ফেলা হয়েছে।');
    }

    /**
     * একটি কিস্তিতে পেমেন্ট
     */
    public function pay(Request $request, FeeInstallment $installment)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice = $installment->invoice;

            // FeePayment তৈরি
            $payment = FeePayment::create([
                'receipt_no' => FeePayment::generateReceiptNo(),
                'fee_invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'discount' => 0,
                'fine' => $installment->late_fee,
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'remarks' => "Installment #{$installment->installment_no} payment",
                'received_by' => auth()->id(),
            ]);

            // Installment আপডেট
            $paid = $installment->paid_amount + $data['amount'];
            $totalDue = $installment->amount + $installment->late_fee;
            $status = $paid >= $totalDue ? 'paid' : ($paid > 0 ? 'partial' : 'pending');

            $installment->update([
                'paid_amount' => $paid,
                'status' => $status,
                'paid_date' => $status === 'paid' ? $data['payment_date'] : null,
            ]);

            // Invoice আপডেট
            $invPaid = $invoice->paid_amount + $data['amount'];
            $invDue = $invoice->total_amount - $invPaid;

            $invoice->update([
                'paid_amount' => $invPaid,
                'due_amount' => max(0, $invDue),
                'status' => $invDue <= 0 ? 'paid' : 'partial',
            ]);

            // Accounting
            try {
                app(\App\Services\AccountingService::class)->recordFeePayment($payment);
            } catch (\Exception $e) {
                \Log::warning('Journal entry failed: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', 'কিস্তি পরিশোধ হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * সব installment এ late fee update
     */
    public function updateLateFees()
    {
        $updated = $this->lateFeeService->updateAllInstallments();
        return back()->with('success', "{$updated}টি installment এ late fee আপডেট হয়েছে।");
    }
}