<?php

namespace App\Actions\Todo;

use App\Models\Todo;
use App\Repository\TodoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CreateTodoAction
{
    private TodoRepository $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    public function execute(Request $request): ?Todo
    {
        try {
            return DB::transaction(function () use ($request) {
                return $this->todoRepository->create([
                    'title' => Str::title($request->title),
                    'description' => $request->description,
                    'status' => strtolower($request->status),
                    'user_id' => $request->user()->id,
                ]);
            });
        } catch (Throwable $e) {
            Log::error($e);

            return null;
        }
    }
}
