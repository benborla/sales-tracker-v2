<?php

namespace App\Nova\Filters;

use App\Models\Order;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class FilterByPaymentStatus extends BooleanFilter
{
    /**
     * @var string
     */
    public $name = 'Payment Status';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if ($value) {
            return $query->where('payment_status', $value);
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Awaiting Payment' => Order::PAYMENT_STATUS_AWAITING_PAYMENT,
            'Success' => Order::PAYMENT_STATUS_SUCCESS,
            'Payment Failed' => Order::PAYMENT_STATUS_FAILED,
            'Payment Refund' => Order::PAYMENT_STATUS_REFUND,
        ];
    }
}
