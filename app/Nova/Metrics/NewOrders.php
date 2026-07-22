<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Laravel\Nova\Metrics\Value;

class NewOrders extends Value
{
    use ScopesToUserStores;

    public $name = 'New Orders';

    public function calculate($request)
    {
        return $this->count($request, $this->storeScopedOrders($request));
    }

    public function ranges()
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
            'MTD' => 'This Month',
            'QTD' => 'This Quarter',
            'YTD' => 'This Year',
        ];
    }

    public function uriKey()
    {
        return 'new-orders';
    }
}
