<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('User/Profile/Edit'));
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->from('/profile')->patch(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHasNoErrors();
    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->from('/profile')->patch(route('profile.update'), [
        'name' => 'Test User',
        'email' => $user->email,
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHasNoErrors();
    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->delete(route('profile.destroy'), [
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertGuest();
    $this->assertNotNull($user->fresh()->deleted_at);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->from('/profile')->delete(route('profile.destroy'), [
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHasErrors('password', null, 'userDeletion');
    $this->assertNotNull($user->fresh());
});
