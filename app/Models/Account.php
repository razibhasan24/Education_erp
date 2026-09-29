<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code', 'name', 'name_bn', 'type', 'sub_type',
        'parent_id', 'is_system', 'is_active',
        'opening_balance', 'opening_type', 'description',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('student');
    }
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOfType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }

    public function scopeByCode(Builder $q): Builder
    {
        return $q->orderBy('code');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * একটি অ্যাকাউন্টের বর্তমান ব্যালেন্স (opening + movements)
     */
    public function getCurrentBalanceAttribute(): float
    {
        $debit = $this->items()->sum('debit');
        $credit = $this->items()->sum('credit');

        // Asset, Expense = Debit side বাড়ায়
        // Liability, Equity, Income = Credit side বাড়ায়
        $openingDebit = $this->opening_type === 'debit' ? $this->opening_balance : 0;
        $openingCredit = $this->opening_type === 'credit' ? $this->opening_balance : 0;

        $totalDebit = $openingDebit + $debit;
        $totalCredit = $openingCredit + $credit;

        if (in_array($this->type, ['asset', 'expense'])) {
            return (float) ($totalDebit - $totalCredit);
        }
        return (float) ($totalCredit - $totalDebit);
    }

    /**
     * Account type labels (বাংলা)
     */
    public static function typeLabels(): array
    {
        return [
            'asset'     => ['label' => 'সম্পদ',       'color' => 'primary', 'normal' => 'debit'],
            'liability' => ['label' => 'দায়',         'color' => 'danger',  'normal' => 'credit'],
            'equity'    => ['label' => 'মালিকানা',    'color' => 'info',    'normal' => 'credit'],
            'income'    => ['label' => 'আয়',          'color' => 'success', 'normal' => 'credit'],
            'expense'   => ['label' => 'ব্যয়',        'color' => 'warning', 'normal' => 'debit'],
        ];
    }

    /**
     * পরবর্তী account code জেনারেট
     */
    public static function generateCode(string $type): string
    {
        $prefixes = [
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'income' => '4',
            'expense' => '5',
        ];

        $prefix = $prefixes[$type] ?? '9';
        $last = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        $next = $last ? (int) $last->code + 10 : (int) $prefix . '000';
        return (string) $next;
    }
}