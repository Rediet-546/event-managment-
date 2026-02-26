<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCapacityReached
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public int $capacity;
    public int $registered;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->capacity = $event->max_attendees;
        $this->registered = $event->current_attendees;
    }
}