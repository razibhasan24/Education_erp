<?php

return [
    'late_fee' => [
        'enabled' => env('LATE_FEE_ENABLED', true),
        'type' => env('LATE_FEE_TYPE', 'percentage'),  // percentage / fixed
        'value' => env('LATE_FEE_VALUE', 2),            // 2% or 100 Taka
        'grace_days' => env('LATE_FEE_GRACE_DAYS', 7),  // Grace period
        'max_amount' => env('LATE_FEE_MAX', 5000),      // Max late fee
    ],
];