<?php

namespace App\Modules\Events\Listeners;

use App\Modules\Events\Events\EventCreated;
use App\Modules\Events\Notifications\EventCreatedNotification;
use App\Modules\Events\Notifications\AdminEventCreatedNotification;

class SendEventCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(EventCreated $event): void
    {
        // Notify the organizer
        $event->event->organizer->notify(new EventCreatedNotification($event->event));
        
        // Notify admins (you'd need to get admin users)
        foreach (User::admins()->get() as $admin) {
            $admin->notify(new AdminEventCreatedNotification($event->event));
        }
    }
}