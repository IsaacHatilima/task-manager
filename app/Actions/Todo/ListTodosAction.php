<?php

namespace App\Actions\Todo;

use Illuminate\Http\Request;

class ListTodosAction
{
    public function __construct() {}

    public function execute(Request $request): array
    {
        return $request->user()->accessibleTodos()
            ->when($request->filled('title'), fn ($q) => $q->where('title', 'like', "%{$request->title}%"))
            ->when($request->filled('description'), fn ($q) => $q->where('description', 'like', "%{$request->description}%"))
            ->when($request->filled('status') && $request->status !== 'all', fn ($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->toArray();
    }
}
