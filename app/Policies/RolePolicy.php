<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function create()
    {
        return i('can create', \App\Models\Role::class);
    }

    public function update()
    {
        return i('can update', \App\Models\Role::class);
    }

    public function delete()
    {
        return i('can delete', \App\Models\Role::class);
    }

    public function view()
    {
        return i('can view', \App\Models\Role::class);
    }
}
