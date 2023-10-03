<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserStorePolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return i('can view', \App\Models\Store::class);
    }

    public function create()
    {
        return i('can assign', \App\Models\Store::class);
    }

    public function update()
    {
        return i('can update', \App\Models\Store::class);
    }

    public function delete()
    {
        return i('can delete', \App\Models\Store::class);
    }

}
