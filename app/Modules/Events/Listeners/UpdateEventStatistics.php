<?php

namespace App\Modules\Events\Listeners;

use App\Modules\Events\Events\EventViewed;
use App\Modules\Events\Events\EventCapacityReached;
use App\Modules\Events\Models\EventStatistic;

class UpdateEventStatistics
{
    /**
     * Handle event viewed.
     */
    public function handleViewed(EventViewed $event): void
    {
        // Record view in statistics table
        EventStatistic::create([
            'event_id' => $event->event->id,
            'user_id' => $event->userId,
            'type' => 'view',
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
            'data' => [
                'referer' => request()->header('referer'),
                'session_id' => session()->getId(),
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * Handle capacity reached.
     */
    public function handleCapacityReached(EventCapacityReached $event): void
    {
        // Record capacity reached milestone
        EventStatistic::create([
            'event_id' => $event->event->id,
            'type' => 'capacity_reached',
            'data' => [
                'capacity' => $event->capacity,
                'registered' => $event->registered,
                'percentage' => ($event->registered / $event->capacity) * 100,
            ],
            'created_at' => now(),
        ]);
    }
}