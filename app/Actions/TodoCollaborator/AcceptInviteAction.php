<?php

namespace App\Actions\TodoCollaborator;

use App\Models\Todo;
use App\Models\TodoAccess;

class AcceptInviteAction
{
    public function __construct() {}

    public function execute(Todo $todo, string $token): string
    {
        $invite = $todo->invites()->where('todo_id', $todo->id)->where('token', $token)->first();

        if (! $invite) {
            return '404';
        }

        if ($invite->expires_at < now()) {
            return '401';
        }

        TodoAccess::firstOrCreate([
            'todo_id' => $todo->id,
            'user_id' => auth()->id(),
        ]);

        $invite->delete();

        return '200';
    }
}
