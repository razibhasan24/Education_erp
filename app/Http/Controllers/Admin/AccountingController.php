<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function __construct(private AccountingService $service) {}

    /**
     * Chart of Accounts তালিকা
     */
    public function chartOfAccounts()
    {
        $accounts = Account::with('parent')->byCode()->get();
        $grouped = $accounts->groupBy('type');
        return view('admin.accounting.chart-of-accounts', compact('accounts', 'grouped'));
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code',
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'sub_type' => 'nullable|string',
            'parent_id' => 'nullable|exists:accounts,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_type' => 'nullable|in:debit,credit',
            'description' => 'nullable|string',
        ]);

        Account::create($data);
        return back()->with('success', 'অ্যাকাউন্ট যোগ হয়েছে।');
    }

    public function updateAccount(Request $request, Account $account)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $account->update($data);
        return back()->with('success', 'আপডেট হয়েছে।');
    }

    public function deleteAccount(Account $account)
    {
        if ($account->is_system) {
            return back()->with('error', 'সিস্টেম অ্যাকাউন্ট ডিলিট করা যাবে না।');
        }
        if ($account->items()->count() > 0) {
            return back()->with('error', 'এই অ্যাকাউন্টে লেনদেন আছে — ডিলিট করা যাবে না।');
        }
        $account->delete();
        return back()->with('success', 'অ্যাকাউন্ট ডিলিট হয়েছে।');
    }

    /**
     * Journal Entries
     */
    public function journalEntries(Request $request)
    {
        $query = JournalEntry::with(['items.account', 'creator']);
        if ($request->filled('from')) $query->where('entry_date', '>=', $request->from);
        if ($request->filled('to')) $query->where('entry_date', '<=', $request->to);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        $entries = $query->latest('entry_date')->paginate(50);
        return view('admin.accounting.journal-entries', compact('entries'));
    }

    public function createJournalEntry()
    {
        $accounts = Account::active()->byCode()->get();
        return view('admin.accounting.journal-entry-create', compact('accounts'));
    }

    public function storeJournalEntry(Request $request)
    {
        $data = $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'narration' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        try {
            JournalEntry::record([
                'entry_date' => $data['entry_date'],
                'reference' => $data['reference'] ?? null,
                'narration' => $data['narration'],
                'type' => 'manual',
            ], $data['lines']);

            return redirect()->route('admin.accounting.journal-entries')
                ->with('success', 'Journal Entry সফলভাবে তৈরি হয়েছে।');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function showJournalEntry(JournalEntry $entry)
    {
        $entry->load(['items.account', 'creator']);
        return view('admin.accounting.journal-entry-show', compact('entry'));
    }

    public function cancelJournalEntry(JournalEntry $entry)
    {
        if ($entry->status === 'cancelled') {
            return back()->with('error', 'ইতিমধ্যে বাতিল হয়েছে।');
        }
        $entry->update(['status' => 'cancelled']);
        return back()->with('success', 'Journal Entry বাতিল হয়েছে।');
    }

    /**
     * General Ledger
     */
    public function ledger(Request $request)
    {
        $accounts = Account::active()->byCode()->get();
        $accountId = $request->account_id;
        $ledger = null;
        $account = null;

        if ($accountId) {
            $account = Account::findOrFail($accountId);
            $ledger = $this->service->generalLedger(
                $account,
                $request->from,
                $request->to
            );
        }

        return view('admin.accounting.ledger', compact('accounts', 'account', 'ledger'));
    }

    /**
     * Trial Balance
     */
    public function trialBalance(Request $request)
    {
        $tb = $this->service->trialBalance($request->from, $request->to);
        return view('admin.accounting.trial-balance', compact('tb'));
    }

    /**
     * Profit & Loss
     */
    public function profitAndLoss(Request $request)
    {
        $pl = $this->service->profitAndLoss($request->from, $request->to);
        return view('admin.accounting.profit-loss', compact('pl'));
    }

    /**
     * Balance Sheet
     */
    public function balanceSheet(Request $request)
    {
        $bs = $this->service->balanceSheet($request->as_of);
        return view('admin.accounting.balance-sheet', compact('bs'));
    }

    /**
     * অটো-Journal তৈরি — পুরোনো fee payment, expense, salary থেকে
     */
    public function syncOldRecords()
    {
        try {
            DB::beginTransaction();
            $count = 0;

            // Fee Payments
            $payments = \App\Models\FeePayment::doesntHave('journalEntry')->get();
            foreach ($payments as $payment) {
                if ($payment->journalEntry) continue;
                $this->service->recordFeePayment($payment);
                $count++;
            }

            // Expenses
            $expenses = \App\Models\Expense::doesntHave('journalEntry')->get();
            foreach ($expenses as $expense) {
                if ($expense->journalEntry) continue;
                $this->service->recordExpense($expense);
                $count++;
            }

            DB::commit();
            return back()->with('success', "{$count}টি পুরোনো লেনদেন থেকে Journal Entry তৈরি হয়েছে।");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
