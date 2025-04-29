<?php

use App\Models\Profile;
use App\Models\Todo;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('example', function () {
    $user = User::factory()
        ->has(Profile::factory([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]))
        ->create([
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

    $todo = Todo::factory()->create([
        'user_id' => $user->id,
    ]);

    $userToDelete = User::factory()
        ->has(Profile::factory([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]))
        ->create([
            'password' => Hash::make('Password1#'),
        ]);

    $this->get(route('todos.collaborators.index', $todo->id));

    $this
        ->followingRedirects()
        ->delete(route('todos.collaborators.destroy', ['todo' => $todo, 'user' => $userToDelete]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/collaborators')
            ->where('errors', [])
        );
});
