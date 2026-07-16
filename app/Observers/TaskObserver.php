<?php

namespace App\Observers;

use App\Models\Task;
use App\Notifications\TaskCreatedNotification;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        if ($task->assignee) {
            $task->assignee->notify(new TaskCreatedNotification($task));
        }
    }
}
