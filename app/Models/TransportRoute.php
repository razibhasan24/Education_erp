<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    protected $fillable = ['name', 'start_point', 'end_point', 'monthly_fee', 'vehicle_id', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stops()
    {
        return $this->hasMany(TransportStop::class)->orderBy('sequence');
    }

    public function studentTransports()
    {
        return $this->hasMany(StudentTransport::class);
    }
}
