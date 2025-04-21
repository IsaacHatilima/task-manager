<?php

namespace App\Repository;

use App\Models\Profile;
use App\Models\User;

class UserRepository
{
    public function __construct() {}

    /**
     * Create a new profile.
     *
     * @param  array  $data  User data.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * This method updates the user email.
     *
     * @param  User  $user  The profile to be updated.
     * @param  string  $email  The new profile data.
     */
    public function updateEmail(User $user, string $email): void
    {
        $normalized = strtolower($email);

        if ($user->email !== $normalized) {
            $user->email = $normalized;
            $user->email_verified_at = null;
            $user->save();
        }
    }
}
