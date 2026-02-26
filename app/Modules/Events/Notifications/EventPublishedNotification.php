<?php

namespace App\Modules\Events\Notifications;

use App\Modules\Events\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class EventPublishedNotification extends Notification
{
    use Queueable;

    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Event Has Been Published: ' . $this->event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your event "' . $this->event->title . '" has been published and is now live!')
            ->line('**Event Details:**')
            ->line('📅 Date: ' . $this->event->start_date->format('F j, Y g:i A'))
            ->line('📍 Location: ' . $this->event->venue . ', ' . $this->event->city)
            ->line('🎫 Price: ' . $this->event->formatted_price)
            ->action('View Your Event', route('events.show', $this->event->slug))
            ->line('Start promoting your event to attract attendees!')
            ->line('Thank you for using our platform!');
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'event_published',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'message' => 'Your event "' . $this->event->title . '" has been published and is now live!',
            'action_url' => route('events.show', $this->event->slug),
            'icon' => 'check-circle',
            'color' => 'green'
        ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'published_at' => now()->toDateTimeString()
        ];
    }
}