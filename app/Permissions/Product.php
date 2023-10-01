<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum Product: string
{
    use EnumEnhancements;

    case PRODUCT_CAN_VIEW = 'PRODUCT_CAN_VIEW';
    case PRODUCT_CAN_CREATE = 'PRODUCT_CAN_CREATE';
    case PRODUCT_CAN_UPDATE = 'PRODUCT_CAN_UPDATE';
    case PRODUCT_CAN_DELETE= 'PRODUCT_CAN_DELETE';
}
