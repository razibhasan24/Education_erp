<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BroadcastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $title নোটিফিকেশনের শিরোনাম
     * @param string $message মূল বার্তা
     * @param array  $extra অতিরিক্ত ডেটা (url, icon, color)
     */
    public function __construct(
        public string $title,
        public string $message,
        public array $extra = []
    ) {}

    /**
     * কোন চ্যানেলে পাঠানো হবে
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Database এ যা সংরক্ষণ হবে
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'title' => $this->title,
            'message' => $this->message,
            'icon' => 'fas fa-bell',
            'color' => 'info',
            'url' => null,
            'created_at' => now()->toDateTimeString(),
        ], $this->extra);
    }

    /**
     * Database notification type কী হবে
     */
    public function databaseType(object $notifiable): string
    {
        return self::class;
    }
}
