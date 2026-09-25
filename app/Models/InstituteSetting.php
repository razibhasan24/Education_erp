<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteSetting extends Model
{
    protected $fillable = [
        'institute_name', 'institute_name_bn', 'institute_type', 'eiin',
        'address', 'phone', 'email', 'logo', 'principal_name', 'session_year',
    ];
}
