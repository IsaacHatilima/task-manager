<?php

namespace App\Actions\TodoCollaborator;

use App\Jobs\SendInviteJob;
use App\Models\Todo;
use Illuminate\Http\Request;
use Str;

class InviteCollaboratorAction
{
    public function __construct() {}

    public function execute(Request $request, Todo $todo): void
    {
        $invite = $todo->invites()->create([
            'email' => $request->email,
            'token' => Str::random(32),
            'expires_at' => now()->addDays(2),
        ]);
        $inviterName = auth()->user()->profile->first_name;
        $todoName = $todo->title;
        $inviteUrl = route('invite-accept', ['todo' => $todo->id, 'token' => $invite->token]);

        SendInviteJob::dispatch($request->email, $inviterName, $todoName, $inviteUrl);
    }
}
