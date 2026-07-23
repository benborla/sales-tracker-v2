<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Laravel\Nova\Metrics\Partition;

class OrdersByPaymentStatus extends Partition
{
    use ScopesToUserStores;

    public $name = 'Orders by Payment Status';

    public function calculate($request)
    {
        return $this->count($request, $this->storeScopedOrders($request), 'payment_status');
    }

    public function uriKey()
    {
        return 'orders-by-payment-status';
    }
}
