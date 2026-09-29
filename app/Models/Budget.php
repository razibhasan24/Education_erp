<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Budget extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name', 'academic_year_id', 'year', 'type',
        'account_id', 'budgeted_amount', 'actual_amount',
        'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('student');
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getVarianceAttribute(): float
    {
        return (float) ($this->budgeted_amount - $this->actual_amount);
    }

    public function getUtilizationPercentAttribute(): float
    {
        if ($this->budgeted_amount <= 0) return 0;
        return round(($this->actual_amount / $this->budgeted_amount) * 100, 2);
    }
}