<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Laravel\Nova\Metrics\Trend;

class SalesTrend extends Trend
{
    use ScopesToUserStores;

    public $name = 'Sales Trend';

    public function calculate($request)
    {
        return $this->sumByDays($request, $this->storeScopedOrders($request), 'total_sales')
            ->prefix('$');
    }

    public function ranges()
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }

    public function uriKey()
    {
        return 'sales-trend';
    }
}
