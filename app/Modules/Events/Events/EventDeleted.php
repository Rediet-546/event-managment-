<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public int $eventId;
    public string $eventTitle;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->eventId = $event->id;
        $this->eventTitle = $event->title;
    }
}