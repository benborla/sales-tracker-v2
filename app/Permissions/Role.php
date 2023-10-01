<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum Role: string
{
    use EnumEnhancements;

    case ROLE_CAN_VIEW = 'ROLE_CAN_VIEW';
    case ROLE_CAN_CREATE = 'ROLE_CAN_CREATE';
    case ROLE_CAN_UPDATE = 'ROLE_CAN_UPDATE';
    case ROLE_CAN_DELETE= 'ROLE_CAN_DELETE';
    case ROLE_CAN_ASSIGN = 'ROLE_CAN_ASSIGN';
}
