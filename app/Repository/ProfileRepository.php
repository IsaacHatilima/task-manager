<?php

namespace App\Repository;

use App\Models\Profile;

class ProfileRepository
{
    public function __construct() {}

    /**
     * Create a new profile.
     *
     * @param  array  $data  Profile data.
     */
    public function create(array $data): Profile
    {
        return Profile::create($data);
    }

    /**
     * Update an existing profile.
     *
     * This method updates the profile’s first name, last name, and gender.
     *
     * @param  Profile  $profile  The profile to be updated.
     * @param  array  $data  The new profile data.
     */
    public function update(Profile $profile, array $data): void
    {
        $profile->update([
            'first_name' => ucwords($data['first_name']),
            'last_name' => ucwords($data['last_name']),
            'gender' => $data['gender'],
        ]);
    }
}
