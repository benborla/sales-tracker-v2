<?php

namespace App\Permissions;

use App\Permissions\Traits\EnumEnhancements;

enum ProductImage: string
{
    use EnumEnhancements;

    case PRODUCTIMAGE_CAN_VIEW = 'PRODUCTIMAGE_CAN_VIEW';
    case PRODUCTIMAGE_CAN_ADD = 'PRODUCTIMAGE_CAN_ADD';
    case PRODUCTIMAGE_CAN_UPDATE = 'PRODUCTIMAGE_CAN_UPDATE';
    case PRODUTIMAGE_CAN_DELETE = 'PRODUTIMAGE_CAN_DELETE';
}
