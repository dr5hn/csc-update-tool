<?php

use App\Models\Comment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\CommentNotification;
use Illuminate\Console\Scheduling\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Artisan::command('queue:send-pending-message', function () {
    $messages = Comment::where('created_at', '<', now()->subHour())->get();
    array_filter($messages, function ($message) {
        $message->user->notify(new CommentNotification($message));
    });
    $this->info('Pending messages sent successfully.');
})->purpose('Send pending messages')->hourly();


