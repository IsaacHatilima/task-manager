<?php

namespace App\Actions\Todo;

class ListTodosAction
{
    public function __construct() {}

    public function execute($request): array
    {
        $query = $request->user()->todos();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString()->toArray();
    }
}
