<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'name', 'account_no', 'bank_name', 'branch_name', 'type',
        'account_id', 'opening_balance', 'current_balance', 'account_holder',
        'routing_no', 'swift_code', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function statements()
    {
        return $this->hasMany(BankStatement::class);
    }

    public static function typeLabels(): array
    {
        return [
            'bank'   => 'ব্যাংক',
            'bkash'  => 'বিকাশ',
            'nagad'  => 'নগদ',
            'rocket' => 'রকেট',
            'upay'   => 'উপায়',
            'other'  => 'অন্যান্য',
        ];
    }

    /**
     * স্টেটমেন্ট থেকে বর্তমান ব্যালেন্স আপডেট
     */
    public function recalculateBalance(): void
    {
        $credits = $this->statements()->where('status', '!=', 'ignored')->sum('credit');
        $debits = $this->statements()->where('status', '!=', 'ignored')->sum('debit');

        $this->update([
            'current_balance' => $this->opening_balance + $credits - $debits,
        ]);
    }
}