<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Profile::class);

        return Profile::all();
    }

    public function store(ProfileRequest $request)
    {
        $this->authorize('create', Profile::class);

        return Profile::create($request->validated());
    }

    public function show(Profile $profile)
    {
        $this->authorize('view', $profile);

        return $profile;
    }

    public function update(ProfileRequest $request, Profile $profile)
    {
        $this->authorize('update', $profile);

        $profile->update($request->validated());

        return $profile;
    }

    public function destroy(Profile $profile)
    {
        $this->authorize('delete', $profile);

        $profile->delete();

        return response()->json();
    }
}
