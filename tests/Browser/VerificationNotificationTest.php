<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('sends verification notification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user);

    $page = visit(route('verification.notice'));

    $page->click('Resend verification email')
        ->assertPathIs(route('verification.notice', absolute: false))
        ->assertNoJavascriptErrors();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('does not send verification notification if email is verified', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    $page = visit(route('verification.notice'));

    $page->assertPathIs(route('dashboard', absolute: false))
        ->assertNoJavascriptErrors();

    Notification::assertNothingSent();
});
