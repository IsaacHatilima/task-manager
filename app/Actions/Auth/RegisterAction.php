<?php

namespace App\Actions\Auth;

use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RegisterAction
{
    /**
     * Create a new class instance.
     */
    private ProfileRepository $profileRepository;

    private UserRepository $userRepository;

    public function __construct(ProfileRepository $profileRepository, UserRepository $userRepository)
    {
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(Request $request): ?User
    {
        try {
            return DB::transaction(function () use ($request) {
                $user = $this->userRepository->create([
                    'email' => strtolower($request->email),
                    'password' => Hash::make($request->password),
                ]);

                $this->profileRepository->create([
                    'user_id' => $user->id,
                    'first_name' => Str::title($request->first_name),
                    'last_name' => Str::title($request->last_name),
                ]);

                SendVerificationEmailJob::dispatch($user);

                return $user;
            });
        } catch (Throwable $e) {
            Log::error($e);

            return null;
        }
    }
}
