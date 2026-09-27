<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    protected $fillable = [
        'voucher_no', 'teacher_id', 'month', 'year', 'basic_salary',
        'allowance', 'deduction', 'net_salary', 'payment_date',
        'payment_method', 'remarks', 'paid_by',
    ];

    protected $casts = ['payment_date' => 'date'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public static function generateVoucherNo(): string
    {
        $prefix = 'SAL-' . date('Ym') . '-';
        $last = self::where('voucher_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->voucher_no, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
