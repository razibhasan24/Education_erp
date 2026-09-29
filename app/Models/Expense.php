<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Expense extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title', 'category', 'amount', 'expense_date', 'payment_method',
        'reference', 'note', 'created_by',
    ];

    protected $casts = ['expense_date' => 'date'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('student');
    }
    public function journalEntry()
    {
        return $this->morphOne(\App\Models\JournalEntry::class, 'source');
    }
}