<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportStop extends Model
{
    protected $fillable = ['transport_route_id', 'name', 'pickup_time', 'drop_time', 'fee', 'sequence'];

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }
}
