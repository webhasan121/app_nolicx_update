<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/confirm-password');

    $response
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page->component('Auth/ConfirmPassword'));
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertNotNull(session('auth.password_confirmed_at'));
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->from('/confirm-password')->post('/confirm-password', [
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/confirm-password');
    $response->assertSessionHasErrors('password');
});
