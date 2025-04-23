<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('profile page is displayed', function () {
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

    $this
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/profile')
            ->where('auth.user.email', $user->email)
        );

});

test('profile information can be updated', function () {
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

    $this->get(route('profile.edit'));

    $this
        ->followingRedirects()
        ->patch(route('profile.update'), [
            'email' => 'new.email@mail.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/verify-email')
            ->where('errors', [])
        );

    $user->refresh();

    expect($user->profile->first_name)->toBe('John')
        ->and($user->profile->last_name)->toBe('Doe')
        ->and($user->profile->gender)->toBe('male')
        ->and($user->email)->toBe($user->email)
        ->and($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
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

    $this->get(route('profile.edit'));

    $this
        ->followingRedirects()
        ->patch(route('profile.update'), [
            'email' => $user->email,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/profile')
            ->where('errors', [])
        );

    $user->refresh();

    expect($user->profile->first_name)->toBe('John')
        ->and($user->profile->last_name)->toBe('Doe')
        ->and($user->profile->gender)->toBe('male')
        ->and($user->email)->toBe($user->email)
        ->and($user->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
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

    $this->get(route('profile.edit'));

    $this
        ->followingRedirects()
        ->delete(route('profile.destroy'), [
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/login')
            ->where('errors', [])
        );

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
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

    $this->get(route('profile.edit'));

    $this
        ->followingRedirects()
        ->delete(route('profile.destroy'), [
            'password' => 'Password1Wrong#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/profile')
            ->where('errors.password', 'The password is incorrect.')
        );

    expect($user->fresh())->not->toBeNull();
});
