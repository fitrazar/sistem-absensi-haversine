<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = new User([
        'id' => 1,
        'username' => 'dummyuser',
        'password' => bcrypt('password'),
    ]);
    $user->save();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users cannot authenticate with invalid password', function () {
    $user = new User([
        'id' => 1,
        'username' => 'dummyuser',
        'password' => bcrypt('password'),
    ]);
    $user->save();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = new User([
        'id' => 1,
        'username' => 'dummyuser',
        'password' => bcrypt('password'),
    ]);
    $user->save();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
