<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password update page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('user-password.edit'));

    $page->assertSee('Update password')
        ->assertNoJavascriptErrors();
});

test('password can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('user-password.edit'));

    $page->fill('current_password', 'password')
        ->fill('password', 'new-password')
        ->fill('password_confirmation', 'new-password')
        ->click('[data-test="update-password-button"]')
        ->assertPathIs(route('user-password.edit', absolute: false))
        ->assertNoJavascriptErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit(route('user-password.edit'));

    $page->fill('current_password', 'wrong-password')
        ->fill('password', 'new-password')
        ->fill('password_confirmation', 'new-password')
        ->click('[data-test="update-password-button"]')
        ->assertPathIs(route('user-password.edit', absolute: false))
        ->assertSee('password is incorrect')
        ->assertNoJavascriptErrors();
});
