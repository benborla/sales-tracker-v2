<?php

declare(strict_types=1);

namespace App\Permissions;

class GetPermissions
{
    public static function all(): array
    {
        return array_merge(
            \App\Permissions\Admin::valueArray(),
            \App\Permissions\User::valueArray(),
            \App\Permissions\GroupTeam::valueArray(),
            \App\Permissions\Role::valueArray(),
            \App\Permissions\Store::valueArray(),
            \App\Permissions\Product::valueArray(),
            \App\Permissions\Order::valueArray(),
            \App\Permissions\ProductImage::valueArray(),
        );
    }

}
