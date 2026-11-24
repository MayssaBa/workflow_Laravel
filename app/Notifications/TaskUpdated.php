<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskUpdated extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Tâche modifiée',
            'message' => 'La tâche "' . $this->task->title . '" a été modifiée.',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
        ];
    }
}
