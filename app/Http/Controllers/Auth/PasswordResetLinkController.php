<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Jobs\SendPasswordResetEmailJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/forgot-password', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(PasswordResetRequest $request): RedirectResponse
    {
        SendPasswordResetEmailJob::dispatch($request->email);

        return back()->with('status', __('A reset link has been sent to your E-Mail.'));
    }
}
