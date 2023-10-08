<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return i('can view', User::class);
    }

    public function view()
    {
        return i('can view', User::class);
    }

    public function create()
    {
        return i('can create', User::class);
    }

    public function update()
    {
        return i('can update', User::class);
    }

    public function delete()
    {
        return i('can delete', User::class);
    }

    public function addUser()
    {
        return i('can assign', User::class);
    }

    public function restore()
    {
        return i('can create', User::class);
    }

    public function forceDelete()
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
