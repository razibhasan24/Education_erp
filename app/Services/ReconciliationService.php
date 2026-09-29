<?php

namespace App\Services;

use App\Models\BankStatement;
use App\Models\FeePayment;
use Illuminate\Support\Facades\DB;

class ReconciliationService
{
    /**
     * একটি ব্যাংক স্টেটমেন্টের সাথে matching FeePayment খুঁজে বের করা
     */
    public function findMatchingPayments(BankStatement $statement): array
    {
        $amount = $statement->credit > 0 ? $statement->credit : $statement->debit;

        $query = FeePayment::where(function ($q) use ($statement, $amount) {
            // Same date (±3 দিন)
            $q->whereBetween('payment_date', [
                $statement->transaction_date->copy()->subDays(3),
                $statement->transaction_date->copy()->addDays(3),
            ])
            ->where('amount', $amount);
        });

        // Transaction ID থাকলে সেটা দিয়ে
        if ($statement->transaction_id) {
            $query->where('transaction_id', $statement->transaction_id);
        }

        return $query->with('student')->get()->toArray();
    }

    /**
     * একটি statement কে একটি payment এর সাথে match করা
     */
    public function match(BankStatement $statement, FeePayment $payment): bool
    {
        return DB::transaction(function () use ($statement, $payment) {
            $statement->update([
                'status' => 'matched',
                'matched_payment_id' => $payment->id,
            ]);

            $statement->bankAccount->recalculateBalance();

            return true;
        });
    }

    /**
     * Match বাতিল
     */
    public function unmatch(BankStatement $statement): bool
    {
        return DB::transaction(function () use ($statement) {
            $statement->update([
                'status' => 'unmatched',
                'matched_payment_id' => null,
                'matched_journal_entry_id' => null,
            ]);

            $statement->bankAccount->recalculateBalance();

            return true;
        });
    }

    /**
     * CSV থেকে bulk import
     * CSV Format: date, description, reference, transaction_id, debit, credit
     */
    public function importFromArray(int $bankAccountId, array $rows): array
    {
        $imported = 0;
        $skipped = 0;
        $balance = \App\Models\BankAccount::find($bankAccountId)->opening_balance;

        return DB::transaction(function () use ($bankAccountId, $rows, &$imported, &$skipped, &$balance) {
            foreach ($rows as $row) {
                // Skip blank
                if (empty($row['date']) || (empty($row['debit']) && empty($row['credit']))) {
                    $skipped++;
                    continue;
                }

                $debit = (float) ($row['debit'] ?? 0);
                $credit = (float) ($row['credit'] ?? 0);
                $balance += ($credit - $debit);

                BankStatement::create([
                    'bank_account_id' => $bankAccountId,
                    'transaction_date' => $row['date'],
                    'description' => $row['description'] ?? 'Imported',
                    'reference' => $row['reference'] ?? null,
                    'transaction_id' => $row['transaction_id'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'status' => 'unmatched',
                    'imported_by' => auth()->id(),
                ]);
                $imported++;
            }

            \App\Models\BankAccount::find($bankAccountId)->recalculateBalance();

            return ['imported' => $imported, 'skipped' => $skipped];
        });
    }
}