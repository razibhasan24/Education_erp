<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = ['name', 'type', 'medium', 'eiin', 'address', 'logo', 'phone', 'email', 'is_active'];
    
    public function users() { return $this->hasMany(User::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function teachers() { return $this->hasMany(Teacher::class); }
    public function academicYears() { return $this->hasMany(AcademicYear::class); }
    public function classes() { return $this->hasMany(SchoolClass::class); }
}