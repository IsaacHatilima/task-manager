<?php

namespace App\Repository;

use App\Models\Task;
use Illuminate\Support\Str;

class TaskRepository
{
    public function __construct() {}

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update([
            'title' => Str::title($data['title']),
            'description' => $data['description'],
            'status' => $data['status'],
            'assigned' => $data['assigned'],
        ]);

        return $task;
    }
}
