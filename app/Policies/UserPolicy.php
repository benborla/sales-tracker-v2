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
        return i('can view', User::class);
    }

    public function view(User $user)
    {
        return i('can view', User::class);
    }

    public function create(User $user)
    {
        return i('can create', User::class);
    }

    public function update(User $user)
    {
        return i('can update', User::class);
    }

    public function delete(User $user)
    {
        return i('can delete', User::class);
    }

    public function addUser(User $user)
    {
        return i('can assign', User::class);
    }

    public function restore(User $user)
    {
        return i('can create', User::class);
    }

    public function forceDelete(User $user)
    {
        return i('can delete', User::class);
    }

    public function attachRole()
    {
        return i('can assign', \App\Models\Role::class);
    }

    public function attachAnyRole()
    {
        return i('can assign', \App\Models\Role::class);
    }

    public function addUserStore()
    {
        return i('can assign', \App\Models\Store::class);
    }

    public function addGroupTeamMember()
    {
        return i('can assign', \App\Models\GroupTeam::class);
    }
}
