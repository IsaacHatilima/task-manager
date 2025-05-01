<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\Todo;
use App\Repository\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CreateTaskAction
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function execute(Request $request, Todo $todo): ?Task
    {
        try {
            return DB::transaction(function () use ($request, $todo) {
                return $this->taskRepository->create([
                    'todo_id' => $todo->id,
                    'title' => Str::title($request->title),
                    'description' => $request->description,
                    'status' => strtolower($request->status),
                    'user_id' => $request->user()->id,
                    'assigned' => $request->assigned ?? null,
                ]);
            });
        } catch (Throwable $e) {
            Log::error($e);

            return null;
        }
    }
}
