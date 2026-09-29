<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BankAccount;
use App\Models\BankStatement;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function __construct(private ReconciliationService $service) {}

    public function index()
    {
        $bankAccounts = BankAccount::with('account')->latest()->get();
        return view('admin.accounting.bank.index', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'account_no' => 'required|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'branch_name' => 'nullable|string|max:100',
            'type' => 'required|in:bank,bkash,nagad,rocket,upay,other',
            'account_id' => 'nullable|exists:accounts,id',
            'opening_balance' => 'required|numeric|min:0',
            'account_holder' => 'nullable|string|max:150',
            'routing_no' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50',
        ]);
        $data['current_balance'] = $data['opening_balance'];

        BankAccount::create($data);
        return back()->with('success', 'ব্যাংক অ্যাকাউন্ট যোগ হয়েছে।');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    public function show(BankAccount $bankAccount, Request $request)
    {
        $query = $bankAccount->statements()->with('matchedPayment.student');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('from')) $query->where('transaction_date', '>=', $request->from);
        if ($request->filled('to')) $query->where('transaction_date', '<=', $request->to);

        $statements = $query->latest('transaction_date')->paginate(50);

        $summary = [
            'total_credit' => $bankAccount->statements()->sum('credit'),
            'total_debit' => $bankAccount->statements()->sum('debit'),
            'unmatched' => $bankAccount->statements()->where('status', 'unmatched')->count(),
            'matched' => $bankAccount->statements()->where('status', 'matched')->count(),
        ];

        return view('admin.accounting.bank.show', compact('bankAccount', 'statements', 'summary'));
    }

    /**
     * CSV ফাইল থেকে import
     */
    public function import(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');

        // প্রথম লাইন skip (header)
        $header = fgetcsv($handle);

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            // Expected: Date, Description, Reference, TransactionID, Debit, Credit
            $rows[] = [
                'date' => $data[0] ?? null,
                'description' => $data[1] ?? '',
                'reference' => $data[2] ?? null,
                'transaction_id' => $data[3] ?? null,
                'debit' => $data[4] ?? 0,
                'credit' => $data[5] ?? 0,
            ];
        }
        fclose($handle);

        $result = $this->service->importFromArray($bankAccount->id, $rows);

        return back()->with('success', "Import সম্পন্ন — {$result['imported']}টি রেকর্ড, স্কিপ: {$result['skipped']}টি।");
    }

    /**
     * ম্যানুয়ালি একটি statement যোগ
     */
    public function storeStatement(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:200',
            'reference' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:100',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
        ]);

        $data['bank_account_id'] = $bankAccount->id;
        $data['debit'] = $data['debit'] ?? 0;
        $data['credit'] = $data['credit'] ?? 0;
        $data['status'] = 'unmatched';
        $data['imported_by'] = auth()->id();

        BankStatement::create($data);
        $bankAccount->recalculateBalance();

        return back()->with('success', 'Statement যোগ হয়েছে।');
    }

    /**
     * Match suggestion দেখা
     */
    public function matchSuggest(BankStatement $statement)
    {
        $suggestions = $this->service->findMatchingPayments($statement);
        return response()->json($suggestions);
    }

    /**
     * Match করা
     */
    public function match(Request $request, BankStatement $statement)
    {
        $data = $request->validate([
            'payment_id' => 'required|exists:fee_payments,id',
        ]);

        $this->service->match($statement, \App\Models\FeePayment::find($data['payment_id']));

        return back()->with('success', 'সফলভাবে matched।');
    }

    public function unmatch(BankStatement $statement)
    {
        $this->service->unmatch($statement);
        return back()->with('success', 'Match বাতিল হয়েছে।');
    }

    public function ignore(BankStatement $statement)
    {
        $statement->update(['status' => 'ignored']);
        $statement->bankAccount->recalculateBalance();
        return back()->with('success', 'Ignore করা হয়েছে।');
    }

    public function destroyStatement(BankStatement $statement)
    {
        $account = $statement->bankAccount;
        $statement->delete();
        $account->recalculateBalance();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    /**
     * অটো-match — যেসব statement এর সাথে হুবহু amount+date+trxid মেলে
     */
    public function autoMatch(BankAccount $bankAccount)
    {
        $unmatched = $bankAccount->statements()->where('status', 'unmatched')->get();
        $matched = 0;

        DB::beginTransaction();
        try {
            foreach ($unmatched as $statement) {
                $suggestions = $this->service->findMatchingPayments($statement);
                if (count($suggestions) === 1) {
                    $this->service->match($statement, \App\Models\FeePayment::find($suggestions[0]['id']));
                    $matched++;
                }
            }
            DB::commit();
            return back()->with('success', "অটো-match: {$matched}টি।");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}