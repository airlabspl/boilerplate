<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $page = visit(route('password.request'));

    $page->assertSee('Forgot password')
        ->assertNoJavascriptErrors();
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $page = visit(route('password.request'));

    $page->fill('email', $user->email)
        ->click('Email password reset link')
        ->assertNoJavascriptErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $page = visit(route('password.request'));

    $page->fill('email', $user->email)
        ->click('Email password reset link');

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $page = visit(route('password.reset', $notification->token));

        $page->assertSee('Reset password')
            ->assertNoJavascriptErrors();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $page = visit(route('password.request'));

    $page->fill('email', $user->email)
        ->click('Email password reset link');

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $page = visit(route('password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]));

        $page->fill('password', 'password')
            ->fill('password_confirmation', 'password')
            ->click('[data-test="reset-password-button"]')
            ->assertPathIs(route('login', absolute: false))
            ->assertNoJavascriptErrors();

        return true;
    });
});

test('password cannot be reset with invalid token', function () {
    $user = User::factory()->create();

    $page = visit(route('password.reset', 'invalid-token'));

    $page->fill('password', 'newpassword123')
        ->fill('password_confirmation', 'newpassword123')
        ->click('Reset password')
        ->assertSee('email')
        ->assertNoJavascriptErrors();
});
