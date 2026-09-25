<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = ['name', 'name_bn', 'fee_type', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function structures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public static function typeLabels(): array
    {
        return [
            'monthly'  => 'মাসিক',
            'one_time' => 'এককালীন',
            'yearly'   => 'বাৎসরিক',
            'session'  => 'সেশন চার্জ',
        ];
    }
}
