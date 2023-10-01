<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum Store: string
{
    use EnumEnhancements;

    case STORE_CAN_VIEW = 'STORE_CAN_VIEW';
    case STORE_CAN_CREATE = 'STORE_CAN_CREATE';
    case STORE_CAN_UPDATE = 'STORE_CAN_UPDATE';
    case STORE_CAN_DELETE= 'STORE_CAN_DELETE';
    case STORE_CAN_ASSIGN = 'STORE_CAN_ASSIGN';
}
