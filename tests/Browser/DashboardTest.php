<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $page = visit(route('dashboard'));

    $page->assertPathIs(route('login', absolute: false));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('dashboard'));

    $page->assertSee('Dashboard')
        ->assertNoJavascriptErrors();
});
