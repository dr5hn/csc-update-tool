<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    protected $changeRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->changeRequest = $comment->changeRequest;
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
        $commentAuthor = $this->comment->user->name;
        $requestTitle = $this->changeRequest->title;

        return (new MailMessage)
            ->subject("New Comment on Change Request: {$requestTitle}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new comment has been added to the change request '{$requestTitle}' by {$commentAuthor}.")
            ->line(new HtmlString('<strong>Comment:</strong>'))
            ->line(new HtmlString('<em>"' . nl2br(e($this->comment->content)) . '"</em>'))
            ->action('View Change Request', $url)
            ->line('You can respond to this comment by visiting the change request page.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'change_request_id' => $this->changeRequest->id,
            'author_name' => $this->comment->user->name,
            'comment_content' => $this->comment->content,
        ];
    }
}