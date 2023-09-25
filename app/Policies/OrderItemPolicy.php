<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderItemPolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\Models\User $user
     * @param \App\Models\OrderItem $orderItem
     * @return bool
     */
    public function create()
    {
        return true;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\OrderItem $orderItem
     * @return bool
     */
    public function update()
    {
        return false;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\OrderItem $orderItem
     * @return bool
     */
    public function delete()
    {
        return true;
    }

}
