<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\DeleteProfileAction;
use App\Actions\Settings\ProfileManagerAction;
use App\Enums\GenderEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CurrentPasswordRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'gender' => GenderEnum::getValues(),
        ]);
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request, ProfileManagerAction $profileManagerAction): RedirectResponse
    {
        $profileManagerAction->execute($request->user(), $request);

        $request->user()->load('profile')->refresh();

        return back();
    }

    /**
     * Delete the user's account.
     */
    public function destroy(CurrentPasswordRequest $request, DeleteProfileAction $deleteProfileAction): RedirectResponse
    {
        $deleteProfileAction->execute($request);

        return redirect('/');
    }
}
