<?php

use App\Models\Todo;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('user can update task', function () {
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

    $todo = Todo::factory()->create([
        'user_id' => $user->id,
    ]);

    $todo->accesses()->create([
        'user_id' => $user->id,
    ]);

    $task = $todo->tasks()->create([
        'user_id' => $user->id,
        'title' => 'Test Task',
        'description' => 'Test Task Description',
        'status' => 'pending',
    ]);

    $this->get(route('todos.show', $todo->id));

    $this
        ->followingRedirects()
        ->put(route('todo.task.update', ['todo' => $todo->id, 'task' => $task->id]), [
            'title' => 'New Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/todo-details')
            ->where('errors', [])
        );

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Test Task',
        'description' => 'Test Task Description',
        'status' => 'pending',
    ]);
});

test('user cannot update task with missing data', function () {
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

    $todo = Todo::factory()->create([
        'user_id' => $user->id,
    ]);

    $todo->accesses()->create([
        'user_id' => $user->id,
    ]);

    $task = $todo->tasks()->create([
        'user_id' => $user->id,
        'title' => 'Test Task',
        'description' => 'Test Task Description',
        'status' => 'pending',
    ]);

    $this->get(route('todos.show', $todo->id));

    $this
        ->followingRedirects()
        ->put(route('todo.task.update', ['todo' => $todo->id, 'task' => $task->id]), [
            'title' => '',
            'description' => 'Test Task Description',
            'status' => 'pending',
        ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('todo/todo-details')
            ->where('errors.title', 'The title field is required.')
        );
});
