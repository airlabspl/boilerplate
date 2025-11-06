<?php

use App\Models\User;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('password.confirm'));

    $page->assertSee('Confirm Password')
        ->assertNoJavascriptErrors();
});

test('password confirmation requires authentication', function () {
    $page = visit(route('password.confirm'));

    $page->assertPathIs(route('login', absolute: false))
        ->assertNoJavascriptErrors();
});
