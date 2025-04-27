<?php

use App\Models\User;
use Carbon\Carbon;
use PragmaRX\Google2FA\Google2FA;

test('2FA users can authenticate with valid code', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->actingAs($user)->post('/user/confirm-password', [
        'password' => 'Password1#',
    ]);

    $this->actingAs($user)->post('/user/two-factor-authentication');

    $decryptedSecret = Crypt::decrypt($user->two_factor_secret);
    $google2fa = new Google2FA;
    $otp = $google2fa->getCurrentOtp($decryptedSecret);

    $this->actingAs($user)->post('/user/confirmed-two-factor-authentication', [
        'code' => $otp,
    ]);

    $this->actingAs($user)->post('/logout');
    $this->assertGuest();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'Password1#',
    ])->assertRedirect(route('two-factor.login'));

    Carbon::setTestNow(Carbon::now()->addSeconds(60)); // To expire the confirmation code on line 20

    $freshOtp = $google2fa->getCurrentOtp($decryptedSecret);

    $this->post('/two-factor-challenge', [
        'code' => $freshOtp,
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('2FA users cannot authenticate with invalid code', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->actingAs($user)->post('/user/confirm-password', [
        'password' => 'Password1#',
    ]);

    $this->actingAs($user)->post('/user/two-factor-authentication');

    $decryptedSecret = Crypt::decrypt($user->two_factor_secret);
    $google2fa = new Google2FA;
    $otp = $google2fa->getCurrentOtp($decryptedSecret);

    $this->actingAs($user)->post('/user/confirmed-two-factor-authentication', [
        'code' => $otp,
    ]);

    $this->actingAs($user)->post('/logout');
    $this->assertGuest();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'Password1#',
    ])->assertRedirect(route('two-factor.login'));

    $this->post('/two-factor-challenge', [
        'code' => '123456',
    ])->assertRedirect(route('two-factor.login'));

    $this->assertGuest();
});
