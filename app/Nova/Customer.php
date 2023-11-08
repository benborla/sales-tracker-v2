<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\AbstractUserBase;
use App\Models\UserInformation;

class Customer extends AbstractUserBase
{
    public static $type = UserInformation::USER_TYPE_CUSTOMER;
}
