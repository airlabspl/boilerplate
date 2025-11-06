<?php

use App\Models\Otp;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

test('user can request OTP with email', function () {
    $email = 'test@example.com';

    $page = visit(route('login'));

    $page->assertSee('Log in to your account')
        ->fill('email', $email)
        ->click('[data-test="send-code-button"]')
        ->assertSee('Enter the code sent to your email')
        ->assertNoJavascriptErrors();
});

test('new user is created when requesting OTP with new email', function () {
    $email = 'newuser@example.com';

    expect(User::where('email', $email)->exists())->toBeFalse();

    $page = visit(route('login'));

    $page->fill('email', $email)
        ->click('[data-test="send-code-button"]');

    expect(User::where('email', $email)->exists())->toBeTrue();
});

test('existing user can request OTP', function () {
    $user = User::factory()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->click('[data-test="send-code-button"]')
        ->assertSee('Enter the code sent to your email')
        ->assertNoJavascriptErrors();
});

test('user can login with valid OTP', function () {
    $user = User::factory()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->click('[data-test="send-code-button"]')
        ->assertSee('Enter the code sent to your email');

    Notification::assertSentTo($user, OtpNotification::class, function ($notification) use ($user, $page) {
        $otp = Otp::where('user_id', $user->id)
            ->where('code', $notification->code)
            ->first();

        expect($otp)->not->toBeNull();

        $page->fill('otp', $notification->code)
            ->click('[data-test="verify-code-button"]')
            ->assertPathIs(route('dashboard', absolute: false))
            ->assertNoJavascriptErrors();

        $this->assertAuthenticated();
        expect(auth()->user()->id)->toBe($user->id);

        return true;
    });
});

test('user cannot login with invalid OTP', function () {
    $user = User::factory()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->click('[data-test="send-code-button"]')
        ->fill('otp', '000000')
        ->click('[data-test="verify-code-button"]')
        ->assertSee('Invalid or expired code')
        ->assertNoJavascriptErrors();

    $this->assertGuest();
});

test('OTP expires after 5 minutes', function () {
    $user = User::factory()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->click('[data-test="send-code-button"]')
        ->assertSee('Enter the code sent to your email');

    Notification::assertSentTo($user, OtpNotification::class, function ($notification) use ($user) {
        $otp = Otp::where('user_id', $user->id)
            ->where('code', $notification->code)
            ->first();

        $this->travel(6)->minutes();

        $page = visit(route('login'));

        $page->fill('email', $user->email)
            ->click('[data-test="send-code-button"]')
            ->assertSee('Enter the code sent to your email')
            ->fill('otp', $notification->code)
            ->click('[data-test="verify-code-button"]')
            ->assertSee('Invalid or expired code')
            ->assertNoJavascriptErrors();

        $this->assertGuest();

        return true;
    });
});

test('countdown timer is displayed after OTP is sent', function () {
    $user = User::factory()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->click('[data-test="send-code-button"]')
        ->assertSee('5:00')
        ->assertNoJavascriptErrors();
});
