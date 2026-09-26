<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $fillable = [
        'provider', 'api_key', 'sender_id', 'api_url', 'is_active',
        'notify_attendance', 'notify_result', 'notify_due',
        'attendance_template', 'result_template', 'due_template',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'notify_attendance' => 'boolean',
        'notify_result' => 'boolean',
        'notify_due' => 'boolean',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'provider' => 'bulksmsbd',
            'attendance_template' => 'প্রিয় অভিভাবক, আপনার সন্তান {student_name} আজ {status} হয়েছে। তারিখ: {date}। - {institute}',
            'result_template' => 'প্রিয় অভিভাবক, {exam_name} পরীক্ষায় {student_name} এর GPA: {gpa}, গ্রেড: {grade}। - {institute}',
            'due_template' => 'প্রিয় অভিভাবক, {student_name} এর {due_amount} টাকা ফি বাকি আছে। অনুগ্রহ করে পরিশোধ করুন। - {institute}',
        ]);
    }
}
