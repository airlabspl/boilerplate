<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $page = visit(route('login'));

    $page->assertSee('Log in to your account')
        ->assertNoJavascriptErrors();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('dashboard'));

    $page->click('[data-test="sidebar-menu-button"]')
        ->click('[data-test="logout-button"]')
        ->assertPathIs(route('home', absolute: false))
        ->assertNoJavascriptErrors();

    $this->assertGuest();
});
