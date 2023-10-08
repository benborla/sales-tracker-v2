<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\UserInformation;

class UserInformationPolicy
{
    use HandlesAuthorization;

    public function create()
    {
        return i('can create', UserInformation::class);
    }

    public function update()
    {
        return i('can update', UserInformation::class);
    }

    public function delete()
    {
        return i('can delete', UserInformation::class);
    }
}
