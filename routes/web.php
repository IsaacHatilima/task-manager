<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    require __DIR__.'/settings.php';

    Route::resource('todos', TodoController::class);

});

require __DIR__.'/auth.php';

Route::get('/run-queue/{token}', function ($token) {
    if ($token !== config('app.cron')) {
        abort(403); // Unauthorized access
    }

    Artisan::call('queue:work --max-jobs=5');

    return response('Queue executed', 200);
});
