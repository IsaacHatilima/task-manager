<?php

use App\Jobs\SendInviteJob;
use App\Models\Profile;
use App\Models\Todo;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('todo collaborators page can be shown', function () {
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

    $todo = Todo::factory()->create();

    $this->get(route('todos.collaborators.index', $todo->id));
});

test('todo collaborators can be invited', function () {
    Bus::fake();

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

    $this->get(route('todos.collaborators.index', $todo->id));

    $this
        ->followingRedirects()
        ->post(route('todos.collaborators.store', $todo->id), [
            'email' => 'user@example.com',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/collaborators')
            ->where('errors', [])
        );

    Bus::assertDispatched(SendInviteJob::class);
});

test('todo collaborators cannot be invited with missing email', function () {
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

    $this->get(route('todos.collaborators.index', $todo->id));

    $this
        ->followingRedirects()
        ->post(route('todos.collaborators.store', $todo->id), [
            'email' => '',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/collaborators')
            ->where('errors.email', 'Email is required')
        );
});
