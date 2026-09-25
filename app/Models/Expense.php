<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title', 'category', 'amount', 'expense_date', 'payment_method',
        'reference', 'note', 'created_by',
    ];

    protected $casts = ['expense_date' => 'date'];
}
