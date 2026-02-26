<?php

namespace App\Modules\Events\Notifications;

use App\Modules\Events\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EventCreatedNotification extends Notification
{
    use Queueable;

    protected Event $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Event Created: ' . $this->event->title)
            ->line('Your event has been created successfully.')
            ->line('Title: ' . $this->event->title)
            ->line('Date: ' . $this->event->start_date->format('M d, Y H:i'))
            ->action('View Event', route('events.show', $this->event->slug))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'message' => 'Your event has been created successfully.',
        ];
    }
}
