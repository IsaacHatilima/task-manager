<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Repository\TaskRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class UpdateTaskAction
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function execute(Task $task, $request): ?Task
    {
        try {
            return DB::transaction(function () use ($task, $request) {
                return $this->taskRepository->update($task, [
                    'title' => Str::title($request->title),
                    'description' => $request->description,
                    'status' => strtolower($request->status),
                    'assigned' => $request->assigned,
                ]);
            });
        } catch (Throwable $e) {
            Log::error($e);

            return null;
        }
    }
}
