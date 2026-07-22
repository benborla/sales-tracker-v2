<?php

namespace App\Nova\Metrics\Concerns;

use App\Models\Order;
use App\Permissions\Admin;
use Illuminate\Database\Eloquent\Builder;

trait ScopesToUserStores
{
    /**
     * Base Order query limited to the stores the requesting user may see.
     *
     * Admins (all-access or sales-tracker access) see every store, matching the
     * bypass in IsUserBelongsToStore. Everyone else is limited to the stores on
     * their UserStore pivot. Passed straight into Nova's metric aggregates.
     */
    protected function storeScopedOrders($request): Builder
    {
        $query = Order::query();
        $user = $request->user();

        // ponytail: no authenticated user -> empty set, never leak store data
        if ($user === null) {
            return $query->whereRaw('1 = 0');
        }

        if (
            $user->hasRoleWithPermission(Admin::ADMIN_ALL_ACCESS->value) ||
            $user->hasRoleWithPermission(Admin::ADMIN_CAN_ACCESS_SALES_TRACKER->value)
        ) {
            return $query;
        }

        return $query->whereIn('store_id', $user->stores->pluck('store_id'));
    }
}
