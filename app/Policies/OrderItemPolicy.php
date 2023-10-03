<?php

namespace App\Policies;

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
        return true;
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
