<?php

namespace App\Modules\Registration\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PendingApprovalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Creator Account Still Pending Approval')
            ->greeting('Hello ' . $this->user->first_name . '!')
            ->line('Your event creator account is still pending approval.')
            ->line('Our team is reviewing your application and you will be notified once approved.')
            ->line('Thank you for your patience!')
            ->line('If you have any questions, please contact our support team.');
    }
}