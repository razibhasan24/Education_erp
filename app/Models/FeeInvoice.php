<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    protected $fillable = [
        'invoice_no', 'student_id', 'class_id', 'section_id', 'academic_year_id',
        'invoice_date', 'due_date', 'month', 'year', 'subtotal', 'discount',
        'fine', 'total_amount', 'paid_amount', 'due_amount', 'status', 'remarks', 'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function items()
    {
        return $this->hasMany(FeeInvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public static function generateInvoiceNo(): string
    {
        $prefix = 'INV-' . date('Ym') . '-';
        $last = self::where('invoice_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->invoice_no, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public static function statusLabels(): array
    {
        return [
            'unpaid'    => ['label' => 'অপরিশোধিত', 'color' => 'danger'],
            'partial'   => ['label' => 'আংশিক', 'color' => 'warning'],
            'paid'      => ['label' => 'পরিশোধিত', 'color' => 'success'],
            'cancelled' => ['label' => 'বাতিল', 'color' => 'secondary'],
        ];
    }
}
