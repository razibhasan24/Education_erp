<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = ['name', 'type', 'warden_name', 'warden_phone', 'address', 'monthly_fee', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }
}
