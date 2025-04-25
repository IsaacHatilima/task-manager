<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CurrentPasswordRequest;

class CustomPasswordConfirmationController extends Controller
{
    /**
     * Custom Password Confirmation in favour of default Laravel which does not proceed to intended route
     * Laravel password_timeout remains unchanged.
     * Confirmation triggered by UI modal
     */
    public function confirm(CurrentPasswordRequest $request)
    {
        return back();
    }
}
