<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public array $oldAttributes;
    public array $newAttributes;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event, array $oldAttributes, array $newAttributes)
    {
        $this->event = $event;
        $this->oldAttributes = $oldAttributes;
        $this->newAttributes = $newAttributes;
    }
}