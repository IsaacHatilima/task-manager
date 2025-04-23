<?php

namespace App\Actions\Settings;

use App\Models\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Str;

class ProfileManagerAction
{
    private ProfileRepository $profileRepository;

    private UserRepository $userRepository;

    public function __construct(ProfileRepository $profileRepository, UserRepository $userRepository)
    {
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(User $user, object $request): void
    {
        $profileData = [
            'first_name' => Str::title($request->first_name),
            'last_name' => Str::title($request->last_name),
            'gender' => strtolower($request->gender),
        ];

        $this->userRepository->updateEmail($user, strtolower($request->email));

        $this->profileRepository->update($user->profile, $profileData);
    }
}
