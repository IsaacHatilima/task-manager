<?php

namespace App\Actions\Todo;

use App\Models\Todo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteTodoAction
{
    public function __construct() {}

    public function execute(Todo $todo)
    {
        if (! $this->isPasswordRecentlyConfirmed()) {
            return '401';
        }

        try {
            return DB::transaction(function () use ($todo) {
                $todo->delete();
            });
        } catch (Throwable $e) {
            Log::error($e);

            return '500';
        }
    }

    protected function isPasswordRecentlyConfirmed(): bool
    {
        $timeout = config('auth.password_timeout');
        $confirmedAt = session('password_confirmed_at');

        return $confirmedAt && now()->diffInSeconds($confirmedAt) < $timeout;
    }
}
