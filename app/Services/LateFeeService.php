<?php

namespace App\Services;

use App\Models\FeeInstallment;
use App\Models\FeeInvoice;
use Carbon\Carbon;

class LateFeeService
{
    /**
     * একটি ইনভয়েসের জন্য late fee হিসাব (due_date পার হলে)
     */
    public function calculateForInvoice(FeeInvoice $invoice): float
    {
        if (!config('usms.late_fee.enabled')) return 0;
        if (!$invoice->due_date || $invoice->due_amount <= 0) return 0;

        $graceDays = (int) config('usms.late_fee.grace_days');
        $graceDate = $invoice->due_date->copy()->addDays($graceDays);

        if (now()->lessThanOrEqualTo($graceDate)) return 0;

        $daysLate = $graceDate->diffInDays(now());
        $type = config('usms.late_fee.type');
        $value = (float) config('usms.late_fee.value');
        $max = (float) config('usms.late_fee.max_amount');

        // Monthly basis calculation (প্রতি মাসে late fee)
        $monthsLate = max(1, ceil($daysLate / 30));

        if ($type === 'percentage') {
            $lateFee = $invoice->due_amount * ($value / 100) * $monthsLate;
        } else {
            $lateFee = $value * $monthsLate;
        }

        return min(round($lateFee, 2), $max);
    }

    /**
     * একটি ইনভয়েসে late fee apply
     */
    public function applyToInvoice(FeeInvoice $invoice): float
    {
        $lateFee = $this->calculateForInvoice($invoice);

        if ($lateFee > 0) {
            $invoice->update([
                'fine' => $lateFee,
                'total_amount' => $invoice->subtotal - $invoice->discount + $lateFee,
                'due_amount' => $invoice->subtotal - $invoice->discount + $lateFee - $invoice->paid_amount,
            ]);
        }

        return $lateFee;
    }

    /**
     * একটি installment এ late fee হিসাব
     */
    public function calculateForInstallment(FeeInstallment $installment): float
    {
        if (!config('usms.late_fee.enabled')) return 0;
        if ($installment->status === 'paid') return 0;
        if (now()->lessThanOrEqualTo($installment->due_date)) return 0;

        $graceDays = (int) config('usms.late_fee.grace_days');
        $graceDate = $installment->due_date->copy()->addDays($graceDays);

        if (now()->lessThanOrEqualTo($graceDate)) return 0;

        $daysLate = $graceDate->diffInDays(now());
        $monthsLate = max(1, ceil($daysLate / 30));

        $type = config('usms.late_fee.type');
        $value = (float) config('usms.late_fee.value');
        $max = (float) config('usms.late_fee.max_amount');

        $base = $installment->amount - $installment->paid_amount;

        if ($type === 'percentage') {
            $lateFee = $base * ($value / 100) * $monthsLate;
        } else {
            $lateFee = $value * $monthsLate;
        }

        return min(round($lateFee, 2), $max);
    }

    /**
     * সব overdue installment এ late fee update
     */
    public function updateAllInstallments(): int
    {
        $updated = 0;
        $installments = FeeInstallment::whereIn('status', ['pending', 'partial', 'overdue'])
            ->where('due_date', '<', now())
            ->get();

        foreach ($installments as $inst) {
            $lateFee = $this->calculateForInstallment($inst);
            if ($lateFee > 0 && $lateFee != $inst->late_fee) {
                $inst->update([
                    'late_fee' => $lateFee,
                    'status' => 'overdue',
                ]);
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * সব overdue invoice এ late fee apply
     */
    public function updateAllInvoices(): int
    {
        $updated = 0;
        $invoices = FeeInvoice::whereIn('status', ['unpaid', 'partial'])
            ->where('due_amount', '>', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->subDays(config('usms.late_fee.grace_days')))
            ->get();

        foreach ($invoices as $inv) {
            $lateFee = $this->calculateForInvoice($inv);
            if ($lateFee > 0 && $lateFee != $inv->fine) {
                $this->applyToInvoice($inv);
                $updated++;
            }
        }

        return $updated;
    }
}