<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum User: string
{
    use EnumEnhancements;

    case USER_CAN_VIEW = 'USER_CAN_VIEW';
    case USER_CAN_CREATE = 'USER_CAN_CREATE';
    case USER_CAN_UPDATE = 'USER_CAN_UPDATE';
    case USER_CAN_DELETE = 'USER_CAN_DELETE';
    case USER_CAN_VIEW_ALL_IN_STORE = 'USER_CAN_VIEW_ALL_IN_STORE';
}
