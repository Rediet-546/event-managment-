<?php

namespace App\Modules\Events\Services;

use App\Modules\Events\Models\Event;
use App\Modules\Events\Repositories\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CapacityService
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function hasAvailableCapacity(Event $event, int $requestedSpots = 1): bool
    {
        if (!$event->max_attendees) {
            return true; // Unlimited capacity
        }

        return ($event->current_attendees + $requestedSpots) <= $event->max_attendees;
    }

    public function incrementAttendees(Event $event, int $count = 1): bool
    {
        if (!$this->hasAvailableCapacity($event, $count)) {
            return false;
        }

        return DB::transaction(function () use ($event, $count) {
            $event->current_attendees += $count;
            return $event->save();
        });
    }

    public function decrementAttendees(Event $event, int $count = 1): bool
    {
        return DB::transaction(function () use ($event, $count) {
            $event->current_attendees = max(0, $event->current_attendees - $count);
            return $event->save();
        });
    }

    public function getAvailableSpots(Event $event): ?int
    {
        if (!$event->max_attendees) {
            return null;
        }

        return max(0, $event->max_attendees - $event->current_attendees);
    }

    public function getOccupancyPercentage(Event $event): ?float
    {
        if (!$event->max_attendees) {
            return null;
        }

        if ($event->max_attendees === 0) {
            return 0;
        }

        return ($event->current_attendees / $event->max_attendees) * 100;
    }
}