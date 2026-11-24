<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle tâche assignée',
            'message' => 'Une nouvelle tâche vous a été assignée : '.$this->task->title,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
        ];
    }
}
