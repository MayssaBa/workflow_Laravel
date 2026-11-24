<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskHistory;

class TaskObserver
{
    /**
     * Record task creation.
     */
    public function created(Task $task): void
    {
        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'field' => null,
            'old_value' => null,
            'new_value' => json_encode(
                $task->getAttributes(),
                JSON_UNESCAPED_UNICODE
            ),
        ]);
    }

    /**
     * Record task updates with correct old and new values.
     */
    public function updated(Task $task): void
    {
        $changes = $task->getChanges();
        $original = $task->getOriginal();

        unset($changes['updated_at']);

        foreach ($changes as $field => $newValue) {

            $oldValue = $original[$field] ?? null;

            if ($field === 'assigned_to') {
                $action = 'assigned';
            } elseif ($field === 'status' && $newValue === 'done') {
                $action = 'closed';
            } elseif ($field === 'status') {
                $action = 'status_changed';
            } else {
                $action = 'updated';
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'action' => $action,
                'field' => $field,
                'old_value' => $oldValue === null ? null : (string) $oldValue,
                'new_value' => $newValue === null ? null : (string) $newValue,
            ]);
        }
    }

    /**
     * Record soft deletion.
     */
    public function deleted(Task $task): void
    {
        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'field' => 'deleted_at',
            'old_value' => null,
            'new_value' => $task->deleted_at?->toDateTimeString(),
        ]);
    }

    /**
     * Record task restoration.
     */
    public function restored(Task $task): void
    {
        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'restored',
            'field' => 'deleted_at',
            'old_value' => 'deleted',
            'new_value' => null,
        ]);
    }
}
