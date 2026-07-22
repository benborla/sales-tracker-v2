<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Laravel\Nova\Metrics\Partition;

class OrdersByChannel extends Partition
{
    use ScopesToUserStores;

    public $name = 'Orders by Channel';

    public function calculate($request)
    {
        return $this->count($request, $this->storeScopedOrders($request), 'sales_channel');
    }

    public function uriKey()
    {
        return 'orders-by-channel';
    }
}
