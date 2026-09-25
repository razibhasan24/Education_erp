<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'name_bn', 'code', 'class_id', 'group_id', 'full_marks', 'pass_marks', 'is_optional', 'is_active'];
    protected $casts = ['is_optional' => 'boolean', 'is_active' => 'boolean'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
