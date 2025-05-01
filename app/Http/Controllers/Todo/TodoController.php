<?php

namespace App\Http\Controllers\Todo;

use App\Actions\Task\ListTaskAction;
use App\Actions\Todo\CreateTodoAction;
use App\Actions\Todo\DeleteTodoAction;
use App\Actions\Todo\ListTodosAction;
use App\Actions\Todo\UpdateTodoAction;
use App\Enums\TodoStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRequest;
use App\Models\Task;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, ListTodosAction $listTodosAction): Response
    {
        $this->authorize('viewAny', Todo::class);

        return Inertia::render('todo/index', [
            'todos' => $listTodosAction->execute($request),
            'todoStatus' => TodoStatusEnum::getValues(),
            'deletedTodoMessage' => $request->session()->get('deletedTodoMessage'),
            'filters' => [
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ],
        ]);
    }

    public function store(TodoRequest $request, CreateTodoAction $createTodoAction): RedirectResponse
    {
        $this->authorize('create', Todo::class);

        $createTodoAction->execute($request);

        return back();
    }

    public function show(Request $request, Todo $todo, ListTaskAction $listTaskAction): Response
    {
        $this->authorize('view', $todo);

        $allStatuses = ['pending', 'in_progress', 'cancelled', 'completed'];

        $taskCounts = collect($allStatuses)->mapWithKeys(function ($status) use ($todo) {
            return [
                $status => Task::where('todo_id', $todo->id)
                    ->where('status', $status)
                    ->count(),
            ];
        })->toArray();

        return Inertia::render('todo/todo-details', [
            'todo' => $todo->load('user'),
            'todoTasks' => $listTaskAction->execute($request, $todo),
            'taskCounts' => $taskCounts,
            'todoStatus' => TodoStatusEnum::getValues(),
            'todCollaborators' => $todo->accessibleUsers,
            'deletedTodoMessage' => $request->session()->get('deletedTodoMessage'),
            'filters' => [
                'title' => $request->title,
                'status' => $request->status,
                'assigned_to' => $request->assigned_to,
            ],
        ]);
    }

    public function update(TodoRequest $request, Todo $todo, UpdateTodoAction $updateTodoAction): RedirectResponse
    {
        $this->authorize('update', $todo);

        $updateTodoAction->execute($todo, $request);

        return back();
    }

    public function destroy(Todo $todo, DeleteTodoAction $deleteTodoAction): RedirectResponse
    {
        $this->authorize('delete', $todo);

        $response = $deleteTodoAction->execute($todo);

        if ($response === '401') {
            return back()->withErrors('deletedTodoMessage', __('401'));
        }

        return redirect()->route('todos.index')->with('deletedTodoMessage', __('Todo deleted successfully.'));

    }
}
