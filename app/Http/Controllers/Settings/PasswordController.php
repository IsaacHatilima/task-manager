<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Auth\ChangePasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/password', [
            'socialRegistration' => is_null($request->user()->password),
        ]);
    }

    /**
     * Update the user's password.
     */
    public function update(SetPasswordRequest $request, ChangePasswordAction $changePasswordAction): RedirectResponse
    {
        $changePasswordAction->execute($request);

        return back();
    }
}
