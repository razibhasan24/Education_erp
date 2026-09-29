<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'value', 'applicable_to', 'fee_category_id',
        'criteria_type', 'min_gpa', 'max_income', 'max_recipients',
        'start_date', 'end_date', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function applications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    public function getApplicableRecipientsCountAttribute(): int
    {
        return $this->applications()->where('status', 'approved')->count();
    }

    /**
     * একটি নির্দিষ্ট amount এর উপর discount হিসাব
     */
    public function calculateDiscount(float $baseAmount): float
    {
        if ($this->type === 'percentage') {
            return round($baseAmount * ($this->value / 100), 2);
        }
        return min($this->value, $baseAmount);
    }

    public static function criteriaLabels(): array
    {
        return [
            'merit' => 'মেধা',
            'need_based' => 'আর্থিক ভিত্তিক',
            'sibling' => 'সহোদর',
            'staff_ward' => 'কর্মচারী সন্তান',
            'freedom_fighter' => 'মুক্তিযোদ্ধা',
            'special' => 'বিশেষ',
        ];
    }
}