<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupTeamMemberPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return i('can view', \App\Models\GroupTeam::class);
    }

    public function create()
    {
        return i('can assign', \App\Models\GroupTeam::class);
    }

    public function update()
    {
        return i('can update', \App\Models\GroupTeam::class);
    }

    public function delete()
    {
        return i('can delete', \App\Models\GroupTeam::class);
    }
}
