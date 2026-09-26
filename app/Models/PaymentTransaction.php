<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_id', 'gateway_transaction_id', 'fee_invoice_id',
        'student_id', 'amount', 'gateway', 'payment_method', 'status',
        'gateway_response', 'ip_address', 'fee_payment_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feePayment()
    {
        return $this->belongsTo(FeePayment::class);
    }

    public static function generateTransactionId(): string
    {
        return 'TXN-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
