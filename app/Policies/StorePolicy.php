<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    public function create()
    {
        return i('can create', \App\Models\Store::class);
    }

    public function update()
    {
        return i('can update', \App\Models\Store::class);
    }

    public function delete()
    {
        return i('can delete', \App\Models\Store::class);
    }

    public function view()
    {
        return i('can view', \App\Models\Store::class);
    }

    public function viewAny()
    {
        return i('can view', \App\Models\Store::class);
    }
}
