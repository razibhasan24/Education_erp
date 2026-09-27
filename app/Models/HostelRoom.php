<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    protected $fillable = ['hostel_id', 'room_no', 'capacity', 'occupied', 'monthly_fee', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class);
    }
}
