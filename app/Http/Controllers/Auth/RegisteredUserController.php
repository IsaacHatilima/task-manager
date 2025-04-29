<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(RegisterRequest $request, RegisterAction $registerAction): RedirectResponse
    {
        $user = $registerAction->execute($request);

        if (! $user) {
            return back()->withErrors([
                'error' => 'Registration failed. Please try again.',
            ]);
        }

        Auth::login($user);

        $user->update([
            'last_login_at' => now(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }
}
