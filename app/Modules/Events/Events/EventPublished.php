<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public $publishedAt;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->publishedAt = now();
    }
}