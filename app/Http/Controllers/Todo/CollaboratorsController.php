<?php

namespace App\Http\Controllers\Todo;

use App\Actions\TodoCollaborator\InviteCollaboratorAction;
use App\Actions\TodoCollaborator\ListCollaboratorsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteCollaboratorRequest;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CollaboratorsController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request, Todo $todo, ListCollaboratorsAction $listCollaboratorsAction): Response
    {
        $this->authorize('view', $todo);

        return Inertia::render('todo/collaborators', [
            'todo' => $todo,
            'todoMembers' => $listCollaboratorsAction->execute($request, $todo),
        ]);
    }

    public function store(InviteCollaboratorRequest $request, Todo $todo, InviteCollaboratorAction $inviteCollaboratorAction): RedirectResponse
    {
        $this->authorize('invite', $todo);

        $inviteCollaboratorAction->execute($request, $todo);

        return back();
    }
}
