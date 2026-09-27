<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class PurgeOldNotifications extends Command
{
    protected $signature = 'notifications:purge {--days=90}';
    protected $description = 'পুরোনো read notification মুছে ফেলুন';

    public function handle()
    {
        $days = (int) $this->option('days');
        $deleted = Notification::whereNotNull('read_at')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("{$deleted}টি পুরোনো notification মুছে ফেলা হয়েছে।");
    }
}
