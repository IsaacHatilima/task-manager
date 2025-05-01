<?php

use App\Http\Controllers\ErrorPageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Todo\AcceptInviteController;
use App\Http\Controllers\Todo\CollaboratorsController;
use App\Http\Controllers\Todo\TodoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    require __DIR__.'/settings.php';

    Route::resource('todos', TodoController::class);

    Route::get('/todos/{todo}/collaborators', [CollaboratorsController::class, 'show'])->name('todos.collaborators.index');
    Route::post('/todos/{todo}/collaborators', [CollaboratorsController::class, 'store'])->name('todos.collaborators.store');
    Route::delete('/todos/{todo}/collaborators/{user}', [CollaboratorsController::class, 'destroy'])->name('todos.collaborators.destroy');
    Route::get('/invite/{todo}/{token}', [AcceptInviteController::class, 'acceptInvite'])->name('invite-accept');

    Route::post('/todo-task/{todo}', [TaskController::class, 'store'])->name('todo.task.store');
    Route::put('/todo-task/{todo}/{task}', [TaskController::class, 'update'])->name('todo.task.update');
    Route::delete('/todo-task/{todo}/{task}', [TaskController::class, 'destroy'])->name('todo.task.destroy');

});

Route::get('/error/{code}', [ErrorPageController::class, 'show'])->name('error.show');

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
