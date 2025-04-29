<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ErrorPageController extends Controller
{
    public function show(Request $request): Response
    {

        return Inertia::render('errors');
    }
}
