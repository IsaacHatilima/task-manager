<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\Todo;
use Illuminate\Http\Request;

class ListTaskAction
{
    public function __construct() {}

    public function execute(Request $request, Todo $todo): array
    {
        $query = Task::where('todo_id', $todo->id)
            ->with(['user', 'user.profile']);

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            $searchTerms = explode(' ', $request->input('assigned_to'));

            $query->whereHas('user.profile', function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where(function ($subQ) use ($term) {
                        $subQ->where('first_name', 'like', '%'.$term.'%')
                            ->orWhere('last_name', 'like', '%'.$term.'%');
                    });
                }
            });
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->toArray();
    }
}
