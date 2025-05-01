<?php

namespace App\Http\Controllers;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function store(TaskRequest $request, Todo $todo, CreateTaskAction $createTaskAction): RedirectResponse
    {
        $this->authorize('canManageTaskInTodo', $todo);

        $createTaskAction->execute($request, $todo);

        return back();
    }

    public function update(TaskRequest $request, Todo $todo, Task $task, UpdateTaskAction $updateTaskAction): RedirectResponse
    {
        $this->authorize('canManageTaskInTodo', $todo);

        $updateTaskAction->execute($task, $request);

        return redirect(route('todos.show', $todo->id));
    }

    public function destroy(Todo $todo, Task $task)
    {
        $this->authorize('canManageTaskInTodo', $todo);

        $task->delete();

        return back();
    }
}
