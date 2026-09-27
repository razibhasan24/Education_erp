<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title', 'title_bn', 'author', 'publisher', 'isbn', 'book_category_id',
        'edition', 'published_year', 'total_copies', 'available_copies',
        'shelf_no', 'price', 'cover_image', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function issues()
    {
        return $this->hasMany(BookIssue::class);
    }
}
