<?php

namespace App\Http\Controllers\Todo;

use App\Actions\TodoCollaborator\InviteCollaboratorAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteCollaboratorRequest;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MembersController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request, Todo $todo): Response
    {
        $this->authorize('view', $todo);

        return Inertia::render('todo/members', [
            'todo' => $todo,
            'todoMembers' => $todo->accessibleUsers()->paginate(10),
        ]);
    }

    public function store(InviteCollaboratorRequest $request, Todo $todo, InviteCollaboratorAction $inviteCollaboratorAction): RedirectResponse
    {
        $this->authorize('invite', $todo);

        $inviteCollaboratorAction->execute($request, $todo);

        return back();
    }
}
