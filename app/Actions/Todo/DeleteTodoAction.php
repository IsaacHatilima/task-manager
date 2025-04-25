<?php

namespace App\Actions\Todo;

use App\Models\Todo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteTodoAction
{
    public function __construct() {}

    public function execute(Todo $todo): void
    {
        try {
            DB::transaction(function () use ($todo) {
                $todo->delete();
            });
        } catch (Throwable $e) {
            Log::error($e);
        }
    }
}
