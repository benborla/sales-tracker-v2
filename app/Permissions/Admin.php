<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum Admin: string
{
    use EnumEnhancements;

    case ADMIN_CAN_ACCESS_SALES_TRACKER = 'ADMIN_CAN_ACCESS_SALES_TRACKER';
    case ADMIN_ALL_ACCESS = 'ADMIN_ALL_ACCESS';
}
