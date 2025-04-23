<?php

namespace App\Actions\Settings;

use Illuminate\Support\Facades\Auth;

class DeleteProfileAction
{
    public function __construct() {}

    public function execute($request): void
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
