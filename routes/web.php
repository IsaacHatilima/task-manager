<?php

use App\Http\Controllers\Todo\MembersController;
use App\Http\Controllers\Todo\TodoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    require __DIR__.'/settings.php';

    Route::resource('todos', TodoController::class);

    Route::get('/todos/{todo}/members', [MembersController::class, 'show'])->name('todos.members.index');
    Route::post('/todos/{todo}/members', [MembersController::class, 'store'])->name('todos.members.store');

});

require __DIR__.'/auth.php';

/**
 * For testing purposes only, application running on shared hosting
 * Runs 1 queue job per minute
 */
Route::get('/run-queue/{token}', function ($token) {
    if ($token !== config('app.cron')) {
        abort(403); // Unauthorized access
    }

    try {
        Artisan::call('queue:work --once');
    } catch (Exception $e) {
        Log::error('Queue worker failed: '.$e->getMessage());
    }

    return response('Queue executed', 200);
});
