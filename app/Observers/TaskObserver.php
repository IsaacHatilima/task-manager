<?php

namespace App\Observers;

use App\Enums\TodoStatusEnum;
use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        if ($task->isDirty('status') && $task->status !== TodoStatusEnum::COMPLETED->value && $task->status !== TodoStatusEnum::CANCELLED->value) {
            $task->todo->update(['status' => TodoStatusEnum::IN_PROGRESS->value]);
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        if ($task->isDirty('status') && $task->status === TodoStatusEnum::COMPLETED->value) {
            $this->maybeCompleteTodo($task);
        }
    }

    protected function maybeCompleteTodo(Task $task): void
    {
        $todo = $task->todo;

        if (! $todo) {
            return;
        }

        $allCompleted = $todo->tasks()->where('status', '!==', TodoStatusEnum::COMPLETED->value)->doesntExist();

        if ($allCompleted && $todo->status !== TodoStatusEnum::COMPLETED->value) {
            $todo->update(['status' => TodoStatusEnum::COMPLETED->value]);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
