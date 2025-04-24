<?php

namespace App\Repository;

use App\Models\Todo;
use Illuminate\Support\Str;

class TodoRepository
{
    public function __construct() {}

    /**
     * Create a new profile.
     *
     * @param  array  $data  Todo data.
     */
    public function create(array $data): Todo
    {
        return Todo::create($data);
    }

    public function update(Todo $todo, array $data): Todo
    {
        $todo->update([
            'title' => Str::title($data['title']),
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        return $todo;
    }
}
