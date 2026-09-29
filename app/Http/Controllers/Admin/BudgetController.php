<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AcademicYear;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $budgets = Budget::with(['account', 'academicYear'])
            ->where('year', $year)
            ->orderBy('type')
            ->orderBy('id')
            ->get();

        $this->syncActuals($budgets);

        $summary = [
            'income_budget' => $budgets->where('type', 'income')->sum('budgeted_amount'),
            'income_actual' => $budgets->where('type', 'income')->sum('actual_amount'),
            'expense_budget' => $budgets->where('type', 'expense')->sum('budgeted_amount'),
            'expense_actual' => $budgets->where('type', 'expense')->sum('actual_amount'),
        ];

        $accounts = Account::active()->whereIn('type', ['income', 'expense'])->byCode()->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.accounting.budgets.index', compact('budgets', 'summary', 'accounts', 'academicYears', 'year'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'year' => 'required|integer',
            'type' => 'required|in:income,expense',
            'account_id' => 'nullable|exists:accounts,id',
            'budgeted_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Budget::create($data);
        return back()->with('success', 'বাজেট যোগ হয়েছে।');
    }

    public function update(Request $request, Budget $budget)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'budgeted_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $budget->update($data);
        return back()->with('success', 'আপডেট হয়েছে।');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return back()->with('success', 'মুছে ফেলা হয়েছে।');
    }

    /**
     * Actual amount update — Journal Entry থেকে হিসাব
     */
    private function syncActuals($budgets): void
    {
        foreach ($budgets as $budget) {
            if (!$budget->account_id) continue;

            $yearStart = now()->setYear($budget->year)->startOfYear();
            $yearEnd = now()->setYear($budget->year)->endOfYear();

            $debit = $budget->account->items()
                ->whereHas('entry', fn($q) => $q->posted()->between($yearStart, $yearEnd))
                ->sum('debit');
            $credit = $budget->account->items()
                ->whereHas('entry', fn($q) => $q->posted()->between($yearStart, $yearEnd))
                ->sum('credit');

            $actual = $budget->type === 'expense' ? ($debit - $credit) : ($credit - $debit);

            if (abs($actual - $budget->actual_amount) > 0.01) {
                $budget->update(['actual_amount' => $actual]);
            }
        }
    }
}