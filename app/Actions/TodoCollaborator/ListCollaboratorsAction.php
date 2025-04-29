<?php

namespace App\Actions\TodoCollaborator;

use App\Models\Todo;
use Illuminate\Http\Request;

class ListCollaboratorsAction
{
    public function __construct() {}

    public function execute(Request $request, Todo $todo): array
    {
        $query = $todo->accessibleUsers();

        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }

        return $query->paginate(10)->toArray();
    }
}
