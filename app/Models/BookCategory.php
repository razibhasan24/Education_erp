<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $fillable = ['name', 'name_bn', 'code', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
