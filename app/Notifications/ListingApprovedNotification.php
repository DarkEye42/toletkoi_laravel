<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Listing $listing,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Listing is Now Live! 🎉')
            ->greeting("Hello {$this->listing->user->name}!")
            ->line("Great news! Your listing has been approved and is now live on ToLetKoi.")
            ->line("**{$this->listing->title}**")
            ->line("📍 {$this->listing->address}")
            ->line("💰 ৳" . number_format($this->listing->price) . " / {$this->listing->price_type}")
            ->action('View Your Listing', $this->listing->getUrl())
            ->salutation('Best regards, The ToLetKoi Team');
    }
}
