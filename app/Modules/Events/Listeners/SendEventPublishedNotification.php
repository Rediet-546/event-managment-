<?php

namespace App\Modules\Events\Listeners;

use App\Modules\Events\Events\EventPublished;
use App\Modules\Events\Notifications\EventPublishedNotification;

class SendEventPublishedNotification
{
    /**
     * Handle the event.
     */
    public function handle(EventPublished $event): void
    {
        // Notify organizer that event is live
        $event->event->organizer->notify(new EventPublishedNotification($event->event));
        
        // Notify followers/subscribers (if you have that feature)
        foreach ($event->event->category->followers as $follower) {
            $follower->notify(new NewEventInCategoryNotification($event->event));
        }
    }
}