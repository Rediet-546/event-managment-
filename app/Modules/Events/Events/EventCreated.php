<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event, array $data = [])
    {
        $this->event = $event;
        $this->data = $data;
    }
}