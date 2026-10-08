<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactUnlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public Listing $listing,
        public int $creditsUsed,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $costText = $this->creditsUsed > 0
            ? "{$this->creditsUsed} credits were deducted from your wallet."
            : 'No credits were used (covered by your subscription).';

        return (new MailMessage)
            ->subject('Contact Unlocked — ' . $this->listing->title)
            ->greeting("Hello {$this->user->name}!")
            ->line("You have successfully unlocked the contact details for:")
            ->line("**{$this->listing->title}**")
            ->line("📍 {$this->listing->address}")
            ->line($costText)
            ->line("**Contact Name:** {$this->listing->contact_name}")
            ->line("**Phone:** {$this->listing->contact_phone}")
            ->action('View Listing', $this->listing->getUrl())
            ->salutation('Best regards, The ToLetKoi Team');
    }
}
