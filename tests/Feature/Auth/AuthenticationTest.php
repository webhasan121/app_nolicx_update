<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('user.dash'));
    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('navigation menu can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));
});

test('users can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post(route('logout.perform'));

    $response->assertRedirect('/');
    $this->assertGuest();
});
