<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can request magic link for authentication', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
    ]);

    $response->assertStatus(302); // Redirect after magic link sent
    $response->assertSessionHas('status', 'We have emailed you a magic link!');

    // Check that a magic link was created
    $this->assertDatabaseCount('magic_links', 1);
});

test('users can request magic link even with new email', function () {
    $response = $this->post('/login', [
        'email' => 'newuser@example.com',
    ]);

    // Magic link system creates users on the fly, so this should work
    $response->assertStatus(302);
    $response->assertSessionHas('status', 'We have emailed you a magic link!');
    $this->assertDatabaseCount('magic_links', 1);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
