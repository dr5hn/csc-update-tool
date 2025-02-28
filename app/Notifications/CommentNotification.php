<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $userChangeRequests;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $userChangeRequests)
    {
        $this->userChangeRequests = $userChangeRequests;
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
        $mailMessage = (new MailMessage)
            ->subject("New Comments on Your Change Requests")
            ->greeting("Hello {$notifiable->name},");

        $totalComments = 0;

        foreach ($this->userChangeRequests as $changeRequestId => $data) {
            // Make sure change_request exists before using it
            if (!isset($data['change_request']) || !$data['change_request']) {
                continue; // Skip this iteration if change_request is missing or null
            }

            $changeRequest = $data['change_request'];
            $comments = $data['comments'] ?? [];
            $totalComments += count($comments);

            // Use null coalescing operator to avoid null property access
            $title = $changeRequest->title ?? 'Untitled Request';
            $url = route('change-requests.show', $changeRequestId);

            $mailMessage->line(new HtmlString("<strong>Change Request: {$title}</strong>"));

            foreach ($comments as $comment) {
                // Check if user exists before accessing name
                $commentAuthor = $comment->user->name ?? 'Unknown User';
                $mailMessage->line(new HtmlString("Comment by {$commentAuthor}:"));
                $mailMessage->line(new HtmlString('<em>"' . nl2br(e($comment->content ?? 'No content')) . '"</em>'));
            }

            $mailMessage->action("View Request", $url);
            $mailMessage->line('---');
        }

        $mailMessage->line("You received a total of {$totalComments} new comments across your change requests.");

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [];

        foreach ($this->userChangeRequests as $changeRequestId => $changeRequestData) {
            $comments = $changeRequestData['comments'];
            $data[$changeRequestId] = [
                'change_request_id' => $changeRequestId,
                'change_request_title' => $changeRequestData['change_request']->title,
                'comments' => []
            ];

            foreach ($comments as $comment) {
                $data[$changeRequestId]['comments'][] = [
                    'author_name' => $comment->user->name,
                    'comment_content' => $comment->content,
                ];
            }
        }

        return $data;
    }
}
