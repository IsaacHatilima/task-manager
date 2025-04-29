<?php

use App\Models\Todo;
use App\Models\TodoInvite;
use App\Models\User;

test('invited user can accept a todo invite', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $owner->id]);

    $token = Str::random(32);

    $owner->todoAccesses()->create([
        'todo_id' => $todo->id,
    ]);

    // Create the invited user account
    TodoInvite::create([
        'todo_id' => $todo->id,
        'token' => $token,
        'email' => $invitedUser->email,
        'expires_at' => now()->addMinutes(60),
    ]);

    // Simulate the invited user logs in
    $this->actingAs($invitedUser);

    // Call the invite acceptance route
    $this
        ->get(route('invite-accept', ['todo' => $todo->id, 'token' => $token]))
        ->assertRedirect(route('todos.show', $todo->id));

    // Assert the invite was accepted assertDatabaseHas
    $this->assertDatabaseMissing('todo_accesses', [
        'todo_id' => $todo->id,
        'email' => $invitedUser->email,
    ]);
});
