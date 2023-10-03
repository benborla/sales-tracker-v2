<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class GroupTeamPolicy
{
    use HandlesAuthorization;

    public function create()
    {
        return i('can create', \App\Models\GroupTeam::class);
    }

    public function update()
    {
        return i('can update', \App\Models\GroupTeam::class);
    }

    public function delete()
    {
        return i('can delete', \App\Models\GroupTeam::class);
    }

    public function view()
    {
        return i('can view', \App\Models\GroupTeam::class);
    }

    public function viewAny()
    {
        return i('can view', \App\Models\GroupTeam::class);
    }

    public function attachUser()
    {
        return i('can assign', \App\Models\GroupTeam::class);
    }
}
