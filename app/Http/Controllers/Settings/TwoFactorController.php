<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\DownloadCodesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DownloadCodesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorController extends Controller
{
    /**
     * Show the two-factor authentication settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/two-factor-setup', [
            'twoFactorEnabled' => ! is_null($request->user()->two_factor_secret),
            'setupCode' => auth()->user()->two_factor_secret ? decrypt(auth()->user()->two_factor_secret) : '',
        ]);
    }

    public function update(DownloadCodesRequest $request, DownloadCodesAction $downloadCodesAction): RedirectResponse
    {
        $downloadCodesAction->execute(auth()->user(), $request);

        return back();
    }
}
