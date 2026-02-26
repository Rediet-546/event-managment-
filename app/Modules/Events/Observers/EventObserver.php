<?php

namespace App\Modules\Events\Observers;

use App\Modules\Events\Models\Event;
use Illuminate\Support\Facades\Cache;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        $this->clearCache();
    }

    /**
     * Clear module-related cache.
     */
    protected function clearCache(): void
    {
        Cache::tags(['events'])->flush();
    }
}