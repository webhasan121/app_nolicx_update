<?php

namespace Tests\Feature\Auth;

use Inertia\Testing\AssertableInertia as Assert;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Register'));
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '01700000000',
        'country_id' => 18,
        'state_id' => 1,
        'city_id' => 1,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});
