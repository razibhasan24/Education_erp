<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    use HasFactory, HasUuids;

    /**
     * Notifications table এ primary key UUID
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Notifiable (User / Student / Guardian) — polymorphic relation
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('notifiable_type', User::class)
            ->where('notifiable_id', $userId);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            return $this->forceFill(['read_at' => now()])->save();
        }
        return false;
    }

    public function markAsUnread(): bool
    {
        if (!is_null($this->read_at)) {
            return $this->forceFill(['read_at' => null])->save();
        }
        return false;
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Data থেকে টাইটেল বের করা
     */
    public function getTitleAttribute(): string
    {
        return $this->data['title'] ?? 'নোটিফিকেশন';
    }

    public function getMessageAttribute(): string
    {
        return $this->data['message'] ?? '';
    }

    public function getIconAttribute(): string
    {
        return $this->data['icon'] ?? 'fas fa-bell';
    }

    public function getColorAttribute(): string
    {
        return $this->data['color'] ?? 'info';
    }

    public function getUrlAttribute(): ?string
    {
        return $this->data['url'] ?? null;
    }

    /**
     * কত সময় আগে তৈরি হয়েছে
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * নির্দিষ্ট ইউজারের unread count
     */
    public static function unreadCountFor(int $userId): int
    {
        return static::forUser($userId)->unread()->count();
    }

    /**
     * একাধিক ইউজারের জন্য একসাথে নোটিফিকেশন তৈরি
     */
    public static function broadcastTo(array $userIds, string $title, string $message, array $extra = []): int
    {
        $data = array_merge([
            'title' => $title,
            'message' => $message,
            'icon' => 'fas fa-bell',
            'color' => 'info',
            'url' => null,
        ], $extra);

        $inserted = 0;
        $now = now();

        $rows = [];
        foreach ($userIds as $userId) {
            $rows[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\BroadcastNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $userId,
                'data' => json_encode($data),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            foreach (array_chunk($rows, 500) as $chunk) {
                Notification::insert($chunk);
                $inserted += count($chunk);
            }
        }

        return $inserted;
    }

    /**
     * সব read হিসেবে চিহ্নিত করা (নির্দিষ্ট ইউজারের)
     */
    public static function markAllReadFor(int $userId): int
    {
        return static::forUser($userId)->unread()->update(['read_at' => now()]);
    }
}