<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ToLetKoi! 🏠')
            ->greeting("Hello {$this->user->name}!")
            ->line('Thank you for joining ToLetKoi — Bangladesh\'s smartest rental marketplace.')
            ->line('You have received 50 welcome credits to get started. Use them to unlock contact details of properties you love.')
            ->line('You can:')
            ->line('• Browse thousands of rental properties')
            ->line('• Save your favorite listings')
            ->line('• Use KoiFind to let properties find you')
            ->action('Start Browsing', url('/'))
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Best regards, The ToLetKoi Team');
    }
}
