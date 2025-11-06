<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('two factor settings page can be rendered', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $user = User::factory()->withoutTwoFactor()->create();

    $this->actingAs($user);

    $page = visit(route('two-factor.show'));

    $page->assertSee('Two-Factor Authentication')
        ->assertNoJavascriptErrors();
});

test('two factor settings page returns forbidden response when two factor is disabled', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    config(['fortify.features' => []]);

    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('two-factor.show'));

    $page->assertSee('403')
        ->assertNoJavascriptErrors();
});
