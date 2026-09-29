<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\JournalEntry;
use App\Models\SalaryPayment;
use App\Models\FeeInvoice;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * ফি পেমেন্টের Journal Entry তৈরি
     */
    public function recordFeePayment(FeePayment $payment): JournalEntry
    {
        $cashAccount = $this->getCashAccountByMethod($payment->payment_method);
        $incomeAccount = $this->getIncomeAccountFromInvoice($payment->invoice);

        return JournalEntry::record([
            'entry_date' => $payment->payment_date,
            'reference' => $payment->receipt_no,
            'narration' => "Fee Payment Received - {$payment->receipt_no} ({$payment->student->name})",
            'type' => 'fee_payment',
            'source_type' => FeePayment::class,
            'source_id' => $payment->id,
        ], [
            [
                'account_id' => $cashAccount->id,
                'debit' => $payment->amount,
                'credit' => 0,
                'description' => "Received via {$payment->payment_method}",
            ],
            [
                'account_id' => $incomeAccount->id,
                'debit' => 0,
                'credit' => $payment->amount,
                'description' => 'Fee Income',
            ],
        ]);
    }

    /**
     * Invoice তৈরি হলে Accounts Receivable এ রেকর্ড
     */
    public function recordFeeInvoice(FeeInvoice $invoice): JournalEntry
    {
        $receivable = Account::where('code', '1100')->firstOrFail(); // Accounts Receivable
        $income = Account::where('code', '4000')->firstOrFail();     // Tuition Fee Income

        return JournalEntry::record([
            'entry_date' => $invoice->invoice_date,
            'reference' => $invoice->invoice_no,
            'narration' => "Fee Invoice Generated - {$invoice->invoice_no}",
            'type' => 'other',
            'source_type' => FeeInvoice::class,
            'source_id' => $invoice->id,
        ], [
            [
                'account_id' => $receivable->id,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'description' => 'Fee Receivable',
            ],
            [
                'account_id' => $income->id,
                'debit' => 0,
                'credit' => $invoice->total_amount,
                'description' => 'Fee Income',
            ],
        ]);
    }

    /**
     * খরচ রেকর্ড
     */
    public function recordExpense(Expense $expense): JournalEntry
    {
        $cash = $this->getCashAccountByMethod($expense->payment_method ?? 'cash');
        $expenseAccount = $this->getExpenseAccountFromCategory($expense->category);

        return JournalEntry::record([
            'entry_date' => $expense->expense_date,
            'reference' => $expense->reference,
            'narration' => "Expense: {$expense->title}",
            'type' => 'expense',
            'source_type' => Expense::class,
            'source_id' => $expense->id,
        ], [
            [
                'account_id' => $expenseAccount->id,
                'debit' => $expense->amount,
                'credit' => 0,
                'description' => $expense->note ?? $expense->title,
            ],
            [
                'account_id' => $cash->id,
                'debit' => 0,
                'credit' => $expense->amount,
                'description' => 'Payment',
            ],
        ]);
    }

    /**
     * বেতন পরিশোধ
     */
    public function recordSalaryPayment(SalaryPayment $payment): JournalEntry
    {
        $cash = $this->getCashAccountByMethod($payment->payment_method ?? 'cash');
        $salaryExpense = Account::where('code', '5000')->firstOrFail(); // Salary Expense

        $lines = [
            [
                'account_id' => $salaryExpense->id,
                'debit' => $payment->net_salary + $payment->deduction,
                'credit' => 0,
                'description' => "Salary for {$payment->month}/{$payment->year}",
            ],
            [
                'account_id' => $cash->id,
                'debit' => 0,
                'credit' => $payment->net_salary,
                'description' => 'Salary Payment',
            ],
        ];

        // Deduction থাকলে Tax Payable এ যাবে
        if ($payment->deduction > 0) {
            $taxPayable = Account::where('code', '2020')->firstOrFail(); // Tax Payable
            $lines[] = [
                'account_id' => $taxPayable->id,
                'debit' => 0,
                'credit' => $payment->deduction,
                'description' => 'Tax / Deduction',
            ];
        }

        return JournalEntry::record([
            'entry_date' => $payment->payment_date,
            'reference' => $payment->voucher_no,
            'narration' => "Salary Paid to {$payment->teacher->name}",
            'type' => 'salary',
            'source_type' => SalaryPayment::class,
            'source_id' => $payment->id,
        ], $lines);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    private function getCashAccountByMethod(string $method): Account
    {
        $map = [
            'cash'   => '1000',  // Cash in Hand
            'bkash'  => '1020',  // bKash
            'nagad'  => '1030',  // Nagad
            'bank'   => '1010',  // Bank
            'card'   => '1010',
            'cheque' => '1010',
            'rocket' => '1020',  // বিকাশ/রকেট একই অ্যাকাউন্ট ধরে রাখছি, চাইলে আলাদা করুন
        ];

        $code = $map[$method] ?? '1000';
        return Account::where('code', $code)->firstOrFail();
    }

    private function getIncomeAccountFromInvoice(FeeInvoice $invoice): Account
    {
        // Invoice items থেকে Fee Category দেখে Income Account বের করা
        $category = $invoice->items->first()?->category;
        if (!$category) {
            return Account::where('code', '4000')->firstOrFail();
        }

        $map = [
            'Tuition Fee'    => '4000',
            'Admission Fee'  => '4010',
            'Exam Fee'       => '4020',
            'Transport Fee'  => '4030',
            'Hostel Fee'     => '4040',
            'Library Fee'    => '4050',
        ];

        $code = $map[$category->name] ?? '4900'; // Other Income
        return Account::where('code', $code)->firstOrFail();
    }

    private function getExpenseAccountFromCategory(?string $category): Account
    {
        if (!$category) return Account::where('code', '5900')->firstOrFail();

        $map = [
            'Salary'       => '5000',
            'Rent'         => '5010',
            'Utility'      => '5020',
            'Electricity'  => '5030',
            'Water'        => '5040',
            'Internet'     => '5050',
            'Stationery'   => '5060',
            'Maintenance'  => '5070',
            'Transport'    => '5080',
            'Marketing'    => '5090',
        ];

        foreach ($map as $key => $code) {
            if (stripos($category, $key) !== false) {
                return Account::where('code', $code)->firstOrFail();
            }
        }
        return Account::where('code', '5900')->firstOrFail(); // Miscellaneous
    }

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    /**
     * Trial Balance
     */
    public function trialBalance(?string $from = null, ?string $to = null): array
    {
        $from = $from ?? now()->startOfYear()->toDateString();
        $to = $to ?? now()->toDateString();

        $accounts = Account::active()->byCode()->get();

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $query = $account->items()
                ->whereHas('entry', fn($q) => $q->posted()->between($from, $to));

            $debit = (clone $query)->sum('debit');
            $credit = (clone $query)->sum('credit');

            // Opening balance
            if ($account->opening_type === 'debit') $debit += $account->opening_balance;
            else $credit += $account->opening_balance;

            $balance = $debit - $credit;

            if (abs($balance) < 0.01 && $debit == 0 && $credit == 0) continue;

            $debitSide = $balance > 0 ? $balance : 0;
            $creditSide = $balance < 0 ? abs($balance) : 0;

            $rows[] = [
                'code' => $account->code,
                'name' => $account->name,
                'name_bn' => $account->name_bn,
                'type' => $account->type,
                'debit' => $debitSide,
                'credit' => $creditSide,
            ];

            $totalDebit += $debitSide;
            $totalCredit += $creditSide;
        }

        return [
            'rows' => $rows,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'from' => $from,
            'to' => $to,
            'balanced' => round($totalDebit, 2) === round($totalCredit, 2),
        ];
    }

    /**
     * General Ledger
     */
    public function generalLedger(Account $account, ?string $from = null, ?string $to = null): array
    {
        $from = $from ?? now()->startOfMonth()->toDateString();
        $to = $to ?? now()->toDateString();

        $openingQuery = $account->items()
            ->whereHas('entry', fn($q) => $q->posted()->where('entry_date', '<', $from));

        $openingDebit = (clone $openingQuery)->sum('debit');
        $openingCredit = (clone $openingQuery)->sum('credit');

        if ($account->opening_type === 'debit') $openingDebit += $account->opening_balance;
        else $openingCredit += $account->opening_balance;

        $opening = $openingDebit - $openingCredit;

        $items = $account->items()
            ->with('entry')
            ->whereHas('entry', fn($q) => $q->posted()->between($from, $to))
            ->get()
            ->sortBy(fn($i) => $i->entry->entry_date);

        $running = $opening;
        $rows = [];
        foreach ($items as $item) {
            $running += ($item->debit - $item->credit);
            $rows[] = [
                'date' => $item->entry->entry_date,
                'voucher_no' => $item->entry->voucher_no,
                'narration' => $item->entry->narration,
                'debit' => $item->debit,
                'credit' => $item->credit,
                'balance' => $running,
            ];
        }

        return [
            'account' => $account,
            'opening' => $opening,
            'rows' => $rows,
            'closing' => $running,
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Profit & Loss Statement
     */
    public function profitAndLoss(?string $from = null, ?string $to = null): array
    {
        $from = $from ?? now()->startOfYear()->toDateString();
        $to = $to ?? now()->toDateString();

        $incomes = Account::active()->ofType('income')->byCode()->get()->map(function ($acc) use ($from, $to) {
            $debit = $acc->items()->whereHas('entry', fn($q) => $q->posted()->between($from, $to))->sum('debit');
            $credit = $acc->items()->whereHas('entry', fn($q) => $q->posted()->between($from, $to))->sum('credit');
            return ['account' => $acc, 'amount' => $credit - $debit];
        })->filter(fn($i) => abs($i['amount']) > 0);

        $expenses = Account::active()->ofType('expense')->byCode()->get()->map(function ($acc) use ($from, $to) {
            $debit = $acc->items()->whereHas('entry', fn($q) => $q->posted()->between($from, $to))->sum('debit');
            $credit = $acc->items()->whereHas('entry', fn($q) => $q->posted()->between($from, $to))->sum('credit');
            return ['account' => $acc, 'amount' => $debit - $credit];
        })->filter(fn($i) => abs($i['amount']) > 0);

        $totalIncome = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');

        return [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $totalIncome - $totalExpense,
            'from' => $from,
            'to' => $to,
        ];
    }

       /**
     * Balance Sheet
     */
    public function balanceSheet(?string $asOf = null): array
    {
        $asOf = $asOf ?? now()->toDateString();

        // Helper: একটি নির্দিষ্ট account type এর সব account এর balance
        $getBalance = function (string $type) use ($asOf) {
            return Account::active()->ofType($type)->byCode()->get()->map(function ($acc) use ($asOf, $type) {
                $debit = $acc->items()
                    ->whereHas('entry', fn($q) => $q->posted()->where('entry_date', '<=', $asOf))
                    ->sum('debit');
                $credit = $acc->items()
                    ->whereHas('entry', fn($q) => $q->posted()->where('entry_date', '<=', $asOf))
                    ->sum('credit');

                $openingDebit = $acc->opening_type === 'debit' ? $acc->opening_balance : 0;
                $openingCredit = $acc->opening_type === 'credit' ? $acc->opening_balance : 0;

                if (in_array($type, ['asset', 'expense'])) {
                    $balance = ($debit + $openingDebit) - ($credit + $openingCredit);
                } else {
                    $balance = ($credit + $openingCredit) - ($debit + $openingDebit);
                }

                return ['account' => $acc, 'amount' => (float) $balance];
            })->filter(fn($i) => abs($i['amount']) > 0.01)->values();
        };

        $assets = $getBalance('asset');
        $liabilities = $getBalance('liability');
        $equity = $getBalance('equity');

        // Retained Earnings = Net profit till date
        $pl = $this->profitAndLoss(null, $asOf);
        $netProfit = $pl['net_profit'];

        $totalAssets = $assets->sum('amount');
        $totalLiabilities = $liabilities->sum('amount');
        $totalEquity = $equity->sum('amount') + $netProfit;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'net_profit' => $netProfit,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'balanced' => round($totalAssets, 2) === round($totalLiabilities + $totalEquity, 2),
            'as_of' => $asOf,
        ];
    }
}