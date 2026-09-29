<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    protected $fillable = [
        'bank_account_id', 'transaction_date', 'description', 'reference',
        'transaction_id', 'debit', 'credit', 'balance', 'status',
        'matched_journal_entry_id', 'matched_payment_id', 'note', 'imported_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function matchedEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'matched_journal_entry_id');
    }

    public function matchedPayment()
    {
        return $this->belongsTo(FeePayment::class, 'matched_payment_id');
    }
}