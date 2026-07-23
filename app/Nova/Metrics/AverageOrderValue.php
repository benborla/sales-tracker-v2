<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Laravel\Nova\Metrics\Value;

class AverageOrderValue extends Value
{
    use ScopesToUserStores;

    public $name = 'Avg Order Value';

    public function calculate($request)
    {
        return $this->average($request, $this->storeScopedOrders($request), 'total_sales')
            ->prefix('$');
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
        return 'average-order-value';
    }
}
