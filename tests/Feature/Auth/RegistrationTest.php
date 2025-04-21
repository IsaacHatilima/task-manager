<?php

use App\Jobs\SendVerificationEmailJob;
use Inertia\Testing\AssertableInertia as Assert;

test('registration screen can be rendered', function () {

    $this->get(route('register'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/register')
            ->where('errors', [])
        );
});

test('new users can register and verify email', function () {
    Bus::fake([SendVerificationEmailJob::class]);

    $this->get(route('register'));

    $this
        ->followingRedirects()
        ->post(route('register.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'Password1#',
            'password_confirmation' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/verify-email')
            ->where('auth.user.email', 'test@example.com')
        );

    Bus::assertDispatched(SendVerificationEmailJob::class);
});
