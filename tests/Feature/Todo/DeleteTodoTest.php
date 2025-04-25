<?php

use App\Models\Todo;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('user can delete todo', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $todo = Todo::factory()->create([
        'user_id' => $user->id,
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

    $this
        ->followingRedirects()
        ->get(route('todos.show', $todo->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/todo-details')
            ->where('todo.id', $todo->id)
        );

    $this->actingAs($user);

    $this->post(route('password.confirmation'), [
        'password' => 'Password1#',
    ])->assertRedirect();

    $this->delete(route('todos.destroy', $todo->id))
        ->assertRedirect(route('todos.index'));

    $this->assertDatabaseMissing('todos', [
        'id' => $todo->id,
    ]);
});

test('user cannot delete todo with wrong password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password1#'),
    ]);

    $todo = Todo::factory()->create([
        'user_id' => $user->id,
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

    $this
        ->followingRedirects()
        ->get(route('todos.show', $todo->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/todo-details')
            ->where('todo.id', $todo->id)
        );

    $this
        ->followingRedirects()
        ->post(route('password.confirmation'), [
            'password' => 'Password12#',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/todo-details')
            ->where('errors.password', 'The password is incorrect.')
        );

});
