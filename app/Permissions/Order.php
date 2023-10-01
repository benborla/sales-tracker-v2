<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum Order: string
{
    use EnumEnhancements;

    case ORDER_CAN_VIEW = 'ORDER_CAN_VIEW';
    case ORDER_CAN_CREATE = 'ORDER_CAN_CREATE';
    case ORDER_CAN_UPDATE = 'ORDER_CAN_UPDATE';
    case ORDER_CAN_DELETE = 'ORDER_CAN_DELETE';
    case ORDER_CAN_APPROVE = 'ORDER_CAN_APPROVE';
    case ORDER_CAN_VIEW_ALL_ORDERS = 'ORDER_CAN_VIEW_ALL_ORDERS';
}
