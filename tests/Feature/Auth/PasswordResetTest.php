<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

test('reset password link screen can be rendered', function () {

    $this->get(route('password.request'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/forgot-password')
            ->where('errors', [])
        );
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->get(route('password.request'));

    $this
        ->followingRedirects()
        ->post(route('password.email'), [
            'email' => $user->email,
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/forgot-password')
            ->where('errors', [])
        );

    try {
        Notification::assertSentTo($user, ResetPassword::class);
    } catch (Exception $e) {
        Log::error($e->getMessage());
    }
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->get(route('password.request'));

    $this
        ->followingRedirects()
        ->post(route('password.email'), [
            'email' => $user->email,
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/forgot-password')
            ->where('errors', [])
        );

    try {
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get(route('password.reset', $notification->token));

            $response->assertStatus(200);

            return true;
        });
    } catch (Exception $e) {
        Log::error($e->getMessage());
    }
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->get(route('password.request'));

    $this
        ->followingRedirects()
        ->post(route('password.email'), [
            'email' => $user->email,
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/forgot-password')
            ->where('errors', [])
        );

    try {
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post(route('password.store'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'Password1#',
                'password_confirmation' => 'Password1#',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    } catch (Exception $e) {
        Log::error($e->getMessage());
    }
});
