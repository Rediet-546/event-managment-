<?php

namespace App\Modules\Events\Notifications;

use App\Modules\Events\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class EventCancelledNotification extends Notification
{
    use Queueable;

    protected Event $event;
    protected ?string $reason;

    public function __construct(Event $event, ?string $reason = null)
    {
        $this->event = $event;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Event Cancelled: ' . $this->event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We regret to inform you that the event "' . $this->event->title . '" has been cancelled.')
            ->line('**Event Details:**')
            ->line('📅 Originally scheduled: ' . $this->event->start_date->format('F j, Y g:i A'))
            ->line('📍 Location: ' . $this->event->venue . ', ' . $this->event->city);

        if ($this->reason) {
            $mail->line('📝 **Reason for cancellation:** ' . $this->reason);
        }

        // If this is for an attendee with a booking
        if ($notifiable->bookings()->where('event_id', $this->event->id)->exists()) {
            $mail->line('💰 **Refund Information:** Your payment will be refunded within 5-7 business days.')
                 ->action('View Booking Details', route('events.my-bookings'));
        }

        // If this is for the organizer
        if ($this->event->user_id === $notifiable->id) {
            $mail->line('Your event has been cancelled. All attendees have been notified.')
                 ->action('Manage Your Events', route('events.index'));
        }

        return $mail->line('We apologize for any inconvenience caused.');
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $isAttendee = $notifiable->bookings()->where('event_id', $this->event->id)->exists();
        $isOrganizer = $this->event->user_id === $notifiable->id;

        return new DatabaseMessage([
            'type' => 'event_cancelled',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'reason' => $this->reason,
            'is_attendee' => $isAttendee,
            'is_organizer' => $isOrganizer,
            'message' => $this->getMessageForUser($notifiable),
            'action_url' => $isOrganizer ? route('events.index') : route('events.my-bookings'),
            'icon' => 'x-circle',
            'color' => 'red'
        ]);
    }

    protected function getMessageForUser($notifiable): string
    {
        if ($this->event->user_id === $notifiable->id) {
            return 'Your event "' . $this->event->title . '" has been cancelled.';
        }

        if ($notifiable->bookings()->where('event_id', $this->event->id)->exists()) {
            return 'The event "' . $this->event->title . '" you booked has been cancelled.';
        }

        return 'Event "' . $this->event->title . '" has been cancelled.';
    }

    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'reason' => $this->reason,
            'cancelled_at' => now()->toDateTimeString()
        ];
    }
}