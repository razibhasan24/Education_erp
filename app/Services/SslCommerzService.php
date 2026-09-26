<?php

namespace App\Services;

use App\Models\FeeInvoice;
use App\Models\PaymentTransaction;
use App\Models\Student;
use Illuminate\Support\Facades\Http;

class SslCommerzService
{
    private string $storeId;
    private string $storePassword;
    private bool $sandbox;

    public function __construct()
    {
        $this->storeId = config('services.sslcommerz.store_id', '');
        $this->storePassword = config('services.sslcommerz.store_password', '');
        $this->sandbox = config('services.sslcommerz.sandbox', true);
    }

    private function baseUrl(): string
    {
        return $this->sandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * পেমেন্ট সেশন তৈরি করে গেটওয়ের URL রিটার্ন করে
     */
    public function initiatePayment(FeeInvoice $invoice, Student $student): array
    {
        $transactionId = PaymentTransaction::generateTransactionId();

        $transaction = PaymentTransaction::create([
            'transaction_id' => $transactionId,
            'fee_invoice_id' => $invoice->id,
            'student_id' => $student->id,
            'amount' => $invoice->due_amount,
            'gateway' => 'sslcommerz',
            'status' => 'initiated',
            'ip_address' => request()->ip(),
        ]);

        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $invoice->due_amount,
            'currency' => 'BDT',
            'tran_id' => $transactionId,

            // সফল হলে redirect
            'success_url' => route('payment.success'),
            'fail_url' => route('payment.fail'),
            'cancel_url' => route('payment.cancel'),
            'ipn_url' => route('payment.ipn'),

            // কাস্টমার তথ্য
            'cus_name' => $student->name,
            'cus_email' => $student->user->email ?? 'student@example.com',
            'cus_add1' => $student->present_address ?? 'N/A',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $student->father_phone ?? '01700000000',

            // শিপিং তথ্য
            'shipping_method' => 'NO',
            'num_of_item' => 1,
            'product_name' => 'School Fee - ' . $invoice->invoice_no,
            'product_category' => 'Education',
            'product_profile' => 'non-physical-goods',
        ];

        $response = Http::asForm()->post($this->baseUrl() . '/gwprocess/v4/api.php', $postData);

        $data = $response->json();

        if (!empty($data['GatewayPageURL'])) {
            $transaction->update(['gateway_response' => json_encode($data)]);
            return [
                'success' => true,
                'url' => $data['GatewayPageURL'],
                'transaction_id' => $transactionId,
            ];
        }

        $transaction->update([
            'status' => 'failed',
            'gateway_response' => json_encode($data),
        ]);

        return [
            'success' => false,
            'message' => $data['failedreason'] ?? 'Payment gateway error',
        ];
    }

    /**
     * পেমেন্ট ভ্যালিডেশন (IPN / Success redirect এ কল হবে)
     */
    public function validatePayment(string $valId): array
    {
        $response = Http::get($this->baseUrl() . '/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ]);

        return $response->json() ?? [];
    }
}