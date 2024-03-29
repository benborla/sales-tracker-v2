<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;

class OrderStatus extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            //
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'order-status-new';
    }
}
