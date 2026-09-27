<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notice extends Model
{
    protected $fillable = [
        'title', 'title_bn', 'content', 'content_bn', 'audience',
        'priority', 'attachment', 'publish_date', 'expire_date',
        'is_published', 'send_sms', 'created_by',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expire_date' => 'date',
        'is_published' => 'boolean',
        'send_sms' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
            ->where('publish_date', '<=', now())
            ->where(fn($sq) => $sq->whereNull('expire_date')->orWhere('expire_date', '>=', now()));
    }

    public static function priorityLabels(): array
    {
        return [
            'low' => ['label' => 'নিম্ন', 'color' => 'secondary'],
            'normal' => ['label' => 'সাধারণ', 'color' => 'info'],
            'high' => ['label' => 'উচ্চ', 'color' => 'warning'],
            'urgent' => ['label' => 'অতি জরুরি', 'color' => 'danger'],
        ];
    }

    public static function audienceLabels(): array
    {
        return [
            'all' => 'সবাই',
            'students' => 'শিক্ষার্থী',
            'teachers' => 'শিক্ষক',
            'guardians' => 'অভিভাবক',
            'staff' => 'স্টাফ',
        ];
    }
}
