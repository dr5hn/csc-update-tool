<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminChangeRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $changeRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(ChangeRequest $changeRequest)
    {
        $this->changeRequest = $changeRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('change-requests.show', $this->changeRequest->id);

        return (new MailMessage)
            ->subject('New Change Request Submitted')
            ->greeting('Hello Admin,')
            ->line('A new change request has been submitted.')
            ->line('Title: ' . $this->changeRequest->title)
            ->line('Submitted by: ' . $this->changeRequest->user->name)
            ->line('Status: ' . ucfirst($this->changeRequest->status))
            ->action('View Change Request', $url)
            ->line('Please review this request at your earliest convenience.');
    }
}