<?php

namespace App\Modules\Events\Events;

use App\Modules\Events\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Event $event;
    public ?int $userId;
    public string $ipAddress;
    public string $userAgent;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event, ?int $userId = null, string $ipAddress = '', string $userAgent = '')
    {
        $this->event = $event;
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }
}