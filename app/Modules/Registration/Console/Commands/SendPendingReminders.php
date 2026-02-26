<?php

namespace App\Modules\Registration\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Registration\Models\User;
use App\Modules\Registration\Notifications\PendingApprovalReminder;

class SendPendingReminders extends Command
{
    protected $signature = 'registration:send-pending-reminders';
    protected $description = 'Send reminders to pending creators';

    public function handle()
    {
        $pendingCreators = User::where('user_type', 'event_creator')
            ->where('is_approved', false)
            ->where('created_at', '<', now()->subDays(3))
            ->get();

        foreach ($pendingCreators as $creator) {
            $creator->notify(new PendingApprovalReminder($creator));
        }

        $this->info("Sent reminders to {$pendingCreators->count()} pending creators.");
    }
}