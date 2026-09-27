<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransport extends Model
{
    protected $fillable = ['student_id', 'transport_route_id', 'transport_stop_id', 'start_date', 'end_date', 'status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }

    public function stop()
    {
        return $this->belongsTo(TransportStop::class, 'transport_stop_id');
    }
}
