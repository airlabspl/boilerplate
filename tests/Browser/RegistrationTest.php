<?php

test('registration screen can be rendered', function () {
    $page = visit(route('register'));

    $page->assertSee('Create an account')
        ->assertNoJavascriptErrors();
});

test('new users can register', function () {
    $page = visit(route('register'));

    $page->fill('name', 'Test User')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('Create account')
        ->assertPathIs(route('dashboard', absolute: false))
        ->assertNoJavascriptErrors();

    $this->assertAuthenticated();
});
