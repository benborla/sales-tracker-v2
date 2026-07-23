<?php

namespace App\Nova\Metrics;

use App\Models\Order;
use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Metrics\Value;

class OrdersToShip extends Value
{
    use ScopesToUserStores;

    public $name = 'Orders to Ship';

    public function calculate($request)
    {
        return $this->count($request, $this->orders($request));
    }

    /** Store-scoped orders not yet fulfilled (open / in-flight). */
    protected function orders($request): Builder
    {
        return $this->storeScopedOrders($request)
            ->whereIn('order_status', [
                Order::ORDER_STATUS_NEW,
                Order::ORDER_STATUS_PROCESSING,
                Order::ORDER_STATUS_IN_TRANSIT,
            ]);
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
        return 'orders-to-ship';
    }
}
