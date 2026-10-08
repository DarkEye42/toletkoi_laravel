<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Listing $listing,
        public string $message = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Inquiry on Your Listing')
            ->greeting("Hello {$this->listing->user->name}!")
            ->line("Someone is interested in your listing:")
            ->line("**{$this->listing->title}**")
            ->line("📍 {$this->listing->address}")
            ->line("💰 ৳" . number_format($this->listing->price) . " / {$this->listing->price_type}")
            ->line("**Message:** {$this->message}")
            ->action('View Listing', $this->listing->getUrl())
            ->salutation('Best regards, The ToLetKoi Team');
    }
}
