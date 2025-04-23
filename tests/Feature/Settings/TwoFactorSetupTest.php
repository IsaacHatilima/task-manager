<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use PragmaRX\Google2FA\Google2FA;

test('two-factor auth page is displayed', function () {
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
        ->get(route('two-factor-authentication.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->where('auth.user.email', $user->email)
        );

});

test('user can activate 2fa', function () {
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

    $this->get(route('two-factor-authentication.edit'));

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/confirm-password')
        );

    $this
        ->followingRedirects()
        ->post('/user/confirm-password', [
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
        );

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->whereNot('auth.user.two_factor_secret', null)
            ->whereNot('auth.user.two_factor_recovery_codes', null)
            ->where('auth.user.two_factor_confirmed_at', null)
        );

});

test('user cannot activate 2fa with wrong password', function () {
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

    $this->get(route('two-factor-authentication.edit'));

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/confirm-password')
        );

    $this
        ->followingRedirects()
        ->post('/user/confirm-password', [
            'password' => 'Password12#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/confirm-password')
            ->where('errors.password', 'The provided password was incorrect.')
        );

});

test('user can deactivate 2fa', function () {
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

    $this->get(route('two-factor-authentication.edit'));

    $this
        ->followingRedirects()
        ->delete('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/confirm-password')
        );

    $this
        ->followingRedirects()
        ->post('/user/confirm-password', [
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
        );

    $this
        ->followingRedirects()
        ->delete('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->where('auth.user.two_factor_secret', null)
            ->where('auth.user.two_factor_recovery_codes', null)
            ->where('auth.user.two_factor_confirmed_at', null)
        );

});

test('user can activate and confirm 2fa', function () {
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

    $this->get(route('two-factor-authentication.edit'));

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/confirm-password')
        );

    $this
        ->followingRedirects()
        ->post('/user/confirm-password', [
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
        );

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->whereNot('auth.user.two_factor_secret', null)
            ->whereNot('auth.user.two_factor_recovery_codes', null)
            ->where('auth.user.two_factor_confirmed_at', null)
        );

    $user->refresh();

    $decryptedSecret = Crypt::decrypt($user->two_factor_secret);

    $google2fa = new Google2FA;
    $otp = $google2fa->getCurrentOtp($decryptedSecret);

    $this
        ->followingRedirects()
        ->post('/user/confirmed-two-factor-authentication', [
            'code' => $otp,
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->whereNot('auth.user.two_factor_secret', null)
            ->whereNot('auth.user.two_factor_recovery_codes', null)
            ->whereNot('auth.user.two_factor_confirmed_at', null)
        );

});

test('user cannot activate and confirm 2fa with wrong code', function () {
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

    $this->get(route('two-factor-authentication.edit'));

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/confirm-password')
        );

    $this
        ->followingRedirects()
        ->post('/user/confirm-password', [
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
        );

    $this
        ->followingRedirects()
        ->post('/user/two-factor-authentication')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->whereNot('auth.user.two_factor_secret', null)
            ->whereNot('auth.user.two_factor_recovery_codes', null)
            ->where('auth.user.two_factor_confirmed_at', null)
        );

    $user->refresh();

    $this
        ->followingRedirects()
        ->post('/user/confirmed-two-factor-authentication', [
            'code' => '123456',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor-setup')
            ->whereNot('auth.user.two_factor_secret', null)
            ->whereNot('auth.user.two_factor_recovery_codes', null)
            ->where('auth.user.two_factor_confirmed_at', null)
            ->has('errors')
        );
});
