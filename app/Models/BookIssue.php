<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    protected $fillable = [
        'issue_no', 'book_id', 'student_id', 'teacher_id', 'issue_date',
        'due_date', 'return_date', 'fine_amount', 'status', 'remarks',
        'issued_by', 'returned_to',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public static function generateIssueNo(): string
    {
        $prefix = 'LIB-' . date('Ym') . '-';
        $last = self::where('issue_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->issue_no, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
