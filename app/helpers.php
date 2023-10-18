<?php

use App\Models\Store;
use App\Models\Order;
use App\Models\User;
use App\Models\UserInformation;
use App\Models\Product;
use App\Models\OrderItem;

if (! function_exists('get_main_store_domain')) {
    function get_main_store_domain()
    {
        return preg_replace('(^https?://)', '', config('app.url'));
    }

}

if (! function_exists('is_me')) {
    /**
     * Compares the provided $userId to the authenticated userId
     *
     * @param int $userId
     *
     * @return bool
     */
    function is_me(int $userId): bool
    {
        return (int) auth()->user()->id === $userId;
    }
}

if (!function_exists('get_user_type')) {
    /**
     * Identifies the role of the logged-in user
     *
     * @return string
     */
    function get_user_type()
    {
        $info = auth()->user()->information ?? null;

        if (is_null($info)) {
            /** @INFO: Identify user based on role **/
            $role = auth()->user()->roles->first()->slug;

            if ($role === UserInformation::USER_TYPE_CUSTOMER) {
                return UserInformation::USER_TYPE_CUSTOMER;
            }

            return UserInformation::USER_TYPE_STAFF;
        }

        return $info->type;
    }
}
if (!function_exists('is_staff')) {
    /**
     * Return true if the user type of the logged-in user is set to "staff"
     *
     * @return bool
     */
    function is_staff()
    {
        return get_user_type() === UserInformation::USER_TYPE_STAFF;
    }
}

if (!function_exists('is_customer')) {
    /**
     * Return true if the user type of the logged-in user is set to "customer"
     *
     * @return bool
     */
    function is_customer()
    {
        return get_user_type() === UserInformation::USER_TYPE_CUSTOMER;
    }
}


if (!function_exists('can')) {
    /**
     * Return true if the user has the permission to access the provided permission string
     * with the provided model
     *
     * @param string $model
     * @param string $permission
     * @param \App\Models\User $user = null
     *
     * @return bool
     */
    function can(string $model, string $permission, ?User $user = null): bool
    {
        if (!class_exists($model)) {
            return false;
        }

        $model = last(explode('\\', $model));
        $permission = preg_replace('/\s+/', '_', trim($permission));
        $permission = strtoupper("{$model}_$permission");

        return $user->hasRoleWithPermission($permission);
    }
}

if (!function_exists('i')) {
    /**
     * Simplified version of "can" function
     * with the provided model
     *
     * @param string $permission
     * @param string $model
     *
     * @return bool
     */
    function i(string $permission, string $model): bool
    {
        // @INFO: Skip if the user has an admin access
        if (admin_all_access()) {
            return true;
        }

        return can($model, $permission, auth()->user());
    }
}

if (!function_exists('admin_all_access')) {
    function admin_all_access(): bool
    {
        return auth()->user()->hasRoleWithPermission(\App\Permissions\Admin::ADMIN_ALL_ACCESS->value);
    }
}


if (!function_exists('is_main_store')) {
    function is_main_store()
    {
        if (request()->request->get('is_main_store')) {
            return true;
        }

        return false;
    }
}

if (!function_exists('get_store_id')) {
    function get_store_id()
    {
        $store = request()->request->get('store') ?? null;

        if ($store instanceof Store) {
            return $store->id;
        }
    }
}

if (!function_exists('store')) {
    function store(): null|\App\Models\Store
    {
        $store = request()->request->get('store') ?? null;

        return $store;
    }
}

if (!function_exists('update_total_payable')) {

    function get_total_sales(
        array $orderItems,
        float $shippingFee,
        float $taxFee,
        float $intermediaryFees,
        string $priceBasedOn
    ) {
        $totalProductPayable = get_total_payable($orderItems, $priceBasedOn);

        return ($totalProductPayable + $shippingFee) - ($taxFee + $intermediaryFees);
    }

    function update_total_payable(Order $order)
    {
        $orderItems = $order->orderItems->toArray();
        $priceBasedOn = $order->price_based_on;

        $order->total_sales = get_total_sales(
            $orderItems,
            $order->shipping_fee,
            $order->tax_fee,
            $order->intermediary_fees,
            $priceBasedOn
        );

        $order->saveQuietly();
    }

    function get_total_payable(array $orderItems, string $priceBasedOn)
    {
        if (!count($orderItems)) {
            return 0;
        }

        $orderItems = collect($orderItems);
        $productIds = array_values($orderItems->pluck('product_id')->all());

        return Product::whereIn('id', $productIds)
            ->get()
            ->map(function ($product, $key) use ($orderItems, $priceBasedOn) {
                $quantity = $orderItems->where('product_id', $product->id)->first()['quantity'] ?? 1;
                $product->ordered_quantity = $quantity;
                $product->total_quantity_price = $product->$priceBasedOn * $quantity;

                return $product;
        })->sum('total_quantity_price') ?:0;
    }
}

if (!function_exists('deduct_from_product_inventory')) {
    function deduct_from_product_inventory(OrderItem $orderItem)
    {
        $product = $orderItem->product;
        $product->total_inventory_remaining -= $orderItem->quantity;
        $product->save();
    }
}

if (!function_exists('add_to_product_inventory')) {
    function add_to_product_inventory(OrderItem $orderItem)
    {
        $product = $orderItem->product;
        $product->total_inventory_remaining += $orderItem->quantity;
        $product->save();
    }
}
