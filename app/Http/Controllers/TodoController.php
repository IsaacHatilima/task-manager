<?php

namespace App\Http\Controllers;

use App\Actions\Todo\CreateTodoAction;
use App\Actions\Todo\DeleteTodoAction;
use App\Actions\Todo\UpdateTodoAction;
use App\Enums\TodoStatusEnum;
use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Todo::class);

        return Inertia::render('todo/index', [
            'todos' => Todo::paginate(15),
            'todoStatus' => TodoStatusEnum::getValues(),
            'deletedTodoMessage' => $request->session()->get('deletedTodoMessage'),
        ]);
    }

    public function store(TodoRequest $request, CreateTodoAction $createTodoAction): RedirectResponse
    {
        $this->authorize('create', Todo::class);

        $createTodoAction->execute($request);

        return back();
    }

    public function show(Todo $todo): Response
    {
        $this->authorize('view', $todo);

        return Inertia::render('todo/todo-details', [
            'todo' => $todo,
            'todoStatus' => TodoStatusEnum::getValues(),
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

        $deleteTodoAction->execute($todo);

        return redirect()->route('todos.index')->with('deletedTodoMessage', __('Todo deleted successfully.'));

    }
}
