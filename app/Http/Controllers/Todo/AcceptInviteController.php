<?php

namespace App\Http\Controllers\Todo;

use App\Actions\TodoCollaborator\AcceptInviteAction;
use App\Http\Controllers\Controller;
use App\Models\Todo;

class AcceptInviteController extends Controller
{
    public function __construct() {}

    public function acceptInvite(Todo $todo, string $token, AcceptInviteAction $acceptInviteAction)
    {
        $response = $acceptInviteAction->execute($todo, $token);

        if ($response === '404') {
            return redirect()->route('error.show', ['code' => $response])->with('error', 'Invalid invite token.');
        }

        if ($response === '401') {
            return redirect()->route('error.show', ['code' => $response])->with('error', 'Invite token has expired.');
        }

        return redirect()->intended(route('todos.show', ['todo' => $todo->id]));
    }
}
