<?php

namespace App\Modules\Registration\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Registration\Models\User;

class CleanUnverifiedUsers extends Command
{
    protected $signature = 'registration:clean-unverified {--days=30 : Delete unverified users older than X days}';
    protected $description = 'Delete unverified user accounts older than specified days';

    public function handle()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);

        $count = User::whereNull('email_verified_at')
            ->where('created_at', '<', $date)
            ->delete();

        $this->info("Deleted {$count} unverified user accounts older than {$days} days.");
    }
}