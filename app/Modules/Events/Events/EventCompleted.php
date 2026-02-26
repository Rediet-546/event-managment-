<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public array $statistics;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->statistics = [
            'total_attendees' => $event->current_attendees,
            'total_revenue' => $event->current_attendees * $event->price,
            'completion_date' => now(),
        ];
    }
}