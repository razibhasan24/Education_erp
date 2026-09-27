<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    protected $fillable = [
        'voucher_no', 'entry_date', 'reference', 'narration',
        'total_debit', 'total_credit', 'type',
        'source_type', 'source_id', 'status', 'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function source()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePosted(Builder $q): Builder
    {
        return $q->where('status', 'posted');
    }

    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        return $q->whereBetween('entry_date', [$from, $to]);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    public static function generateVoucherNo(): string
    {
        $prefix = 'JE-' . date('Y') . '-';
        $last = self::where('voucher_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->voucher_no, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * একটি Journal Entry তৈরি (accounts array সহ)
     * $lines = [
     *   ['account_id' => 1, 'debit' => 5000, 'credit' => 0, 'description' => '...'],
     *   ['account_id' => 2, 'debit' => 0, 'credit' => 5000],
     * ]
     */
    public static function record(array $data, array $lines): self
    {
        return DB::transaction(function () use ($data, $lines) {
            $totalDebit = collect($lines)->sum('debit');
            $totalCredit = collect($lines)->sum('credit');

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new \Exception("Debit ({$totalDebit}) এবং Credit ({$totalCredit}) সমান নয়।");
            }

            $entry = self::create([
                'voucher_no' => $data['voucher_no'] ?? self::generateVoucherNo(),
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'reference' => $data['reference'] ?? null,
                'narration' => $data['narration'] ?? 'Journal Entry',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'type' => $data['type'] ?? 'manual',
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'status' => $data['status'] ?? 'posted',
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            foreach ($lines as $line) {
                $entry->items()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry;
        });
    }
}
