<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\SalaryPayment;
use App\Services\AccountingService;
use Illuminate\Console\Command;

class SyncAccounting extends Command
{
    protected $signature = 'accounting:sync';
    protected $description = 'পুরোনো Fee Payment, Expense, Salary থেকে Journal Entry তৈরি';

    public function handle(AccountingService $service): int
    {
        $count = 0;

        $payments = FeePayment::doesntHave('journalEntry')->get();
        foreach ($payments as $p) {
            try { $service->recordFeePayment($p); $count++; }
            catch (\Exception $e) { $this->warn("FeePayment #{$p->id}: {$e->getMessage()}"); }
        }
        $this->info("Fee Payment: {$payments->count()}টি processed");

        $expenses = Expense::doesntHave('journalEntry')->get();
        foreach ($expenses as $e) {
            try { $service->recordExpense($e); $count++; }
            catch (\Exception $ex) { $this->warn("Expense #{$e->id}: {$ex->getMessage()}"); }
        }
        $this->info("Expense: {$expenses->count()}টি processed");

        $salaries = SalaryPayment::doesntHave('journalEntry')->get();
        foreach ($salaries as $s) {
            try { $service->recordSalaryPayment($s); $count++; }
            catch (\Exception $ex) { $this->warn("Salary #{$s->id}: {$ex->getMessage()}"); }
        }
        $this->info("Salary: {$salaries->count()}টি processed");

        $this->info("✅ মোট {$count}টি Journal Entry তৈরি হয়েছে।");
        return self::SUCCESS;
    }
}
