<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\PaymentTransaction;
use App\Models\Student;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * স্টুডেন্ট পোর্টাল থেকে পেমেন্ট শুরু
     */
    public function initiate(Request $request, FeeInvoice $invoice, SslCommerzService $service)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student || $invoice->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }

        if ($invoice->due_amount <= 0) {
            return back()->with('error', 'এই ইনভয়েসে কোনো বকেয়া নেই।');
        }

        $result = $service->initiatePayment($invoice, $student);

        if ($result['success']) {
            return redirect()->away($result['url']);
        }

        return back()->with('error', $result['message'] ?? 'পেমেন্ট শুরু করা যায়নি।');
    }

    /**
     * সফল পেমেন্ট
     */
    public function success(Request $request, SslCommerzService $service)
    {
        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');

        $transaction = PaymentTransaction::where('transaction_id', $tranId)->firstOrFail();

        // গেটওয়ে থেকে ভ্যালিডেশন
        $validation = $service->validatePayment($valId);

        $status = strtoupper($validation['status'] ?? '');
        $valid = in_array($status, ['VALID', 'VALIDATED']);

        if ($valid) {
            DB::beginTransaction();
            try {
                $invoice = $transaction->invoice;

                // FeePayment রেকর্ড তৈরি
                $payment = FeePayment::create([
                    'receipt_no' => FeePayment::generateReceiptNo(),
                    'fee_invoice_id' => $invoice->id,
                    'student_id' => $invoice->student_id,
                    'payment_date' => now()->toDateString(),
                    'amount' => $transaction->amount,
                    'discount' => 0,
                    'fine' => 0,
                    'payment_method' => 'card',
                    'transaction_id' => $valId,
                    'remarks' => 'Online Payment via SSLCommerz (' . ($validation['card_type'] ?? '') . ')',
                    'received_by' => null,
                ]);

                // ইনভয়েস আপডেট
                $paid = $invoice->paid_amount + $transaction->amount;
                $due = $invoice->total_amount - $paid;

                $invoice->update([
                    'paid_amount' => $paid,
                    'due_amount' => $due,
                    'status' => $due <= 0 ? 'paid' : 'partial',
                ]);

                $transaction->update([
                    'status' => 'success',
                    'gateway_transaction_id' => $valId,
                    'payment_method' => $validation['card_type'] ?? 'Online',
                    'gateway_response' => json_encode($validation),
                    'fee_payment_id' => $payment->id,
                ]);

                DB::commit();

                return redirect()->route('student.fees')
                    ->with('success', 'পেমেন্ট সফল হয়েছে! রিসিট নম্বর: ' . $payment->receipt_no);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Payment success error: ' . $e->getMessage());
                return redirect()->route('student.fees')->with('error', 'পেমেন্ট সম্পন্ন হলেও সেভ করা যায়নি। সাপোর্টে যোগাযোগ করুন।');
            }
        }

        $transaction->update(['status' => 'failed', 'gateway_response' => json_encode($validation)]);
        return redirect()->route('student.fees')->with('error', 'পেমেন্ট ভ্যালিডেশন ব্যর্থ।');
    }

    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');
        PaymentTransaction::where('transaction_id', $tranId)->update([
            'status' => 'failed',
            'gateway_response' => json_encode($request->all()),
        ]);
        return redirect()->route('student.fees')->with('error', 'পেমেন্ট ব্যর্থ হয়েছে।');
    }

    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');
        PaymentTransaction::where('transaction_id', $tranId)->update([
            'status' => 'cancelled',
            'gateway_response' => json_encode($request->all()),
        ]);
        return redirect()->route('student.fees')->with('error', 'পেমেন্ট বাতিল হয়েছে।');
    }

    /**
     * IPN (Instant Payment Notification)
     */
    public function ipn(Request $request, SslCommerzService $service)
    {
        Log::info('SSLCommerz IPN', $request->all());

        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');

        $transaction = PaymentTransaction::where('transaction_id', $tranId)->first();
        if (!$transaction || $transaction->status === 'success') {
            return response('OK', 200);
        }

        $validation = $service->validatePayment($valId);
        $status = strtoupper($validation['status'] ?? '');

        if (in_array($status, ['VALID', 'VALIDATED'])) {
            // success মেথডে যেভাবে করেছি সেভাবে রেকর্ড আপডেট
            // (এখানে সরল রাখার জন্য শুধু status আপডেট)
            $transaction->update(['status' => 'success', 'gateway_response' => json_encode($validation)]);
        }

        return response('OK', 200);
    }
}
