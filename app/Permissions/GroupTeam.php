<?php
namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum GroupTeam: string
{
    use EnumEnhancements;

    case GROUPTEAM_CAN_VIEW = 'GROUPTEAM_CAN_VIEW';
    case GROUPTEAM_CAN_CREATE = 'GROUPTEAM_CAN_CREATE';
    case GROUPTEAM_CAN_UPDATE = 'GROUPTEAM_CAN_UPDATE';
    case GROUPTEAM_CAN_DELETE= 'GROUPTEAM_CAN_DELETE';
    case GROUPTEAM_CAN_ASSIGN = 'GROUPTEAM_CAN_ASSIGN';
}
