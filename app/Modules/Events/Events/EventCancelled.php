<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event, string $reason = '')
    {
        $this->event = $event;
        $this->reason = $reason;
    }
}