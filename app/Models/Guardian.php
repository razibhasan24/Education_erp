<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'user_id', 'guardian_id', 'name', 'name_bn', 'relation', 'phone',
        'email', 'occupation', 'nid', 'present_address', 'permanent_address',
        'photo', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'guardian_student');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($g) {
            if (empty($g->guardian_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $next = $last ? $last->id + 1 : 1;
                $g->guardian_id = 'GDN' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
