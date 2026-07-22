<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Laravel\Nova\Metrics\Partition;

class OrdersByStatus extends Partition
{
    use ScopesToUserStores;

    public $name = 'Orders by Status';

    public function calculate($request)
    {
        return $this->count($request, $this->storeScopedOrders($request), 'order_status');
    }

    public function uriKey()
    {
        return 'orders-by-status';
    }
}
