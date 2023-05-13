<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class UserPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        return Gate::any(['manageUsers'], $user);
    }

    public function view(User $user)
    {
        return Gate::any(['manageUsers'], $user);
    }

    public function create(User $user)
    {
        return $user->can('manageUsers');
    }

    public function update(User $user)
    {
        return $user->can('manageUsers');
    }

    public function delete(User $user)
    {
        return $user->can('manageUsers');
    }

    public function addUser(User $user)
    {
        return $user->can('manageUsers');
    }

    public function restore(User $user)
    {
        return $user->can('manageUsers');
    }

    public function forceDelete(User $user)
    {
        return $user->can('manageUsers');
    }
}
