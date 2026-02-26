<?php

namespace App\Modules\Registration\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $temporaryPassword;

    public function __construct($user, $temporaryPassword = null)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->greeting('Hello ' . $this->user->first_name . '!')
            ->line('Welcome to ' . config('app.name') . '! We\'re excited to have you on board.');

        if ($this->temporaryPassword) {
            $mail->line('Your temporary password is: **' . $this->temporaryPassword . '**')
                 ->line('Please change your password after logging in.');
        }

        if ($this->user->user_type === 'attendee') {
            $mail->line('Start exploring events and book your first ticket today!')
                 ->action('Browse Events', route('events.index'));
        } else {
            $mail->line('Your creator account is pending approval. We\'ll notify you once approved.')
                 ->line('In the meantime, you can start planning your events!')
                 ->action('Start Planning', route('creator.pending'));
        }

        return $mail->line('Thank you for joining us!');
    }
}