<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicLinkNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $magicLink
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Magic Link')
            ->line('Click the button below to sign in to your account.')
            ->action('Sign In', $this->magicLink)
            ->line('This link will expire in 1 hour.')
            ->line('If you did not request this, no further action is required.');
    }
}