<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\TodoAccess;

class AcceptInviteController extends Controller
{
    public function __construct() {}

    public function acceptInvite(Todo $todo, string $token)
    {
        $invite = $todo->invites()->where('todo_id', $todo->id)->where('token', $token)->first();

        if (! $invite) {
            return redirect()->route('error.show', ['code' => 404])->with('error', 'Invalid invite token.');
        }

        if ($invite->expires_at < now()) {
            return redirect()->route('error.show', ['code' => 401])->with('error', 'Invite token has expired.');
        }

        TodoAccess::create([
            'todo_id' => $todo->id,
            'user_id' => auth()->user()->id,
        ]);
        $invite->delete();

        return redirect()->intended(route('todos.show', ['todo' => $todo->id]));
    }
}
