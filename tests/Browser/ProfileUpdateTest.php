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
        ->fill('email', 'test@example.com')
        ->click('Save')
        ->assertPathIs(route('profile.edit', absolute: false))
        ->assertNoJavascriptErrors();

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('profile.edit'));

    $page->fill('name', 'Test User')
        ->fill('email', $user->email)
        ->click('Save')
        ->assertPathIs(route('profile.edit', absolute: false))
        ->assertNoJavascriptErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('profile.edit'));

    $page->click('[data-test="delete-user-button"]')
        ->fill('password', 'password')
        ->click('[data-test="confirm-delete-user-button"]')
        ->assertPathIs(route('home', absolute: false))
        ->assertNoJavascriptErrors();

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('profile.edit'));

    $page->click('[data-test="delete-user-button"]')
        ->fill('password', 'wrong-password')
        ->click('[data-test="confirm-delete-user-button"]')
        ->assertPathIs(route('profile.edit', absolute: false))
        ->assertSee('password')
        ->assertNoJavascriptErrors();

    expect($user->fresh())->not->toBeNull();
});
