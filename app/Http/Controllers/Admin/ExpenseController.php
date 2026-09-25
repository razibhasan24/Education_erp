<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('from')) $query->where('expense_date', '>=', $request->from);
        if ($request->filled('to')) $query->where('expense_date', '<=', $request->to);
        if ($request->filled('category')) $query->where('category', $request->category);

        $expenses = $query->latest()->get();
        $total = $expenses->sum('amount');

        $categories = Expense::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.fees.expenses.index', compact('expenses', 'total', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        Expense::create($data);
        return back()->with('success', 'খরচ যোগ হয়েছে।');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'খরচ মুছে ফেলা হয়েছে।');
    }
}
