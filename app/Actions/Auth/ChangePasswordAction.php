<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    public function __construct() {}

    /*
     * Change authenticated user password.
     * */
    public function execute($request): void
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);
    }
}
