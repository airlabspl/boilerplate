<?php

use Laravel\Fortify\Features;

test('two factor challenge redirects to login when not authenticated', function () {
    $page = visit(route('two-factor.login'));

    $page->assertPathIs(route('login', absolute: false))
        ->assertNoJavascriptErrors();
});

test('two factor challenge can be rendered', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $this->markTestSkipped('2FA integration with passwordless login needs to be implemented.');
});
