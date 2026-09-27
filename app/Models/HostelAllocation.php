<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelAllocation extends Model
{
    protected $fillable = ['student_id', 'hostel_id', 'hostel_room_id', 'start_date', 'end_date', 'status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'hostel_room_id');
    }
}
