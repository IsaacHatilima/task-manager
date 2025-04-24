<?php

namespace App\Http\Controllers;

use App\Actions\Todo\CreateTodoAction;
use App\Enums\TodoStatusEnum;
use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class TodoController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Todo::class);

        return Inertia::render('todo/index', [
            'todos' => Todo::paginate(10),
            'todoStatus' => TodoStatusEnum::getValues(),
        ]);
    }

    public function store(TodoRequest $request, CreateTodoAction $createTodoAction)
    {
        $this->authorize('create', Todo::class);

        $createTodoAction->execute($request);

        return back();
    }

    public function show(Todo $todo)
    {
        $this->authorize('view', $todo);

        return $todo;
    }

    public function update(TodoRequest $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $todo->update($request->validated());

        return $todo;
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return response()->json();
    }
}
