<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $changeRequest;
    private $status;
    private $reason;

    public function __construct(ChangeRequest $changeRequest, string $status, ?string $reason = null)
    {
        $this->changeRequest = $changeRequest;
        $this->status = $status;
        $this->reason = $reason;
        // $this->delay(now()->addMinute(2));
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Change Request {$this->status}: {$this->changeRequest->title}")
            ->greeting("Hello {$notifiable->name},");

        if ($this->status === 'approved') {
            $message->line("Your change request '{$this->changeRequest->title}' has been approved.")
                   ->line('The changes will be implemented according to your request.')
                   ->action('View Change Request', route('change-requests.show', $this->changeRequest));
        } else {
            $message->line("Your change request '{$this->changeRequest->title}' has been rejected.")
                   ->line('Reason for rejection:')
                   ->line($this->reason)
                   ->action('View Change Request', route('change-requests.show', $this->changeRequest))
                   ->line('You may submit a new change request addressing the feedback provided.');
        }

        return $message->line('Thank you for using our application.');
    }
}