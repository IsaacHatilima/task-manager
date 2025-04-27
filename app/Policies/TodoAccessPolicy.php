<?php

namespace App\Policies;

use App\Models\TodoAccess;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TodoAccessPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, TodoAccess $todoAccess): bool {}

    public function create(User $user): bool {}

    public function update(User $user, TodoAccess $todoAccess): bool {}

    public function delete(User $user, TodoAccess $todoAccess): bool {}

    public function restore(User $user, TodoAccess $todoAccess): bool {}

    public function forceDelete(User $user, TodoAccess $todoAccess): bool {}
}
