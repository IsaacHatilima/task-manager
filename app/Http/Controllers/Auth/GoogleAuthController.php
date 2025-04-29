<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\GoogleRegisterAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     *
     * @return RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback after Google authentication.
     *
     * @return RedirectResponse
     */
    public function handleGoogleCallback(GoogleRegisterAction $googleRegisterAction)
    {
        /** @var GoogleProvider $driver */
        $driver = Socialite::driver('google');

        // Retrieve the authenticated user's information from Google
        $googleUser = $driver->stateless()->user();

        // Ensure required fields are present
        if (! isset($googleUser->user['given_name']) || ! isset($googleUser->user['family_name'])) {
            return back()->withErrors(['error' => 'Your Google account is missing names.']);
        }

        // Try to find an existing user by email
        $user = User::where('email', $googleUser->email)->first();

        // If no user exists, register a new user
        if (! $user) {
            $data = [
                'email' => $googleUser->email,
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
            ];

            $user = $googleRegisterAction->execute((object) $data);
        }

        // If user creation failed, return with error
        if (! $user) {
            return back()->withErrors([
                'error' => 'Registration failed. Please try again.',
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Update the last login timestamp
        $user->update([
            'last_login_at' => now(),
        ]);

        // Redirect to the Intended route or dashboard
        return redirect()->intended(route('dashboard'));
    }
}
