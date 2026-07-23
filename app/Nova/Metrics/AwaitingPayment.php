<?php

namespace App\Nova\Metrics;

use App\Models\Order;
use App\Nova\Metrics\Concerns\ScopesToUserStores;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Metrics\Value;

class AwaitingPayment extends Value
{
    use ScopesToUserStores;

    public $name = 'Awaiting Payment';

    public function calculate($request)
    {
        return $this->sum($request, $this->orders($request), 'total_sales')
            ->prefix('$');
    }

    /** Store-scoped orders still awaiting payment. */
    protected function orders($request): Builder
    {
        return $this->storeScopedOrders($request)
            ->where('payment_status', Order::PAYMENT_STATUS_AWAITING_PAYMENT);
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
        return 'awaiting-payment';
    }
}
