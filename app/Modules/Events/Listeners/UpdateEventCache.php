<?php

namespace App\Modules\Events\Listeners;

use App\Modules\Events\Events\EventCreated;
use App\Modules\Events\Events\EventUpdated;
use App\Modules\Events\Events\EventDeleted;
use App\Modules\Events\Events\EventPublished;
use App\Modules\Events\Events\EventCancelled;
use App\Modules\Events\Events\EventCapacityReached;
use Illuminate\Support\Facades\Cache;

class UpdateEventCache
{
    /**
     * Handle event created.
     */
    public function handleCreated(EventCreated $event): void
    {
        Cache::tags(['events'])->flush();
    }

    /**
     * Handle event updated.
     */
    public function handleUpdated(EventUpdated $event): void
    {
        Cache::tags(['events'])->forget('event_' . $event->event->id);
        Cache::tags(['events'])->forget('featured_events');
        Cache::tags(['events'])->forget('upcoming_events');
    }

    /**
     * Handle event deleted.
     */
    public function handleDeleted(EventDeleted $event): void
    {
        Cache::tags(['events'])->flush();
    }

    /**
     * Handle event published.
     */
    public function handlePublished(EventPublished $event): void
    {
        Cache::tags(['events'])->flush();
    }

    /**
     * Handle event cancelled.
     */
    public function handleCancelled(EventCancelled $event): void
    {
        Cache::tags(['events'])->forget('event_' . $event->event->id);
        Cache::tags(['events'])->forget('upcoming_events');
    }

    /**
     * Handle capacity reached.
     */
    public function handleCapacityReached(EventCapacityReached $event): void
    {
        Cache::tags(['events'])->forget('event_' . $event->event->id);
    }
}