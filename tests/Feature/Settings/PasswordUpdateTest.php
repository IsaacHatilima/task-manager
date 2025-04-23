<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('password can be updated', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->get(route('login'));

    $this
        ->followingRedirects()
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('auth.user.email', $user->email)
        );

    $this->get(route('password.edit'));

    $this
        ->followingRedirects()
        ->put(route('password.update'), [
            'current_password' => 'Password1#',
            'password' => 'Password12#',
            'password_confirmation' => 'Password12#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/password')
            ->where('errors', [])
        );

    expect(Hash::check('Password12#', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->get(route('login'));

    $this
        ->followingRedirects()
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('auth.user.email', $user->email)
        );

    $this->get(route('password.edit'));

    $this
        ->followingRedirects()
        ->put(route('password.update'), [
            'current_password' => 'Wrong-Password',
            'password' => 'Password12#',
            'password_confirmation' => 'Password12#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/password')
            ->where('errors.current_password', 'The password is incorrect.')
        );
});

test('correct password format must be provided to update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->get(route('login'));

    $this
        ->followingRedirects()
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('auth.user.email', $user->email)
        );

    $this->get(route('password.edit'));

    $this
        ->followingRedirects()
        ->put(route('password.update'), [
            'current_password' => 'Password1#',
            'password' => 'Password',
            'password_confirmation' => 'Password',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/password')
            ->has('errors.password')
        );
});
