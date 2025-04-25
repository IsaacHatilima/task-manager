<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('user can update todo', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->get(route('login'));

    $this
        ->followingRedirects()
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('auth.user.email', $user->email)
        );

    $this->get(route('todos.index'));

    $this
        ->followingRedirects()
        ->post(route('todos.store'), [
            'title' => 'Test Todo',
            'description' => 'Test Todo Description',
            'status' => 'pending',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/index')
            ->where('errors', [])
        );

    $todo = $user->todos()->latest()->first();

    $this
        ->followingRedirects()
        ->put(route('todos.update', $todo), [
            'title' => 'New Todo Title',
            'description' => 'Test Todo Description',
            'status' => 'completed',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/index')
            ->where('errors', [])
        );

    $this->assertDatabaseHas('todos', [
        'id' => $todo->id,
        'title' => 'New Todo Title',
        'description' => 'Test Todo Description',
        'status' => 'completed',
    ]);
});

test('user cannot update todo with missing fields', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $this->get(route('login'));

    $this
        ->followingRedirects()
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password1#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('auth.user.email', $user->email)
        );

    $this->get(route('todos.index'));

    $this
        ->followingRedirects()
        ->post(route('todos.store'), [
            'title' => 'Test Todo',
            'description' => 'Test Todo Description',
            'status' => 'pending',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/index')
            ->where('errors', [])
        );

    $todo = $user->todos()->latest()->first();

    $this
        ->followingRedirects()
        ->put(route('todos.update', $todo), [
            'title' => 'New Todo Title',
            'status' => 'completed',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/index')
            ->where('errors.description', 'The description field is required.')
        );
});
