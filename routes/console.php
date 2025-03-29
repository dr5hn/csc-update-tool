<?php

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\CommentNotification;
use Illuminate\Support\Facades\Log;

Artisan::command('queue:send-pending-message', function () {
    try {
        // Get all messages created more than an hour ago
        $query = Comment::where('created_at', '>=', now()->subHour())
            ->where('created_at', '<=', now())
            ->with(['user', 'changeRequest']);
        $messages = $query->get();

        $this->info('Messages: ' . $messages->count());
        $this->info('Onehourbefore: ' . now()->subHour());
        $this->info('Time: ' . now()->toDateTimeString());
        if ($messages->isEmpty()) {
            // If there are no pending messages, output a message and return
            $this->info('No pending messages to send.');
            return;
        }

        // Group messages by user_id and then by change_request_id
        $groupedMessages = [];
        foreach ($messages as $message) {
            $userId = $message->user_id;
            $changeRequestId = $message->change_request_id;

            if (!isset($groupedMessages[$userId])) {
                $groupedMessages[$userId] = [];
            }

            if (!isset($groupedMessages[$userId][$changeRequestId])) {
                $groupedMessages[$userId][$changeRequestId] = [
                    'change_request' => $message->changeRequest,
                    'comments' => []
                ];
            }

            $groupedMessages[$userId][$changeRequestId]['comments'][] = $message;
        }

        // Track total users
        $totalUsers = count($groupedMessages);
        $totalMessages = $messages->count();

        // Iterate over each user and send a notification
        foreach ($groupedMessages as $userId => $userChangeRequests) {
            $user = User::find($userId);

            if ($user) {
                $user->notify(new CommentNotification($userChangeRequests));
            }
        }

        // Update the 'created_at' field for each message
        foreach ($messages as $message) {
            $message->created_at = now();
            $message->save();
        }

        // Output summary
        $this->info("Processed {$totalMessages} messages for {$totalUsers} users.");
    } catch (\Exception $e) {
        // Handle any exception that occurs during the command execution
        $this->error("An error occurred: " . $e->getMessage());
        Log::error("Error in queue:send-pending-message - " . $e->getMessage());
    }
})->purpose('Send pending messages');
