<?php

namespace App\Modules\Events\Listeners;

use App\Modules\Events\Events\EventCancelled;
use App\Modules\Events\Notifications\EventCancelledNotification;

class SendEventCancelledNotification
{
    /**
     * Handle the event.
     */
    public function handle(EventCancelled $event): void
    {
        // Notify all booked attendees
        foreach ($event->event->bookings as $booking) {
            $booking->user->notify(new EventCancelledNotification($event->event, $booking));
        }
        
        // Notify organizer
        $event->event->organizer->notify(new EventCancelledNotification($event->event));
    }
}