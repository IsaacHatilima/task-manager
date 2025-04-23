<?php

namespace App\Actions\Settings;

use App\Models\User;
use App\Repository\UserRepository;

class DownloadCodesAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(User $user, object $request): void
    {
        $this->userRepository->updateDownloadedCodes($user, $request->downloaded_codes);
    }
}
