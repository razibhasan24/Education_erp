<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'receipt_no', 'fee_invoice_id', 'student_id', 'payment_date',
        'amount', 'discount', 'fine', 'payment_method', 'transaction_id',
        'remarks', 'received_by',
    ];

    protected $casts = ['payment_date' => 'date'];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public static function generateReceiptNo(): string
    {
        $prefix = 'RCP-' . date('Ymd') . '-';
        $last = self::where('receipt_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->receipt_no, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
