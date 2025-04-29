<?php

namespace App\Actions\TodoCollaborator;

use App\Jobs\SendInviteJob;
use App\Models\Todo;
use Illuminate\Http\Request;

class InviteCollaboratorAction
{
    public function __construct() {}

    public function execute(Request $request, Todo $todo): void
    {
        $inviterName = auth()->user()->profile->first_name;
        $todoName = $todo->title;
        $inviteUrl = route('todos.show', ['todo' => $todo->id]);

        SendInviteJob::dispatch($request->email, $inviterName, $todoName, $inviteUrl);
    }
}
