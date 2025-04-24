<?php

namespace App\Actions\Todo;

use App\Models\Todo;
use App\Repository\TodoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class UpdateTodoAction
{
    private TodoRepository $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    public function execute(Todo $todo, $request): ?Todo
    {
        try {
            return DB::transaction(function () use ($todo, $request) {
                return $this->todoRepository->update($todo, [
                    'title' => Str::title($request->title),
                    'description' => $request->description,
                    'status' => strtolower($request->status),
                ]);
            });
        } catch (Throwable $e) {
            Log::error($e);

            return null;
        }
    }
}
