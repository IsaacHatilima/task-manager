<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StoreIntendedUrl
{
    /**
     * To set intended route to redirect users to todo after invite.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()
            && ! $request->is('/')
            && ! $request->is('register')
            && ! $request->is('login')
            && ! $request->is('google/callback')
            && ! $request->is('google/redirect')
            && ! $request->is('two-factor-challenge')
        ) {
            session(['url.intended' => $request->fullUrl()]);
        }

        return $next($request);
    }
}
