<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreatedNotification extends Notification
{
    public function __construct(
        public int $taskId
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $task = \App\Models\Task::find($this->taskId);

        return (new MailMessage)
            ->subject('Task created')
            ->line("Task: {$task->title}");
    }
}
