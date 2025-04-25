<?php

use App\Http\Controllers\Settings\CustomPasswordConfirmationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('settings', 'settings/profile');

Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

Route::get('settings/two-factor-authentication', [TwoFactorController::class, 'edit'])->name('two-factor-authentication.edit');
Route::put('settings/two-factor-authentication-recovery-codes', [TwoFactorController::class, 'update'])->name('two-factor-authentication.update');

Route::get('settings/appearance', function () {
    return Inertia::render('settings/appearance');
})->name('appearance');

Route::post('password-confirmation', [CustomPasswordConfirmationController::class, 'confirm'])->name('password.confirmation');
