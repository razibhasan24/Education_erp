<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['name', 'registration_no', 'capacity', 'driver_name', 'driver_phone', 'helper_name', 'helper_phone', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function routes()
    {
        return $this->hasMany(TransportRoute::class);
    }
}
