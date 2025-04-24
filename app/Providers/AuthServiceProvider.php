<?php

namespace App\Providers;

use App\Models\Profile;
use App\Models\Todo;
use App\Models\User;
use App\Policies\ProfilePolicy;
use App\Policies\TodoPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Profile::class => ProfilePolicy::class,
        User::class => UserPolicy::class,
        Todo::class => TodoPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
