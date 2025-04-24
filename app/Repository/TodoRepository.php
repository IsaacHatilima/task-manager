<?php

namespace App\Repository;

use App\Models\Todo;

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
}
