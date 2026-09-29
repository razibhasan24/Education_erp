<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    protected $fillable = [
        'fee_invoice_id', 'installment_no', 'amount', 'due_date',
        'paid_amount', 'late_fee', 'status', 'paid_date', 'remarks',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->amount + $this->late_fee - $this->paid_amount);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }
}