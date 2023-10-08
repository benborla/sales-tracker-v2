<?php

namespace App\Policies;

use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return i('can view', Order::class);
    }

    public function view()
    {
        return i('can view', Order::class);
    }

    public function create()
    {
        return i('can create', Order::class);
    }

    public function update()
    {
        return i('can update', Order::class);
    }

    public function delete()
    {
        return i('can delete', Order::class);
    }
}
