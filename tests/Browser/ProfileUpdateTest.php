<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('profile.edit'));

    $page->assertSee('Profile Information')
        ->assertNoJavascriptErrors();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('profile.edit'));

    $page->fill('name', 'Test User')
        ->click('Save')
        ->assertPathIs(route('profile.edit', absolute: false))
        ->assertNoJavascriptErrors();

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe($user->email);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('profile.edit'));

    $page->click('[data-test="delete-user-button"]')
        ->click('[data-test="confirm-delete-user-button"]')
        ->assertPathIs(route('home', absolute: false))
        ->assertNoJavascriptErrors();

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});
